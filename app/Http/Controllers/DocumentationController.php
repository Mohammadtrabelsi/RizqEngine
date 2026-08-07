<?php

namespace App\Http\Controllers;

class DocumentationController extends Controller
{
    public function index()
    {
        $sections = array_keys(trans('documentation.sections'));

        return view('documentation.index', compact('sections'));
    }
}
