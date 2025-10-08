
<x-layouts.app :title="__('Toutes les interactions')">
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
            {{ __('Toutes les interactions') }}
        </h2>
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
                                        <span class="px-2 py-1 text-xs rounded-full 
                                            {{ $interaction->type->nom === 'Appel' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300' : '' }}
                                            {{ $interaction->type->nom === 'E-mail' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : '' }}
                                            {{ $interaction->type->nom === 'Réunion' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300' : '' }}
                                        ">
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