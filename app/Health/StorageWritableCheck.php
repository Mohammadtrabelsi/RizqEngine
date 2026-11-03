<?php

namespace App\Health;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\Health\Checks\Check;
use Spatie\Health\Checks\Result;
use Throwable;

/**
 * Verifies the application can write to, read from and delete on the given
 * filesystem disks (uploads, PDFs, imports and backups all depend on it).
 */
class StorageWritableCheck extends Check
{
    /** @var array<int, string> */
    protected array $disks = ['local', 'public'];

    /**
     * @param  array<int, string>  $disks
     */
    public function disks(array $disks): self
    {
        $this->disks = $disks;

        return $this;
    }

    public function run(): Result
    {
        $failing = [];

        foreach ($this->disks as $disk) {
            $path = '.health-check-'.Str::random(16).'.tmp';

            try {
                $storage = Storage::disk($disk);
                $ok = $storage->put($path, 'ok') && $storage->get($path) === 'ok';
                $storage->delete($path);
            } catch (Throwable) {
                $ok = false;
            }

            if (! $ok) {
                $failing[] = $disk;
            }
        }

        $result = Result::make()->meta(['disks' => $this->disks, 'failing' => $failing]);

        return $failing === []
            ? $result->ok()->shortSummary('Writable')
            : $result->failed('Storage not writable: '.implode(', ', $failing))->shortSummary('Not writable');
    }
}
