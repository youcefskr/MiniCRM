<x-layouts.app :title="__('Nouvelle conversation')">

        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                {{ __('Nouvelle conversation') }}
            </h2>
            <a href="{{ route('messages.index') }}" 
               class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white dark:bg-gray-800 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Retour
            </a>
        </div>


    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden">
                <div class="p-6" x-data="{ conversationType: 'private' }">
                    <!-- Type de conversation avec design amélioré -->
                    <div class="mb-8">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                            Choisissez le type de conversation
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <button type="button" 
                                    @click="conversationType = 'private'"
                                    :class="{'ring-2 ring-blue-500': conversationType === 'private'}"
                                    class="relative rounded-lg border border-gray-300 dark:border-gray-600 p-4 hover:border-blue-500 dark:hover:border-blue-400 transition-all duration-200">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <h4 class="text-base font-medium text-gray-900 dark:text-gray-100">Conversation privée</h4>
                                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Discussion en tête-à-tête avec un utilisateur</p>
                                    </div>
                                </div>
                            </button>

                            <button type="button"
                                    @click="conversationType = 'group'"
                                    :class="{'ring-2 ring-blue-500': conversationType === 'group'}"
                                    class="relative rounded-lg border border-gray-300 dark:border-gray-600 p-4 hover:border-blue-500 dark:hover:border-blue-400 transition-all duration-200">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <h4 class="text-base font-medium text-gray-900 dark:text-gray-100">Conversation de groupe</h4>
                                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Créer un groupe avec plusieurs participants</p>
                                    </div>
                                </div>
                            </button>
                        </div>
                    </div>

                    <!-- Formulaire conversation privée -->
                    <div x-show="conversationType === 'private'" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-4"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="space-y-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Nouvelle conversation privée</h3>
                        <form action="{{ route('messages.create.private') }}" method="POST">
                            @csrf
                            <div class="space-y-4">
                                <div>
                                    <label for="user_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Sélectionner un utilisateur
                                    </label>
                                    <select id="user_id" 
                                            name="user_id" 
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-blue-500 focus:ring-blue-500" 
                                            required>
                                        <option value="">Choisir un utilisateur</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <button type="submit" 
                                        class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    Démarrer la conversation
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Formulaire groupe -->
                    <div x-show="conversationType === 'group'"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-4"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="space-y-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Nouveau groupe de discussion</h3>
                        <form action="{{ route('messages.create.group') }}" method="POST">
                            @csrf
                            <div class="space-y-4">
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Nom du groupe
                                    </label>
                                    <input type="text" 
                                           id="name" 
                                           name="name" 
                                           class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-blue-500 focus:ring-blue-500" 
                                           required>
                                </div>
                                <div>
                                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Description (optionnelle)
                                    </label>
                                    <textarea id="description" 
                                              name="description" 
                                              rows="3" 
                                              class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                                </div>
                                <div>
                                    <label for="user_ids" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Participants
                                    </label>
                                    <select id="user_ids" 
                                            name="user_ids[]" 
                                            multiple 
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-blue-500 focus:ring-blue-500" 
                                            required>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                        Maintenez Ctrl (Cmd sur Mac) pour sélectionner plusieurs participants
                                    </p>
                                </div>
                                <button type="submit" 
                                        class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    Créer le groupe
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>