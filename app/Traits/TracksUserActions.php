<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Drop this trait onto any Eloquent model to automatically stamp the
 * authenticated user onto its audit columns:
 *
 *  - `created_by` is set once, when the record is first created;
 *  - `updated_by` is refreshed on every create and update.
 *
 * The `created_at` / `updated_at` timestamps are handled natively by
 * Eloquent, so together these give a full "who and when" audit trail.
 *
 * Values that are already set explicitly (e.g. during seeding or imports)
 * are preserved rather than overwritten, and nothing is stamped when there
 * is no authenticated user (console commands, queued jobs, etc.).
 *
 * @property int|null $created_by
 * @property int|null $updated_by
 */
trait TracksUserActions
{
    public static function bootTracksUserActions(): void
    {
        static::creating(function (self $model): void {
            $userId = auth()->id();

            if ($userId === null) {
                return;
            }

            if (empty($model->created_by)) {
                $model->created_by = $userId;
            }

            if (empty($model->updated_by)) {
                $model->updated_by = $userId;
            }
        });

        static::updating(function (self $model): void {
            $userId = auth()->id();

            if ($userId !== null) {
                $model->updated_by = $userId;
            }
        });
    }

    /**
     * The user who created the record.
     *
     * @return BelongsTo<User, $this>
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * The user who last updated the record.
     *
     * @return BelongsTo<User, $this>
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
