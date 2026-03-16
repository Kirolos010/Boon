<?php

namespace App\Console\Commands;

use App\Services\DatabaseBackupService;
use Illuminate\Console\Command;
use Throwable;

class BackupDatabaseToGoogleDrive extends Command
{
    /**
     * The console command name.
     *
     * This is the command you can call manually or from the scheduler.
     */
    protected $signature = 'backup:database-to-google-drive';

    /**
     * The console command description.
     */
    protected $description = 'Create a database SQL backup and upload it to Google Drive using a service account';

    /**
     * Execute the console command.
     */
    public function handle(DatabaseBackupService $databaseBackupService): int
    {
        // Step 1: Announce the beginning of the backup process.
        $this->info('Starting database backup...');

        try {
            // Step 2: Create the local SQL file and upload it to Google Drive.
            $backup = $databaseBackupService->createAndUploadBackup();

            // Step 3: Print a success message that includes the Google Drive file ID.
            $this->info('Backup completed successfully.');
            $this->line('Local file: '.$backup['local_path']);
            $this->line('Google Drive file ID: '.$backup['google_file_id']);

            return self::SUCCESS;
        } catch (Throwable $exception) {
            // Step 4: Print a readable error message so scheduled runs are easy to debug.
            $this->error('Backup failed: '.$exception->getMessage());

            return self::FAILURE;
        }
    }
}
