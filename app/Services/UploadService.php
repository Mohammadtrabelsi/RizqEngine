<?php

namespace App\Services;

use App\Models\Upload;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\HasMedia;

/**
 * Owns the FilePond/Dropzone temporary-upload workflow and the promotion of a
 * staged upload into a model's media collection, keeping Storage and the
 * Upload model out of controllers.
 */
class UploadService
{
    /**
     * Stage a FilePond upload under a unique temp folder and record it.
     *
     * @return string The folder token returned to the client.
     */
    public function stageFilepond(UploadedFile $file): string
    {
        $filename = now()->timestamp.'.'.$file->getClientOriginalExtension();
        $folder = uniqid().'-'.now()->timestamp;

        Storage::putFileAs('temp/'.$folder, $file, $filename);

        Upload::create([
            'folder' => $folder,
            'filename' => $filename,
        ]);

        return $folder;
    }

    /**
     * Remove a staged FilePond upload by its folder token.
     */
    public function removeFilepond(string $folder): void
    {
        $upload = Upload::where('folder', $folder)->first();

        if (! $upload) {
            return;
        }

        Storage::deleteDirectory('temp/'.$upload->folder);
        $upload->delete();
    }

    /**
     * Stage a Dropzone upload and return its stored + original names.
     *
     * @return array{name: string, original_name: string}
     */
    public function stageDropzone(UploadedFile $file): array
    {
        $filename = now()->timestamp.'.'.trim($file->getClientOriginalExtension());

        Storage::putFileAs('temp/dropzone/', $file, $filename);

        return [
            'name' => $filename,
            'original_name' => $file->getClientOriginalName(),
        ];
    }

    public function removeDropzone(string $filename): void
    {
        Storage::delete('temp/dropzone/'.$filename);
    }

    /**
     * Promote a staged temp upload (identified by its folder token) into the
     * given media collection, replacing any existing single media item.
     *
     * @param  string  $basePath  Storage path prefix the temp folder lives under.
     */
    public function promoteToMedia(HasMedia $model, ?string $folder, string $collection, string $basePath = 'public/temp/'): void
    {
        if (! $folder) {
            return;
        }

        $tempFile = Upload::where('folder', $folder)->first();

        if ($model->getFirstMedia($collection)) {
            $model->getFirstMedia($collection)->delete();
        }

        if (! $tempFile) {
            return;
        }

        $model->addMedia(Storage::path($basePath.$folder.'/'.$tempFile->filename))
            ->toMediaCollection($collection);

        Storage::deleteDirectory($basePath.$folder);
        $tempFile->delete();
    }
}
