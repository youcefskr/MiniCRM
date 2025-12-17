<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ App\Http\Middleware\SetLocale::isRtl() ? 'rtl' : 'ltr' }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <flux:sidebar sticky stashable class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

            <a href="{{ route('dashboard') }}" class="mr-5 flex items-center gap-2 px-2">
                <svg class="size-6 text-zinc-900 dark:text-zinc-100" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
                </svg>
                <span class="font-bold text-zinc-900 dark:text-zinc-100">MiniCRM</span>
            </a>

            <flux:navlist variant="outline">
                <flux:navlist.group :heading="__('common.platform')" class="grid">
                    <flux:navlist.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>{{ __('common.dashboard') }}</flux:navlist.item>
                    <flux:navlist.item icon="currency-dollar" :href="route('opportunities.index')" :current="request()->routeIs('opportunities.*')" wire:navigate>{{ __('common.opportunities') }}</flux:navlist.item>
                    <flux:navlist.item icon="clipboard-document-list" :href="route('tasks.index')" :current="request()->routeIs('tasks.*')" wire:navigate>{{ __('common.tasks') }}</flux:navlist.item>
                    <flux:navlist.item icon="chat-bubble-left-right" :href="route('interactions.index')" :current="request()->routeIs('interactions.*')" wire:navigate>{{ __('common.interactions') }}</flux:navlist.item>
                    <flux:navlist.item icon="arrow-path" :href="route('subscriptions.index')" :current="request()->routeIs('subscriptions.*')" wire:navigate>{{ __('common.subscriptions') }}</flux:navlist.item>
                    <flux:navlist.item icon="document-text" :href="route('invoices.index')" :current="request()->routeIs('invoices.*')" wire:navigate>{{ __('common.invoices') }}</flux:navlist.item>
                    <flux:navlist.item icon="chat-bubble-left-ellipsis" :href="route('messages.index')" :current="request()->routeIs('messages.*')" wire:navigate>{{ __('common.messaging') }}</flux:navlist.item>
                    
                    <flux:navlist.group x-data="{ open: false }" class="relative">
                        <flux:navlist.item @click="open = !open" icon="users" class="cursor-pointer">
                            <div class="flex items-center justify-between w-full">
                                {{ __('common.contacts') }}
                                <flux:icon.chevron-down class="size-4 transition-transform" ::class="{ 'rotate-180': open }" />
                            </div>
                        </flux:navlist.item>

                        <div x-show="open" x-collapse class="pl-4 space-y-1">
                            @include('components.layouts.app.sub-menus.contact-menu-items')
                        </div>
                    </flux:navlist.group>
                </flux:navlist.group>

                @if(auth()->user()->can('manage users') || auth()->user()->can('manage role and permissions') || auth()->user()->can('gere type interaction'))
                    <flux:navlist.group :heading="__('common.administration')" class="grid mt-4">
                        @can('manage users')
                            <flux:navlist.item icon="user-group" :href="route('admin.users.index')" :current="request()->routeIs('admin.users.*')" wire:navigate>{{ __('common.users') }}</flux:navlist.item>
                        @endcan

                        @can('manage role and permissions')
                            <flux:navlist.item icon="shield-check" :href="route('admin.roles.index')" :current="request()->routeIs('admin.roles.*')" wire:navigate>{{ __('common.roles_permissions') }}</flux:navlist.item>
                        @endcan

                        @can('gere type interaction')
                            <flux:navlist.item icon="list-bullet" :href="route('types-interactions.index')" :current="request()->routeIs('types-interactions.*')" wire:navigate>{{ __('common.interaction_types') }}</flux:navlist.item>
                        @endcan

                        <flux:navlist.item icon="cube" :href="route('admin.products.index')" :current="request()->routeIs('admin.products.*') || request()->routeIs('admin.categories.*')" wire:navigate>{{ __('common.products_categories') }}</flux:navlist.item>
                        
                        <flux:navlist.item icon="clipboard-document-list" :href="route('admin.activity-logs.index')" :current="request()->routeIs('admin.activity-logs.*')" wire:navigate>{{ __('common.activity_log') }}</flux:navlist.item>
                    </flux:navlist.group>
                @endif
            </flux:navlist>

            <flux:spacer />

            <flux:dropdown class="hidden lg:block" position="bottom" align="start">
                <flux:profile :name="auth()->user()->name" :initials="auth()->user()->initials()" icon:trailing="chevrons-up-down" />

                <flux:menu class="w-[220px]">
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                    <span class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
                                        {{ auth()->user()->initials() }}
                                    </span>
                                </span>

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                    <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>{{ __('Paramètres') }}</flux:menu.item>
                    
                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                            {{ __('Déconnexion') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:sidebar>

        <!-- Top Navbar -->
        <flux:header class="border-b border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <div class="flex items-center gap-4 w-full">
                <flux:input icon="magnifying-glass" :placeholder="__('common.search')" class="max-w-sm hidden lg:block" />
                
                <flux:spacer />

                <livewire:language-switcher />
                <livewire:notifications-menu />
                <flux:button icon="question-mark-circle" variant="ghost" class="text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-200 hidden lg:flex" />

                <!-- Mobile Profile Dropdown -->
                <flux:dropdown position="top" align="end" class="lg:hidden">
                    <flux:profile :initials="auth()->user()->initials()" icon-trailing="chevron-down" />

                    <flux:menu>
                        <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>{{ __('Paramètres') }}</flux:menu.item>
                        <form method="POST" action="{{ route('logout') }}" class="w-full">
                            @csrf
                            <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                                {{ __('Déconnexion') }}
                            </flux:menu.item>
                        </form>
                    </flux:menu>
                </flux:dropdown>
            </div>
        </flux:header>

        {{ $slot }}

        @fluxScripts
    </body>
</html>
