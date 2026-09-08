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
     * @param  array<int, string>  $remove  Image columns to clear (delete file + null the column).
     */
    public function update(array $data, array $images = [], array $remove = []): Setting
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

        // A removal is skipped when a replacement file was uploaded in the same request.
        foreach ($remove as $column) {
            if (array_key_exists($column, $data)) {
                continue;
            }

            if ($settings->{$column}) {
                Storage::disk('public')->delete($settings->{$column});
            }

            $data[$column] = null;
        }

        $settings->update($data);

        cache()->forget('settings');

        return $settings;
    }
}
