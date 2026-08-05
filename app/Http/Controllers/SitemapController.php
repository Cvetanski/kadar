<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class SitemapController extends Controller
{
    /**
     * Only the landing page is listed for now — /creators and /creators/{id}
     * still sit behind the 'auth' middleware, so listing them would just hand
     * Google a set of URLs that redirect to /login. Once those routes are made
     * public, add the category and creator-profile loops back here.
     */
    public function index(): Response
    {
        $urls = collect([
            [
                'loc' => route('welcome'),
                'changefreq' => 'daily',
                'priority' => '1.0',
            ],
        ]);

        return response()
            ->view('sitemap', ['urls' => $urls])
            ->header('Content-Type', 'application/xml');
    }
}
