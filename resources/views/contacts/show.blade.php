<x-layouts.app :title="$contact->nom . ' ' . $contact->prenom">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center">
                    <span class="text-xl font-bold text-white">
                        {{ substr($contact->prenom, 0, 1) }}{{ substr($contact->nom, 0, 1) }}
                    </span>
                </div>
                <h2 class="text-2xl font-bold leading-tight text-gray-800 dark:text-gray-200">
                    {{ $contact->nom }} {{ $contact->prenom }}
                </h2>
            </div>
            <div class="flex items-center space-x-4">
                <a href="{{ route('contacts.edit', $contact) }}" 
                   class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                    </svg>
                    Modifier
                </a>
                <a href="{{ route('contacts.interactions.index', $contact) }}"
                   class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-purple-600 border border-transparent rounded-md shadow-sm hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-all duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                    </svg>
                    Interactions
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-xl rounded-lg overflow-hidden">
                <div class="p-8 space-y-8">
                    <div class="grid grid-cols-1 gap-8 md:grid-cols-2">
                        <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg shadow-inner">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                Informations personnelles
                            </h3>
                            <dl class="mt-6 space-y-6">
                                <div class="relative">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Nom</dt>
                                    <dd class="mt-1 text-lg text-gray-900 dark:text-gray-100 font-medium">{{ $contact->nom }}</dd>
                                </div>
                                <div class="relative">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Prénom</dt>
                                    <dd class="mt-1 text-lg text-gray-900 dark:text-gray-100 font-medium">{{ $contact->prenom }}</dd>
                                </div>
                                <div class="relative">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Email</dt>
                                    <dd class="mt-1 text-lg text-blue-600 dark:text-blue-400 hover:text-blue-800 transition-colors duration-200">
                                        <a href="mailto:{{ $contact->email }}">{{ $contact->email }}</a>
                                    </dd>
                                </div>
                                <div class="relative">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Téléphone</dt>
                                    <dd class="mt-1 text-lg text-gray-900 dark:text-gray-100 font-medium">
                                        <a href="tel:{{ $contact->telephone }}" class="hover:text-blue-600 transition-colors duration-200">
                                            {{ $contact->telephone }}
                                        </a>
                                    </dd>
                                </div>
                            </dl>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg shadow-inner">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                                Informations professionnelles
                            </h3>
                            <dl class="mt-6 space-y-6">
                                <div class="relative">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Entreprise</dt>
                                    <dd class="mt-1 text-lg text-gray-900 dark:text-gray-100 font-medium">{{ $contact->entreprise ?? 'Non renseigné' }}</dd>
                                </div>
                                <div class="relative">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Adresse</dt>
                                    <dd class="mt-1 text-lg text-gray-900 dark:text-gray-100 font-medium">{{ $contact->adresse ?? 'Non renseignée' }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>