<?php
/*
Dev Omar Shaheen
Devomar095@gmail.com
WhatsApp +972592554320
*/

namespace App\Traits;

use App\Models\PasswordHistory;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Hash;

/**
 * Password ageing and reuse prevention for an authenticatable model.
 *
 * The model must declare the guard it belongs to:
 *
 *     public static $passwordGuard = 'admin';
 *
 * History is recorded from a model event, so every write path is covered:
 * the self service screen, the admin forms and the reset by email flow alike.
 */
trait ManagesPasswordLifetime
{
    public static function bootManagesPasswordLifetime()
    {
        static::created(function ($model) {
            $model->recordPasswordInHistory();
        });

        static::updated(function ($model) {
            if ($model->wasChanged('password')) {
                $model->recordPasswordInHistory();
            }
        });
    }

    public function password_histories(): MorphMany
    {
        return $this->morphMany(PasswordHistory::class, 'model');
    }

    /**
     * Number of days a password stays valid for this model's guard, or null
     * when expiry is switched off.
     *
     * Every other expiry helper derives from this one, so the master switch
     * and the per guard duration are both honoured from a single place.
     */
    public function passwordLifetimeDays()
    {
        if (!config('password_policy.expiry_enabled', true)) {
            return null;
        }

        $guard = static::$passwordGuard ?? null;
        $days = $guard ? config('password_policy.expiry_days.' . $guard) : null;

        return $days > 0 ? (int) $days : null;
    }

    /**
     * When the current password stops being accepted, or null if never.
     */
    public function passwordExpiresAt()
    {
        $days = $this->passwordLifetimeDays();

        if (!$days || !$this->password_changed_at) {
            return null;
        }

        return Carbon::parse($this->password_changed_at)->addDays($days);
    }

    public function passwordHasExpired()
    {
        $expires = $this->passwordExpiresAt();

        return $expires && $expires->isPast();
    }

    /**
     * Whole days left before expiry; null when expiry does not apply, and a
     * value of 0 means it runs out today.
     */
    public function passwordExpiresInDays()
    {
        $expires = $this->passwordExpiresAt();

        if (!$expires) {
            return null;
        }

        return max(0, Carbon::now()->startOfDay()->diffInDays($expires->copy()->startOfDay(), false));
    }

    /**
     * Whether the reminder banner should be shown yet.
     */
    public function passwordExpiryIsNear()
    {
        $warn = (int) config('password_policy.warn_days', 0);
        $days = $this->passwordExpiresInDays();

        return $warn > 0 && $days !== null && !$this->passwordHasExpired() && $days <= $warn;
    }

    /**
     * Whether this plain password matches one of the remembered hashes.
     */
    public function passwordWasUsedBefore($password)
    {
        $keep = (int) config('password_policy.history', 0);

        if ($keep < 1 || !is_string($password) || $password === '') {
            return false;
        }

        // the current password counts as the most recent entry even if history
        // has not caught up yet, e.g. on accounts created before this feature
        if ($this->password && Hash::check($password, $this->password)) {
            return true;
        }

        $hashes = $this->password_histories()
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->limit($keep)
            ->pluck('password');

        foreach ($hashes as $hash) {
            if (Hash::check($password, $hash)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Store the current hash and drop anything past the retention window.
     */
    public function recordPasswordInHistory()
    {
        $keep = (int) config('password_policy.history', 0);

        if ($keep < 1 || empty($this->password)) {
            return;
        }

        $this->password_histories()->create([
            'password' => $this->password,
            'created_at' => now(),
        ]);

        $this->prunePasswordHistory($keep);
    }

    /**
     * Keep only the newest $keep entries for this account.
     */
    protected function prunePasswordHistory($keep)
    {
        $stale = $this->password_histories()
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->pluck('id')
            ->slice($keep);

        if ($stale->isNotEmpty()) {
            PasswordHistory::query()->whereIn('id', $stale->all())->delete();
        }
    }
}
