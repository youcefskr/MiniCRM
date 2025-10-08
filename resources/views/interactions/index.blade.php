<x-layouts.app :title="__('Interactions - :name', ['name' => $contact->nom])">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                {{ __('Interactions avec') }} {{ $contact->nom }} {{ $contact->prenom }}
            </h2>
            <a href="{{ route('contacts.information') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white dark:bg-gray-800 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700">
                <svg class="w-5 h-5 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                {{ __('Retour aux contacts') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <!-- Formulaire pour nouvelle interaction -->
                    <form action="{{ route('contacts.interactions.store', $contact) }}" method="POST" class="mb-8">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label for="type_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Type d'interaction</label>
                                <select name="type_id" id="type_id" required class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Sélectionner un type</option>
                                    @foreach($types as $type)
                                        <option value="{{ $type->id }}">{{ $type->nom }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="note" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Note</label>
                                <textarea name="note" id="note" rows="3" required class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                            </div>
                            <div>
                                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <svg class="w-5 h-5 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                    {{ __('Ajouter une interaction') }}
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Liste des interactions -->
                    <div class="space-y-6">
                        @forelse($interactions as $interaction)
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <div class="flex items-start justify-between">
                                    <div class="flex items-center space-x-2">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                            @if($interaction->type->nom === 'Appel')
                                                bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100
                                            @elseif($interaction->type->nom === 'E-mail')
                                                bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100
                                            @else
                                                bg-purple-100 text-purple-800 dark:bg-purple-800 dark:text-purple-100
                                            @endif">
                                            {{ $interaction->type->nom }}
                                        </span>
                                        <span class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $interaction->created_at->format('d/m/Y H:i') }}
                                        </span>
                                        <span class="text-sm text-gray-500 dark:text-gray-400">
                                            par {{ $interaction->user->name }}
                                        </span>
                                    </div>
                                    <form action="{{ route('contacts.interactions.destroy', [$contact, $interaction]) }}" method="POST" class="flex">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>

                                <!-- Notes de l'interaction -->
                                <div class="mt-4 space-y-4">
                                    @foreach($interaction->notes as $note)
                                        <div class="pl-4 border-l-2 border-gray-300 dark:border-gray-600">
                                            <p class="text-gray-700 dark:text-gray-300">{{ $note->contenu }}</p>
                                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                                Par {{ $note->user->name }} - {{ $note->created_at->format('d/m/Y H:i') }}
                                            </p>
                                        </div>
                                    @endforeach
                                </div>

                                <!-- Formulaire pour ajouter une note -->
                                <form action="{{ route('contacts.interactions.addNote', [$contact, $interaction]) }}" method="POST" class="mt-4">
                                    @csrf
                                    <div class="flex gap-4">
                                        <input type="text" name="note" required placeholder="Ajouter une note..." 
                                            class="flex-1 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        <button type="submit" class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                            Ajouter
                                        </button>
                                    </div>
                                </form>
                            </div>
                        @empty
                            <p class="text-gray-500 dark:text-gray-400 text-center py-4">
                                Aucune interaction enregistrée pour ce contact.
                            </p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>