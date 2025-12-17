<div class="relative">
    <flux:dropdown position="bottom" align="end">
        <flux:button variant="ghost" class="flex items-center gap-2 px-3 py-2 text-sm font-medium text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-lg transition-colors">
            <span class="text-lg">{{ $locales[$currentLocale]['flag'] ?? '🌐' }}</span>
            <span class="hidden sm:inline">{{ strtoupper($currentLocale) }}</span>
            <flux:icon.chevron-down class="size-4" />
        </flux:button>

        <flux:menu class="w-48">
            <flux:menu.heading>{{ __('common.select_language') }}</flux:menu.heading>
            
            @foreach($locales as $code => $locale)
                <flux:menu.item 
                    wire:click="switchLocale('{{ $code }}')"
                    class="flex items-center gap-3 {{ $currentLocale === $code ? 'bg-blue-50 dark:bg-blue-900/20' : '' }}"
                >
                    <span class="text-lg">{{ $locale['flag'] }}</span>
                    <div class="flex-1">
                        <div class="font-medium {{ $currentLocale === $code ? 'text-blue-600 dark:text-blue-400' : '' }}">
                            {{ $locale['native'] }}
                        </div>
                        <div class="text-xs text-zinc-500">{{ $locale['name'] }}</div>
                    </div>
                    @if($currentLocale === $code)
                        <flux:icon.check class="size-4 text-blue-600 dark:text-blue-400" />
                    @endif
                </flux:menu.item>
            @endforeach
        </flux:menu>
    </flux:dropdown>
</div>
