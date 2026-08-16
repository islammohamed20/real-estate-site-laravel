<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class LocalizationController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $locale = $request->input('locale');

        if (! in_array($locale, ['en', 'ar'], true)) {
            $locale = config('app.locale');
        }

        $request->session()->put('locale', $locale);
        $request->session()->save();

        return back()
            ->withCookie(Cookie::make('locale', $locale, 60 * 24 * 30));
    }
}
