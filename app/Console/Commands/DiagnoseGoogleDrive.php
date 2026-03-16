<?php

namespace App\Console\Commands;

use Google\Client as GoogleClient;
use Google\Service\Drive;
use Google\Service\Exception as GoogleServiceException;
use Illuminate\Console\Command;

class DiagnoseGoogleDrive extends Command
{
    protected $signature = 'backup:diagnose-drive';

    protected $description = 'Diagnose Google Drive OAuth2 connection and folder accessibility';

    public function handle(): int
    {
        $this->info('=== Google Drive Diagnostic Tool ===');
        $this->newLine();

        // 1. Check all required .env values
        $clientId     = config('services.google_drive.client_id');
        $clientSecret = config('services.google_drive.client_secret');
        $refreshToken = config('services.google_drive.refresh_token');
        $folderId     = config('services.google_drive.folder_id');

        $missing = array_keys(array_filter([
            'GOOGLE_DRIVE_CLIENT_ID'     => blank($clientId),
            'GOOGLE_DRIVE_CLIENT_SECRET' => blank($clientSecret),
            'GOOGLE_DRIVE_REFRESH_TOKEN' => blank($refreshToken),
            'GOOGLE_DRIVE_FOLDER_ID'     => blank($folderId),
        ]));

        if ($missing) {
            foreach ($missing as $key) {
                $this->error("Missing in .env: {$key}");
            }
            if (in_array('GOOGLE_DRIVE_REFRESH_TOKEN', $missing)) {
                $this->warn('Run: php artisan backup:google-oauth-setup');
            }
            return self::FAILURE;
        }

        $this->line('<info>✔</info> All .env credentials are set.');

        // 2. Build Google client with OAuth2 refresh token
        try {
            $client = new GoogleClient();
            $client->setClientId($clientId);
            $client->setClientSecret($clientSecret);
            $client->setScopes([Drive::DRIVE_FILE]);
            $client->setAccessType('offline');
            $client->fetchAccessTokenWithRefreshToken($refreshToken);

            if ($client->isAccessTokenExpired()) {
                $this->error('Access token could not be refreshed. Re-run: php artisan backup:google-oauth-setup');
                return self::FAILURE;
            }

            $drive = new Drive($client);
            $this->line('<info>✔</info> OAuth2 token refreshed successfully.');
        } catch (\Throwable $e) {
            $this->error('Failed to build Google client: '.$e->getMessage());
            return self::FAILURE;
        }

        $this->newLine();

        // 3. Check the configured folder
        $this->info('--- Checking Folder ID: '.$folderId.' ---');
        try {
            $folder = $drive->files->get($folderId, [
                'fields'           => 'id,name,mimeType',
                'supportsAllDrives' => true,
            ]);
            $this->line('<info>✔</info> Folder found: '.$folder->getName());
            $this->line('   MIME type: '.$folder->getMimeType());
        } catch (GoogleServiceException $e) {
            // With DRIVE_FILE scope, folder metadata may return 404 even when upload is still allowed.
            // Do not fail early; continue with a real upload test to verify effective access.
            $this->warn('Could not read folder metadata directly: '.$e->getMessage());
            $this->warn('Continuing to upload test, which is the final access check.');
        }

        $this->newLine();

        // 4. Test upload
        $this->info('--- Testing upload to folder ---');
        try {
            $testFile = $drive->files->create(
                new \Google\Service\Drive\DriveFile([
                    'name'    => 'upload-test-'.now()->format('YmdHis').'.txt',
                    'parents' => [$folderId],
                ]),
                [
                    'data'              => 'upload test '.now()->toDateTimeString(),
                    'mimeType'          => 'text/plain',
                    'uploadType'        => 'multipart',
                    'fields'            => 'id,name',
                    'supportsAllDrives' => true,
                ]
            );

            $this->line('<info>✔</info> Test file uploaded! File ID: '.$testFile->getId());
            $drive->files->delete($testFile->getId(), ['supportsAllDrives' => true]);
            $this->line('   (Test file deleted)');
            $this->newLine();
            $this->info('✔ Everything looks good! Run: php artisan backup:database-to-google-drive');
        } catch (GoogleServiceException $e) {
            $this->error('Upload test FAILED: '.$e->getMessage());
            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
