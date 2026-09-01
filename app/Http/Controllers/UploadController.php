<?php

namespace App\Http\Controllers;

use App\Services\UploadService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class UploadController extends Controller
{
    public function __construct(private readonly UploadService $uploads) {}

    public function filepondUpload(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:png,jpeg,jpg',
        ]);

        if ($request->hasFile('image')) {
            return $this->uploads->stageFilepond($request->file('image'));
        }

        return false;
    }

    public function filepondDelete(Request $request)
    {
        $this->uploads->removeFilepond($request->getContent());

        return response(null);
    }

    public function dropzoneUpload(Request $request)
    {
        return response()->json($this->uploads->stageDropzone($request->file('file')));
    }

    public function dropzoneDelete(Request $request)
    {
        $this->uploads->removeDropzone($request->file_name);

        return response()->json($request->file_name, 200);
    }
}
