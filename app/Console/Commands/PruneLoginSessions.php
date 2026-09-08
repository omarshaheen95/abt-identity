<?php

namespace App\Console\Commands;

use App\Models\LoginSession;
use Illuminate\Console\Command;

class PruneLoginSessions extends Command
{
    protected $signature = 'login-sessions:prune
                            {--days=365 : Retention period in days}
                            {--dry-run : Show what would be deleted without actually deleting}';

    protected $description = 'Delete login session records (successful and failed attempts) older than the retention period';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $days = (int) $this->option('days');

        if ($days <= 0) {
            $this->error("Retention period must be greater than 0. Got: {$days}");

            return 1;
        }

        $cutoff = now()->subDays($days);

        $this->info("Pruning login sessions older than {$days} day(s) (before {$cutoff->toDateTimeString()}).");

        if ($dryRun) {
            $this->warn('[DRY RUN] No records will be deleted.');
        }

        $query = LoginSession::query()->where('created_at', '<', $cutoff);

        $total = $query->count();

        if ($total === 0) {
            $this->info('No login sessions found matching the criteria.');

            return 0;
        }

        $this->info("Found {$total} record(s) to delete.");

        if ($dryRun) {
            return 0;
        }

        // Deleted in batches so a large backlog does not lock the table.
        $deleted = 0;
        do {
            $affected = LoginSession::query()->where('created_at', '<', $cutoff)->limit(2000)->delete();
            $deleted += $affected;
        } while ($affected > 0);

        $this->info("Deleted {$deleted} record(s).");

        return 0;
    }
}
