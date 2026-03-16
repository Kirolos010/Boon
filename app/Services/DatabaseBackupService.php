<?php

namespace App\Services;

use Google\Client as GoogleClient;
use Google\Service\Drive;
use Google\Service\Drive\DriveFile;
use Google\Service\Exception as GoogleServiceException;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use RuntimeException;
use Symfony\Component\Process\Process;
use Throwable;

// ---------------------------------------------------------------------------
// Authentication method: OAuth2 with Refresh Token
// ---------------------------------------------------------------------------
// A one-time setup provides a refresh token that is stored in .env.
// After that, no browser interaction is needed and it works exactly like a
// service account for scheduled / automated runs.
// Run once: php artisan backup:google-oauth-setup
// ---------------------------------------------------------------------------

class DatabaseBackupService
{
    /**
     * Create a database dump, save it locally, then upload it to Google Drive.
     */
    public function createAndUploadBackup(): array
    {
        // Step 1: Read the active database connection from Laravel config.
        $connectionName = config('database.default');
        $connection = config("database.connections.{$connectionName}");

        if (! is_array($connection)) {
            throw new RuntimeException('Database connection settings could not be loaded.');
        }

        // Step 2: Prepare a local storage path for the .sql backup file.
        $backupDirectory = storage_path('app/backups');
        File::ensureDirectoryExists($backupDirectory);

        $databaseName = $connection['database'] ?? $connectionName;
        $backupFileName = sprintf('%s-%s.sql', $databaseName, now()->format('Y-m-d_H-i-s'));
        $backupFilePath = $backupDirectory.DIRECTORY_SEPARATOR.$backupFileName;

        // Step 3: Export the database using the correct dump tool for MySQL or PostgreSQL.
        $this->dumpDatabase($connection, $backupFilePath);

        // Step 4: Build a Google Drive client using the service account JSON file.
        $drive = $this->makeDriveService();
        $folderId = config('services.google_drive.folder_id');

        // Step 5: Upload the generated SQL file into the target Google Drive folder.
        try {
            $uploadedFile = $drive->files->create(
                new DriveFile([
                    'name' => $backupFileName,
                    'parents' => [$folderId],
                ]),
                [
                    'data' => File::get($backupFilePath),
                    'mimeType' => 'application/sql',
                    'uploadType' => 'multipart',
                    'fields' => 'id,name',
                    // Required when the destination folder belongs to a Shared Drive.
                    'supportsAllDrives' => true,
                ]
            );
        } catch (GoogleServiceException $exception) {
            throw $this->transformGoogleDriveException($exception);
        }

        if (blank($uploadedFile->id ?? null)) {
            throw new RuntimeException('The backup file was uploaded, but Google Drive did not return a file ID.');
        }

        return [
            'local_path' => $backupFilePath,
            'file_name' => $backupFileName,
            'google_file_id' => $uploadedFile->id,
        ];
    }

    /**
     * Dump the database into the given file path.
     */
    protected function dumpDatabase(array $connection, string $backupFilePath): void
    {
        $driver = $connection['driver'] ?? null;
        $database = $connection['database'] ?? null;

        if (blank($driver) || blank($database)) {
            throw new RuntimeException('Database driver or database name is missing from the connection settings.');
        }

        $command = match ($driver) {
            // MySQL / MariaDB backup using mysqldump.
            'mysql' => [
            $this->resolveDumpBinaryPath('mysql'),
                '--host='.(string) ($connection['host'] ?? '127.0.0.1'),
                '--port='.(string) ($connection['port'] ?? 3306),
                '--user='.(string) ($connection['username'] ?? ''),
                '--password='.(string) ($connection['password'] ?? ''),
                '--default-character-set=utf8mb4',
                '--single-transaction',
                '--skip-lock-tables',
                (string) $database,
            ],

            // PostgreSQL backup using pg_dump.
            'pgsql' => [
                $this->resolveDumpBinaryPath('pgsql'),
                '--host='.(string) ($connection['host'] ?? '127.0.0.1'),
                '--port='.(string) ($connection['port'] ?? 5432),
                '--username='.(string) ($connection['username'] ?? ''),
                '--format=plain',
                '--no-owner',
                '--no-privileges',
                (string) $database,
            ],

            default => throw new RuntimeException("The [{$driver}] database driver is not supported for SQL backups."),
        };

        $environment = $driver === 'pgsql'
            ? array_merge($_ENV, ['PGPASSWORD' => (string) ($connection['password'] ?? '')])
            : null;

        $handle = fopen($backupFilePath, 'wb');

        if ($handle === false) {
            throw new RuntimeException('Unable to create the local SQL backup file.');
        }

        $errorOutput = '';

        try {
            $process = new Process($command, base_path(), $environment);
            $process->setTimeout(3600);

            // Stream stdout directly into the .sql file while collecting stderr for debugging.
            $process->run(function (string $type, string $buffer) use ($handle, &$errorOutput): void {
                if ($type === Process::OUT) {
                    fwrite($handle, $buffer);

                    return;
                }

                $errorOutput .= $buffer;
            });

            if (! $process->isSuccessful()) {
                throw new RuntimeException(trim($errorOutput ?: $process->getErrorOutput() ?: 'Database dump command failed.'));
            }
        } catch (Throwable $exception) {
            if (File::exists($backupFilePath)) {
                File::delete($backupFilePath);
            }

            throw new RuntimeException('Failed to generate the SQL backup. '.$exception->getMessage(), 0, $exception);
        } finally {
            fclose($handle);
        }

        if (! File::exists($backupFilePath) || (int) File::size($backupFilePath) === 0) {
            throw new RuntimeException('The SQL backup file was created, but it is empty.');
        }
    }

