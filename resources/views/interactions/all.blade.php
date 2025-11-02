
<x-layouts.app :title="__('Toutes les interactions')">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                {{ __('Toutes les interactions') }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('interactions.modern') }}" 
                   class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white dark:bg-gray-800 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700">
                    {{ __('Vue timeline') }}
                </a>
                <a href="{{ route('interactions.dashboard') }}" 
                   class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                    </svg>
                    {{ __('Dashboard') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <div class="space-y-6">
                        @foreach($interactions as $interaction)
                            <div class="border-b border-gray-200 dark:border-gray-700 pb-4 last:border-0 last:pb-0">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <span class="px-2 py-1 text-xs rounded-full {{ $interaction->type->getBadgeClasses() }}">
                                            {{ $interaction->type->nom }}
                                        </span>
                                        <a href="{{ route('contacts.show', $interaction->contact) }}" class="text-blue-600 dark:text-blue-400 hover:underline">
                                            {{ $interaction->contact->nom }} {{ $interaction->contact->prenom }}
                                        </a>
                                    </div>
                                    <span class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $interaction->created_at->format('d/m/Y H:i') }}
                                    </span>
                                </div>
                                
                                <div class="mt-2 space-y-2">
                                    @foreach($interaction->notes as $note)
                                        <div class="pl-4 border-l-2 border-gray-200 dark:border-gray-700">
                                            <p class="text-gray-700 dark:text-gray-300">{{ $note->contenu }}</p>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                                Par {{ $note->user->name }} - {{ $note->created_at->diffForHumans() }}
                                            </p>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-6">
                        {{ $interactions->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>