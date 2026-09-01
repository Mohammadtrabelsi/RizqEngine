<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * Owns access to the singleton application settings record, including the
 * default product/category images stored on the public disk.
 */
class SettingService
{
    /**
     * The (single) settings record.
     */
    public function current(): Setting
    {
        return Setting::firstOrFail();
    }

    /**
     * Update the settings record. Any provided image files replace the stored
     * ones (deleting the previous file first).
     *
     * @param  array<string, mixed>  $data  Scalar settings columns.
     * @param  array<string, UploadedFile|null>  $images  Keyed by column name.
     */
    public function update(array $data, array $images = []): Setting
    {
        $settings = $this->current();

        foreach ($images as $column => $file) {
            if (! $file instanceof UploadedFile) {
                continue;
            }

            if ($settings->{$column}) {
                Storage::disk('public')->delete($settings->{$column});
            }

            $data[$column] = $file->store('settings', 'public');
        }

        $settings->update($data);

        cache()->forget('settings');

        return $settings;
    }
}
