<?php

namespace App\Helpers;

use App\Http\Middleware\SetLocale;
use Illuminate\Support\Facades\App;

class I18nHelper
{
    /**
     * Get all supported locales
     */
    public static function getLocales(): array
    {
        return SetLocale::getSupportedLocales();
    }

    /**
     * Get current locale
     */
    public static function getCurrentLocale(): string
    {
        return App::getLocale();
    }

    /**
     * Get current locale configuration
     */
    public static function getCurrentLocaleConfig(): array
    {
        return SetLocale::getCurrentLocaleConfig();
    }

    /**
     * Check if current locale is RTL
     */
    public static function isRtl(): bool
    {
        return SetLocale::isRtl();
    }

    /**
     * Get direction for current locale
     */
    public static function getDirection(): string
    {
        return self::isRtl() ? 'rtl' : 'ltr';
    }

    /**
     * Get text alignment for current locale
     */
    public static function getTextAlign(): string
    {
        return self::isRtl() ? 'right' : 'left';
    }

    /**
     * Get locale flag emoji
     */
    public static function getFlag(string $locale = null): string
    {
        $locale = $locale ?? self::getCurrentLocale();
        $locales = self::getLocales();
        return $locales[$locale]['flag'] ?? '🌐';
    }

    /**
     * Get locale native name
     */
    public static function getNativeName(string $locale = null): string
    {
        $locale = $locale ?? self::getCurrentLocale();
        $locales = self::getLocales();
        return $locales[$locale]['native'] ?? $locale;
    }

    /**
     * Format number according to locale
     */
    public static function formatNumber(float $number, int $decimals = 2): string
    {
        $locale = self::getCurrentLocale();
        
        $separators = [
            'fr' => ['decimal' => ',', 'thousands' => ' '],
            'en' => ['decimal' => '.', 'thousands' => ','],
            'ar' => ['decimal' => '٫', 'thousands' => '٬'],
        ];

        $sep = $separators[$locale] ?? $separators['en'];
        
        return number_format($number, $decimals, $sep['decimal'], $sep['thousands']);
    }

    /**
     * Format currency according to locale
     */
    public static function formatCurrency(float $amount, string $currency = 'MAD'): string
    {
        $formattedNumber = self::formatNumber($amount, 2);
        $locale = self::getCurrentLocale();

        $currencySymbols = [
            'MAD' => ['fr' => 'DH', 'en' => 'MAD', 'ar' => 'درهم'],
            'EUR' => ['fr' => '€', 'en' => '€', 'ar' => '€'],
            'USD' => ['fr' => '$', 'en' => '$', 'ar' => '$'],
        ];

        $symbol = $currencySymbols[$currency][$locale] ?? $currency;

        if ($locale === 'ar') {
            return $formattedNumber . ' ' . $symbol;
        }

        return $formattedNumber . ' ' . $symbol;
    }

    /**
     * Get greeting based on time of day
     */
    public static function getGreeting(): string
    {
        $hour = (int) date('H');
        $locale = self::getCurrentLocale();

        $greetings = [
            'fr' => [
                'morning' => 'Bonjour',
                'afternoon' => 'Bon après-midi',
                'evening' => 'Bonsoir',
            ],
            'en' => [
                'morning' => 'Good morning',
                'afternoon' => 'Good afternoon',
                'evening' => 'Good evening',
            ],
            'ar' => [
                'morning' => 'صباح الخير',
                'afternoon' => 'مساء الخير',
                'evening' => 'مساء الخير',
            ],
        ];

        $period = $hour < 12 ? 'morning' : ($hour < 18 ? 'afternoon' : 'evening');

        return $greetings[$locale][$period] ?? $greetings['en'][$period];
    }
}
