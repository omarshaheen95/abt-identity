<?php

namespace App\Console\Commands;

use App\Models\ProctorImage;
use App\Models\Setting;
use Illuminate\Console\Command;

class CleanProctorImages extends Command
{
    protected $signature = 'proctor:clean-images
                            {--days= : Override retention period in days (overrides settings value)}
                            {--dry-run : Show what would be deleted without actually deleting}';

    protected $description = 'Delete proctor images and their records older than the configured retention period';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');

        $days = $this->option('days');
        if ($days === null) {
            $setting = Setting::query()->where('key', 'proctor_images_retention_days')->first();
            $days = $setting ? (int) $setting->value : 90;
        } else {
            $days = (int) $days;
        }

        if ($days <= 0) {
            $this->error("Retention period must be greater than 0. Got: {$days}");
            return self::FAILURE;
        }

        $cutoff = now()->subDays($days);

        $this->info("Cleaning proctor images older than {$days} day(s) (before {$cutoff->toDateTimeString()}).");

        if ($dryRun) {
            $this->warn('[DRY RUN] No records will be deleted.');
        }

        $query = ProctorImage::query()->withTrashed()->where('created_at', '<', $cutoff);

        $total = $query->count();

        if ($total === 0) {
            $this->info('No proctor images found matching the criteria.');
            return self::SUCCESS;
        }

        $this->info("Found {$total} record(s) to delete.");

        if ($dryRun) {
            return self::SUCCESS;
        }

        $deleted     = 0;
        $filesDeleted = 0;
        $filesFailed  = 0;

        $query->chunkById(200, function ($images) use (&$deleted, &$filesDeleted, &$filesFailed) {
            foreach ($images as $image) {
                if ($image->file_path && file_exists(public_path($image->file_path))) {
                    if (@unlink(public_path($image->file_path))) {
                        $filesDeleted++;
                    } else {
                        $filesFailed++;
                        $this->warn("Could not delete file: {$image->file_path}");
                    }
                }
                $image->forceDelete();
                $deleted++;
            }
        });

        $this->info("Deleted {$deleted} record(s) and {$filesDeleted} file(s).");

        if ($filesFailed > 0) {
            $this->warn("{$filesFailed} file(s) could not be deleted from disk.");
        }

        return self::SUCCESS;
    }
}
