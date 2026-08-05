<?php

namespace App\Http\Controllers;

use App\Support\LocaleOptions;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Validation\Rule;

class LocaleController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'option' => ['required', Rule::in(array_keys(LocaleOptions::OPTIONS))],
        ]);

        $option = $validated['option'];
        $locale = LocaleOptions::OPTIONS[$option]['locale'];

        Cookie::queue('locale', $locale, 60 * 24 * 365);
        Cookie::queue('locale_option', $option, 60 * 24 * 365);

        if ($request->user()) {
            $request->user()->update(['locale' => $locale]);
        }

        return back();
    }
}
