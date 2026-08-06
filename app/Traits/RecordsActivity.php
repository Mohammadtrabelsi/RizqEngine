<?php

namespace App\Traits;

use Illuminate\Support\Str;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

/**
 * Drop this trait onto any Eloquent model to automatically record an
 * activity log entry whenever the model is created, updated or deleted.
 *
 * It ships with sensible defaults for this application:
 *  - every attribute is tracked, except noisy/sensitive ones;
 *  - only the attributes that actually changed are stored;
 *  - "empty" updates (no real change) are not logged;
 *  - each model gets a human friendly log name (used as the "Module"
 *    column on the activity log screen).
 */
trait RecordsActivity
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logExcept([
                'password',
                'remember_token',
                'created_at',
                'updated_at',
            ])
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->useLogName(Str::headline(class_basename($this)))
            ->setDescriptionForEvent(
                fn (string $eventName) => Str::headline(class_basename($this))." was {$eventName}"
            );
    }
}
