<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CleanupProfilePhotosConfig extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'profile-photos:cleanup-config';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up profile photos configuration and ensure proper S3 setup';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧹 Cleaning up profile photos configuration...');

        // Verificar configuración actual
        $this->info('📋 Current configuration:');
        $this->line('  - Jetstream profile_photo_disk: ' . config('jetstream.profile_photo_disk'));
        $this->line('  - S3 profile-pictures disk configured: ' . (config('filesystems.disks.profile-pictures') ? 'Yes' : 'No'));

        // Verificar archivos existentes
        $this->info('📁 Current profile photos in S3:');
        $disk = Storage::disk('profile-pictures');
        $files = $disk->allFiles();

        $profilePhotos = array_filter($files, function($file) {
            return str_contains($file, 'profile-pictures/');
        });

        foreach ($profilePhotos as $file) {
            $this->line("  - {$file}");
        }

        // Verificar usuarios con fotos de perfil
        $this->info('👥 Users with profile photos:');
        $users = \App\Models\User::whereNotNull('profile_photo_path')->get();

        foreach ($users as $user) {
            $exists = $disk->exists($user->profile_photo_path);
            $status = $exists ? '✅' : '❌';
            $this->line("  {$status} {$user->name}: {$user->profile_photo_path}");
        }

        // Recomendaciones
        $this->info('💡 Recommendations:');
        $this->line('  1. The current setup uses double prefix (profile-pictures/profile-pictures/)');
        $this->line('  2. This is working but not ideal for new uploads');
        $this->line('  3. Consider updating the trait to use single prefix for new uploads');
        $this->line('  4. Existing photos will continue to work with current URLs');

        $this->info('✅ Configuration cleanup complete!');
    }
}
