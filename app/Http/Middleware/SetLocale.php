<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Supported locales with their configurations
     */
    public const SUPPORTED_LOCALES = [
        'fr' => [
            'name' => 'Français',
            'native' => 'Français',
            'code' => 'fr',
            'flag' => '🇫🇷',
            'direction' => 'ltr',
        ],
        'en' => [
            'name' => 'English',
            'native' => 'English',
            'code' => 'en',
            'flag' => '🇬🇧',
            'direction' => 'ltr',
        ],
        'ar' => [
            'name' => 'Arabic',
            'native' => 'العربية',
            'code' => 'ar',
            'flag' => '🇸🇦',
            'direction' => 'rtl',
        ],
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $this->determineLocale($request);
        
        // Validate the locale
        if (!array_key_exists($locale, self::SUPPORTED_LOCALES)) {
            $locale = config('app.locale', 'fr');
        }

        // Set the application locale
        App::setLocale($locale);
        
        // Store in session for guests
        Session::put('locale', $locale);

        return $next($request);
    }

    /**
     * Determine the locale based on priority:
     * 1. User preference (if authenticated)
     * 2. Session preference
     * 3. Query parameter
     * 4. Browser preference
     * 5. Default locale
     */
    protected function determineLocale(Request $request): string
    {
        // 1. Authenticated user preference
        if (Auth::check() && Auth::user()->locale) {
            return Auth::user()->locale;
        }

        // 2. Session preference
        if (Session::has('locale')) {
            return Session::get('locale');
        }

        // 3. Query parameter (for switching)
        if ($request->has('lang')) {
            $lang = $request->get('lang');
            if (array_key_exists($lang, self::SUPPORTED_LOCALES)) {
                return $lang;
            }
        }

        // 4. Browser preference
        $browserLocale = $request->getPreferredLanguage(array_keys(self::SUPPORTED_LOCALES));
        if ($browserLocale) {
            return $browserLocale;
        }

        // 5. Default locale
        return config('app.locale', 'fr');
    }

    /**
     * Get supported locales
     */
    public static function getSupportedLocales(): array
    {
        return self::SUPPORTED_LOCALES;
    }

    /**
     * Check if locale is RTL
     */
    public static function isRtl(string $locale = null): bool
    {
        $locale = $locale ?? App::getLocale();
        return (self::SUPPORTED_LOCALES[$locale]['direction'] ?? 'ltr') === 'rtl';
    }

    /**
     * Get current locale configuration
     */
    public static function getCurrentLocaleConfig(): array
    {
        $locale = App::getLocale();
        return self::SUPPORTED_LOCALES[$locale] ?? self::SUPPORTED_LOCALES['fr'];
    }
}
