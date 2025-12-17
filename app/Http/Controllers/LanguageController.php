<?php

namespace App\Http\Controllers;

use App\Http\Middleware\SetLocale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    /**
     * Get available locales
     */
    public function getLocales()
    {
        return response()->json([
            'current' => App::getLocale(),
            'locales' => SetLocale::getSupportedLocales(),
            'isRtl' => SetLocale::isRtl(),
        ]);
    }

    /**
     * Switch the application language
     */
    public function switch(Request $request, string $locale)
    {
        $locales = SetLocale::getSupportedLocales();

        // Validate locale
        if (!array_key_exists($locale, $locales)) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Invalid locale'], 400);
            }
            return back()->with('error', 'Langue non supportée');
        }

        // Update user preference if authenticated
        if (Auth::check()) {
            Auth::user()->update(['locale' => $locale]);
        }

        // Store in session
        Session::put('locale', $locale);

        // Set application locale
        App::setLocale($locale);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'locale' => $locale,
                'isRtl' => SetLocale::isRtl($locale),
            ]);
        }

        return back()->with('success', __('common.language') . ' ' . __('common.updated_successfully'));
    }
}
