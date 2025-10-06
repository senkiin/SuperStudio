<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class MigrateProfilePhotosToS3 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'profile-photos:migrate-to-s3 {--dry-run : Show what would be migrated without actually doing it}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate existing profile photos from local storage to S3 profile-pictures disk';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');

        if ($isDryRun) {
            $this->info('🔍 DRY RUN MODE - No files will be actually migrated');
        }

        $this->info('🚀 Starting profile photos migration to S3...');

        // Get all users with profile photos
        $users = User::whereNotNull('profile_photo_path')->get();

        if ($users->isEmpty()) {
            $this->info('✅ No users with profile photos found.');
            return;
        }

        $this->info("📊 Found {$users->count()} users with profile photos");

        $migrated = 0;
        $errors = 0;

        foreach ($users as $user) {
            $this->line("Processing user: {$user->name} (ID: {$user->id})");

            try {
                $oldPath = $user->profile_photo_path;

                // Check if file exists in local storage
                if (!Storage::disk('public')->exists($oldPath)) {
                    $this->warn("  ⚠️  File not found in local storage: {$oldPath}");
                    continue;
                }

                // Generate new path for S3
                $extension = pathinfo($oldPath, PATHINFO_EXTENSION);
                $newPath = "profile-pictures/{$user->id}/" . uniqid() . ".{$extension}";

                if ($isDryRun) {
                    $this->line("  📋 Would migrate: {$oldPath} → {$newPath}");
                } else {
                    // Get file content from local storage
                    $fileContent = Storage::disk('public')->get($oldPath);

                    // Upload to S3
                    Storage::disk('profile-pictures')->put($newPath, $fileContent);

                    // Update user record
                    $user->update(['profile_photo_path' => $newPath]);

                    // Delete from local storage
                    Storage::disk('public')->delete($oldPath);

                    $this->line("  ✅ Migrated: {$oldPath} → {$newPath}");
                }

                $migrated++;

            } catch (\Exception $e) {
                $this->error("  ❌ Error migrating user {$user->id}: " . $e->getMessage());
                $errors++;
            }
        }

        if ($isDryRun) {
            $this->info("🔍 DRY RUN COMPLETE - Would migrate {$migrated} photos");
        } else {
            $this->info("✅ Migration complete!");
            $this->info("📊 Successfully migrated: {$migrated} photos");
            if ($errors > 0) {
                $this->warn("⚠️  Errors encountered: {$errors}");
            }
        }
    }
}
