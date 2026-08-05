<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class BrowseController extends Controller
{
    public function index(): View
    {
        return view('browse.index');
    }
}
