<?php

namespace App\Livewire;

use App\Http\Middleware\SetLocale;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class LanguageSwitcher extends Component
{
    public string $currentLocale;
    public array $locales = [];

    public function mount()
    {
        $this->currentLocale = App::getLocale();
        $this->locales = SetLocale::getSupportedLocales();
    }

    public function switchLocale(string $locale)
    {
        // Validate locale
        if (!array_key_exists($locale, $this->locales)) {
            return;
        }

        // Update user preference if authenticated
        if (Auth::check()) {
            Auth::user()->update(['locale' => $locale]);
        }

        // Store in session
        Session::put('locale', $locale);

        // Set application locale
        App::setLocale($locale);
        $this->currentLocale = $locale;

        // Refresh the page to apply changes
        return $this->redirect(request()->header('Referer', route('dashboard')), navigate: true);
    }

    public function isRtl(): bool
    {
        return SetLocale::isRtl($this->currentLocale);
    }

    public function getCurrentLocaleConfig(): array
    {
        return $this->locales[$this->currentLocale] ?? $this->locales['fr'];
    }

    public function render()
    {
        return view('livewire.language-switcher');
    }
}
