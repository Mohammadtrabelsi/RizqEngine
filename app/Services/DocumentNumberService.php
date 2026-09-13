<?php

namespace App\Services;

use App\Models\DocumentSequence;
use Illuminate\Support\Facades\DB;

/**
 * Allocates legal, uninterrupted document numbers.
 *
 * Tunisian tax rules require sales invoices to carry a continuous,
 * non-reusable sequence. This service is the single place that hands out the
 * next number for a given document family: it takes a row lock on the counter,
 * reads and increments it atomically, so two concurrent invoices can never
 * receive the same number and a deleted invoice's number is never re-issued.
 *
 * The formatted reference keeps the app's existing shape (e.g. "SL-00007") via
 * {@see make_reference_id()} so views, exports and historical data are
 * unaffected.
 */
class DocumentNumberService
{
    /**
     * Allocate and format the next reference for the given sequence key.
     *
     * Must be called inside a database transaction (all document creation
     * already runs in one) so the row lock is held until the surrounding write
     * commits.
     *
     * @param  string  $key  Sequence identifier, e.g. "sale".
     * @param  string  $fallbackPrefix  Prefix used when the counter row does
     *                                  not exist yet (first ever allocation).
     */
    public function next(string $key, string $fallbackPrefix): string
    {
        $sequence = DocumentSequence::where('key', $key)->lockForUpdate()->first();

        if ($sequence === null) {
            // First allocation for this key: create the counter, then re-read
            // it under a lock so the increment below is serialized like any
            // other allocation.
            DocumentSequence::firstOrCreate(
                ['key' => $key],
                ['prefix' => $fallbackPrefix, 'next_number' => 1],
            );

            $sequence = DocumentSequence::where('key', $key)->lockForUpdate()->first();
        }

        $number = $sequence->next_number;

        DB::table('document_sequences')
            ->where('id', $sequence->id)
            ->update(['next_number' => $number + 1, 'updated_at' => now()]);

        return make_reference_id($sequence->prefix, $number);
    }
}
