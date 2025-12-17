<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ App\Http\Middleware\SetLocale::isRtl() ? 'rtl' : 'ltr' }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white antialiased dark:bg-linear-to-b dark:from-neutral-950 dark:to-neutral-900">
        <!-- Language Switcher for Auth Pages -->
        <div class="absolute top-4 {{ App\Http\Middleware\SetLocale::isRtl() ? 'left-4' : 'right-4' }}">
            @php
                $locales = App\Http\Middleware\SetLocale::getSupportedLocales();
                $currentLocale = app()->getLocale();
            @endphp
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" class="flex items-center gap-2 px-3 py-2 text-sm font-medium text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-lg transition-colors">
                    <span class="text-lg">{{ $locales[$currentLocale]['flag'] ?? '🌐' }}</span>
                    <span>{{ strtoupper($currentLocale) }}</span>
                    <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div x-show="open" @click.away="open = false" x-transition class="absolute {{ App\Http\Middleware\SetLocale::isRtl() ? 'left-0' : 'right-0' }} mt-2 w-48 bg-white dark:bg-zinc-800 rounded-lg shadow-lg border border-zinc-200 dark:border-zinc-700 py-1 z-50">
                    @foreach($locales as $code => $locale)
                        <a href="{{ request()->fullUrlWithQuery(['lang' => $code]) }}" 
                           class="flex items-center gap-3 px-4 py-2 text-sm hover:bg-zinc-100 dark:hover:bg-zinc-700 {{ $currentLocale === $code ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400' : 'text-zinc-700 dark:text-zinc-300' }}">
                            <span class="text-lg">{{ $locale['flag'] }}</span>
                            <span class="flex-1">{{ $locale['native'] }}</span>
                            @if($currentLocale === $code)
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="bg-background flex min-h-svh flex-col items-center justify-center gap-6 p-6 md:p-10">
            <div class="flex w-full max-w-sm flex-col gap-2">
                <a href="{{ route('home') }}" class="flex flex-col items-center gap-2 font-medium" wire:navigate>
                    <span class="flex h-9 w-9 mb-1 items-center justify-center rounded-md">
                        <x-app-logo-icon class="size-9 fill-current text-black dark:text-white" />
                    </span>
                    <span class="sr-only">{{ config('app.name', 'Laravel') }}</span>
                </a>
                <div class="flex flex-col gap-6">
                    {{ $slot }}
                </div>
            </div>
        </div>
        @fluxScripts
    </body>
</html>

