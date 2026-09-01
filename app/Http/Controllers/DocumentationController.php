<?php

namespace App\Http\Controllers;

class DocumentationController extends Controller
{
    public function index()
    {
        $sections = array_keys(trans('documentation.sections'));

        // Split the sections into two balanced columns for the layout.
        $sectionChunks = collect($sections)->chunk((int) ceil(count($sections) / 2));

        return view('documentation.index', compact('sections', 'sectionChunks'));
    }
}
