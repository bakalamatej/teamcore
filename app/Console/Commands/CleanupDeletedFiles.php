<?php

namespace App\Console\Commands;

use App\Models\File;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class CleanupDeletedFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'files:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Permanently delete soft-deleted files older than 7 days';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $days = 7;  
        $cutoffDate = Carbon::now()->subDays($days);

        $this->info("Cleaning up files deleted before {$cutoffDate->format('Y-m-d H:i:s')}...");

        $deletedFiles = File::onlyTrashed()
            ->where('deleted_at', '<', $cutoffDate)
            ->get();

        if ($deletedFiles->isEmpty()) {
            $this->info('No files to clean up.');
            return Command::SUCCESS;
        }

        $this->info("Found {$deletedFiles->count()} files to delete.");

        $progressBar = $this->output->createProgressBar($deletedFiles->count());
        $progressBar->start();

        $deletedCount = 0;
        $failedCount = 0;

        foreach ($deletedFiles as $file) {
            try {
                if ($file->file_path && Storage::exists($file->file_path)) {
                    Storage::delete($file->file_path);
                }

                $file->forceDelete();

                $deletedCount++;
            } catch (\Exception $e) {
                $this->error("Failed to delete file {$file->file_id}: {$e->getMessage()}");
                $failedCount++;
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine();

        $this->info("Cleanup complete!");
        $this->info("  Deleted: {$deletedCount} files");
        if ($failedCount > 0) {
            $this->warn("  Failed: {$failedCount} files");
        }

        return Command::SUCCESS;
    }
}