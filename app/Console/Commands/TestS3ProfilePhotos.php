<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class TestS3ProfilePhotos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'profile-photos:test-s3';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test S3 profile-pictures disk configuration and connectivity';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧪 Testing S3 profile-pictures disk configuration...');

        try {
            // Test disk configuration
            $disk = Storage::disk('profile-pictures');
            $this->info('✅ Disk configuration loaded successfully');

            // Test S3 connectivity
            $testContent = 'Test file for S3 connectivity - ' . now();
            $testPath = 'test-connectivity.txt';

            $this->info('📤 Uploading test file to S3...');
            $disk->put($testPath, $testContent);
            $this->info('✅ Test file uploaded successfully');

            // Test file retrieval
            $this->info('📥 Retrieving test file from S3...');
            $retrievedContent = $disk->get($testPath);

            if ($retrievedContent === $testContent) {
                $this->info('✅ Test file retrieved successfully');
            } else {
                $this->error('❌ Test file content mismatch');
                return 1;
            }

            // Test URL generation
            $this->info('🔗 Testing URL generation...');
            $url = $disk->url($testPath);
            $this->info("✅ Generated URL: {$url}");

            // Test file existence
            $this->info('🔍 Testing file existence check...');
            if ($disk->exists($testPath)) {
                $this->info('✅ File existence check works');
            } else {
                $this->error('❌ File existence check failed');
                return 1;
            }

            // Clean up test file
            $this->info('🧹 Cleaning up test file...');
            $disk->delete($testPath);
            $this->info('✅ Test file deleted successfully');

            // Test directory structure
            $this->info('📁 Testing directory structure...');
            $testDirPath = 'test-directory/';
            $disk->makeDirectory($testDirPath);
            $this->info('✅ Directory created successfully');

            $disk->deleteDirectory($testDirPath);
            $this->info('✅ Directory deleted successfully');

            $this->info('');
            $this->info('🎉 All S3 tests passed successfully!');
            $this->info('✅ Your profile-pictures disk is properly configured');
            $this->info('✅ S3 connectivity is working');
            $this->info('✅ File operations are working');
            $this->info('✅ URL generation is working');

            return 0;

        } catch (\Exception $e) {
            $this->error('❌ S3 test failed: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
            return 1;
        }
    }
}