    /**
     * Resolve the full dump binary path for the active database driver.
     */
    protected function resolveDumpBinaryPath(string $driver): string
    {
        $binaryName = $driver === 'mysql' ? 'mysqldump' : 'pg_dump';
        $configuredPath = $driver === 'mysql'
            ? config('services.google_drive.mysqldump_path')
            : config('services.google_drive.pg_dump_path');

        if (filled($configuredPath) && File::exists($configuredPath)) {
            return $configuredPath;
        }

        foreach ($this->dumpBinaryCandidates($driver) as $candidate) {
            if (File::exists($candidate)) {
                return $candidate;
            }
        }

        // Final fallback: use the bare command name in case it exists in PATH.
        return $binaryName;
    }

    /**
     * Build candidate dump tool locations for common local environments.
     */
    protected function dumpBinaryCandidates(string $driver): array
    {
        $isWindows = PHP_OS_FAMILY === 'Windows';
        $binaryFile = $driver === 'mysql'
            ? ($isWindows ? 'mysqldump.exe' : 'mysqldump')
            : ($isWindows ? 'pg_dump.exe' : 'pg_dump');

        $patterns = $driver === 'mysql'
            ? [
                'C:/laragon/bin/mysql/*/bin/'.$binaryFile,
                'C:/xampp/mysql/bin/'.$binaryFile,
                'C:/Program Files/MySQL/*/bin/'.$binaryFile,
                'C:/Program Files/MariaDB */bin/'.$binaryFile,
            ]
            : [
                'C:/laragon/bin/postgresql/*/bin/'.$binaryFile,
                'C:/Program Files/PostgreSQL/*/bin/'.$binaryFile,
            ];

        $candidates = [];

        foreach ($patterns as $pattern) {
            $matches = glob($pattern) ?: [];

            if ($matches !== []) {
                usort($matches, static fn (string $first, string $second): int => version_compare(
                    static::extractVersionFromPath($second),
                    static::extractVersionFromPath($first)
                ));

                $candidates = [...$candidates, ...$matches];
            }
        }

        return array_values(array_unique($candidates));
    }

    /**
     * Extract a sortable version string from a binary path.
     */
    protected static function extractVersionFromPath(string $path): string
    {
        preg_match('/(\d+(?:\.\d+)+)/', Str::replace('\\', '/', $path), $matches);

        return $matches[1] ?? '0.0.0';
    }

    /**
     * Create a Google Drive service instance using OAuth2 refresh token.
     * No browser interaction required after initial setup.
     */
    protected function makeDriveService(): Drive
    {
        $clientId     = config('services.google_drive.client_id');
        $clientSecret = config('services.google_drive.client_secret');
        $refreshToken = config('services.google_drive.refresh_token');
        $folderId     = config('services.google_drive.folder_id');

        if (blank($clientId) || blank($clientSecret)) {
            throw new RuntimeException(
                'GOOGLE_DRIVE_CLIENT_ID or GOOGLE_DRIVE_CLIENT_SECRET is missing. '
                .'Create OAuth2 credentials at console.cloud.google.com.'
            );
        }

        if (blank($refreshToken)) {
            throw new RuntimeException(
                'GOOGLE_DRIVE_REFRESH_TOKEN is missing. '
                .'Run: php artisan backup:google-oauth-setup'
            );
        }

        if (blank($folderId)) {
            throw new RuntimeException('GOOGLE_DRIVE_FOLDER_ID is missing from the environment configuration.');
        }

        $client = new GoogleClient();
        $client->setApplicationName(config('app.name', 'Laravel').' Database Backup');
        $client->setClientId($clientId);
        $client->setClientSecret($clientSecret);
        $client->setScopes([Drive::DRIVE_FILE]);
        $client->setAccessType('offline');

        // Use the stored refresh token to obtain a fresh access token automatically.
        $client->fetchAccessTokenWithRefreshToken($refreshToken);

        if ($client->isAccessTokenExpired() && blank($client->getRefreshToken())) {
            throw new RuntimeException(
                'Could not refresh the Google Drive access token. '
                .'Re-run: php artisan backup:google-oauth-setup'
            );
        }

        return new Drive($client);
    }

    /**
     * Convert Google Drive API errors into actionable messages.
     */
    protected function transformGoogleDriveException(GoogleServiceException $exception): RuntimeException
    {
        $message = $exception->getMessage();

        if (str_contains($message, 'storageQuotaExceeded') || str_contains($message, 'Service Accounts do not have storage quota')) {
            return new RuntimeException(
                'Google Drive rejected the upload because Service Accounts do not have personal storage quota. '
                .'Create or use a Shared Drive folder, add the Service Account as a member of that Shared Drive, '
                .'then put that Shared Drive folder ID in GOOGLE_DRIVE_FOLDER_ID.',
                previous: $exception
            );
        }

        return new RuntimeException('Google Drive upload failed. '.$message, 0, $exception);
    }
}
