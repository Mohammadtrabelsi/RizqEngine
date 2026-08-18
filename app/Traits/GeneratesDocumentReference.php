<?php

namespace App\Traits;

/**
 * Generates a human-friendly, concurrency-safe document reference.
 *
 * The reference is derived from the row's own auto-increment primary key in the
 * `created` model event, so it is unique without any read-modify-write race
 * (no Model::count()+1 / max()+1). Each consuming model just declares its
 * prefix via {@see self::referencePrefix()} (e.g. "BC", "CMD").
 */
trait GeneratesDocumentReference
{
    public static function bootGeneratesDocumentReference(): void
    {
        static::created(function ($model) {
            if (empty($model->reference)) {
                $model->reference = make_reference_id($model->referencePrefix(), $model->getKey());
                $model->saveQuietly();
            }
        });
    }

    /**
     * The short prefix used to build the reference (without the trailing dash).
     */
    abstract public function referencePrefix(): string;
}
