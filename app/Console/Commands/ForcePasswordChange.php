<?php
/*
Dev Omar Shaheen
Devomar095@gmail.com
WhatsApp +972592554320
*/

namespace App\Console\Commands;

use App\Models\Inspection;
use App\Models\Manager;
use App\Models\School;
use Illuminate\Console\Command;

/**
 * Raises the forced password change flag in bulk, so the policy can be rolled
 * out over accounts that already exist.
 */
class ForcePasswordChange extends Command
{
    protected $signature = 'password:force-change
                            {guard : manager, school, inspection or all}
                            {--id=* : limit to these ids}
                            {--stale= : only accounts whose password was last changed more than N days ago (never changed counts as stale)}';

    protected $description = 'Force the selected accounts to change their password on the next sign in';

    private $models = [
        'manager' => Manager::class,
        'school' => School::class,
        'inspection' => Inspection::class,
    ];

    public function handle()
    {
        $guard = $this->argument('guard');
        $guards = $guard === 'all' ? array_keys($this->models) : [$guard];

        foreach ($guards as $name) {
            if (!isset($this->models[$name])) {
                $this->error("Unknown guard [$name]. Use manager, school, inspection or all.");
                return 1;
            }
        }

        foreach ($guards as $name) {
            $query = $this->models[$name]::query();

            if ($ids = array_filter((array) $this->option('id'))) {
                $query->whereIn('id', $ids);
            }

            if ($days = $this->option('stale')) {
                $query->where(function ($query) use ($days) {
                    $query->whereNull('password_changed_at')
                        ->orWhere('password_changed_at', '<', now()->subDays((int) $days));
                });
            }

            $count = $query->count();

            if (!$this->confirm("Force a password change on $count $name account(s)?", true)) {
                $this->line("Skipped $name.");
                continue;
            }

            $query->update(['force_password_change' => 1]);
            $this->info("$name: $count account(s) flagged.");
        }

        return 0;
    }
}
