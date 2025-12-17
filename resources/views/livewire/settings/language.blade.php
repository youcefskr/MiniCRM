<?php

use App\Http\Middleware\SetLocale;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component {
    public string $locale = 'fr';
    public array $locales = [];

    public function mount(): void
    {
        $this->locale = Auth::user()->locale ?? app()->getLocale();
        $this->locales = SetLocale::getSupportedLocales();
    }

    public function updateLocale(string $locale): void
    {
        if (!array_key_exists($locale, $this->locales)) {
            return;
        }

        Auth::user()->update(['locale' => $locale]);
        $this->locale = $locale;

        session()->put('locale', $locale);
        app()->setLocale($locale);

        $this->dispatch('language-updated');
        
        // Redirect to refresh the page with new locale
        $this->redirect(request()->header('Referer', route('language.edit')), navigate: true);
    }

    public function isRtl(): bool
    {
        return SetLocale::isRtl($this->locale);
    }
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('common.language')" :subheading="__('common.select_language')">
        <div class="space-y-6">
            <!-- Current Language Info -->
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-4">
                <div class="flex items-center gap-3">
                    <span class="text-3xl">{{ $locales[$locale]['flag'] ?? '🌐' }}</span>
                    <div>
                        <h4 class="font-semibold text-blue-800 dark:text-blue-200">{{ __('common.language') }}: {{ $locales[$locale]['native'] ?? $locale }}</h4>
                        <p class="text-sm text-blue-600 dark:text-blue-400">
                            @if($this->isRtl())
                                {{ __('common.arabic') }} - RTL (Right-to-Left)
                            @else
                                {{ $locales[$locale]['name'] ?? '' }} - LTR (Left-to-Right)
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- Language Selection -->
            <div class="grid gap-4">
                @foreach($locales as $code => $localeInfo)
                    <button 
                        wire:click="updateLocale('{{ $code }}')"
                        class="flex items-center gap-4 p-4 rounded-xl border-2 transition-all duration-200 {{ $locale === $code ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-zinc-200 dark:border-zinc-700 hover:border-zinc-300 dark:hover:border-zinc-600 bg-white dark:bg-zinc-800' }}"
                    >
                        <span class="text-3xl">{{ $localeInfo['flag'] }}</span>
                        <div class="flex-1 text-start">
                            <div class="font-semibold text-zinc-900 dark:text-zinc-100">{{ $localeInfo['native'] }}</div>
                            <div class="text-sm text-zinc-500">{{ $localeInfo['name'] }}</div>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-1 text-xs font-medium rounded {{ $localeInfo['direction'] === 'rtl' ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400' : 'bg-zinc-100 text-zinc-600 dark:bg-zinc-700 dark:text-zinc-400' }}">
                                {{ strtoupper($localeInfo['direction']) }}
                            </span>
                            @if($locale === $code)
                                <flux:icon.check-circle class="size-6 text-blue-500" />
                            @endif
                        </div>
                    </button>
                @endforeach
            </div>

            <!-- RTL Info -->
            <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl p-4">
                <h4 class="font-semibold text-amber-800 dark:text-amber-200 mb-2 flex items-center gap-2">
                    <flux:icon.information-circle class="size-5" />
                    {{ __('common.info') }}
                </h4>
                <p class="text-sm text-amber-700 dark:text-amber-300">
                    @if(app()->getLocale() === 'fr')
                        La langue arabe utilise un affichage de droite à gauche (RTL). L'interface s'adaptera automatiquement lors du changement de langue.
                    @elseif(app()->getLocale() === 'ar')
                        اللغة العربية تستخدم عرض من اليمين إلى اليسار (RTL). ستتكيف الواجهة تلقائيًا عند تغيير اللغة.
                    @else
                        Arabic language uses right-to-left (RTL) display. The interface will automatically adapt when changing the language.
                    @endif
                </p>
            </div>
        </div>
    </x-settings.layout>
</section>
