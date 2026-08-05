<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class SavedProjectController extends Controller
{
    public function index(Request $request): View
    {
        $savedProjects = $request->user()->savedProjects()
            ->with(['categories', 'country', 'city', 'client'])
            ->latest('saved_projects.created_at')
            ->get();

        return view('saved-projects.index', ['savedProjects' => $savedProjects]);
    }
}
