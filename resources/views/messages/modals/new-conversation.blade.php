<div x-show="showNewConversationModal" 
     x-cloak
     x-transition
     class="fixed inset-0 z-50 overflow-y-auto"
     @keydown.escape.window="showNewConversationModal = false"
     role="dialog"
     aria-modal="true"
     aria-labelledby="modal-title">
    
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
         @click="showNewConversationModal = false">
    </div>

    <!-- Modal Container -->
    <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
        <div x-show="showNewConversationModal"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="relative transform overflow-hidden rounded-lg bg-white dark:bg-gray-800 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg"
             @click.stop>
            
            <!-- Header -->
            <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100">
                        Nouvelle conversation
                    </h3>
                    <button type="button" 
                            @click="showNewConversationModal = false"
                            class="rounded-md text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Type Selection -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Type de conversation</label>
                    <div class="flex gap-4">
                        <label class="flex items-center cursor-pointer">
                            <input type="radio" 
                                   x-model="conversationType" 
                                   value="private" 
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Privée</span>
                        </label>
                        <label class="flex items-center cursor-pointer">
                            <input type="radio" 
                                   x-model="conversationType" 
                                   value="group" 
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Groupe</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div class="bg-white dark:bg-gray-800 px-4 pt-4 pb-4 sm:p-6">
                <!-- Formulaire Conversation Privée -->
                <div x-show="conversationType === 'private'" x-transition>
                    <form action="{{ route('messages.create.private') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label for="user_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Sélectionner un utilisateur
                            </label>
                            <select name="user_id" 
                                    id="user_id"
                                    required
                                    class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Choisir un utilisateur...</option>
                                @foreach($users ?? [] as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="flex justify-end gap-3 mt-6">
                            <button type="button" 
                                    @click="showNewConversationModal = false"
                                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white dark:bg-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Annuler
                            </button>
                            <button type="submit"
                                    class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Créer la conversation
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Formulaire Groupe -->
                <div x-show="conversationType === 'group'" 
                     x-transition
                     x-cloak>
                    <form action="{{ route('messages.create.group') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label for="group_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Nom du groupe
                            </label>
                            <input type="text" 
                                   x-model="groupName" 
                                   name="name" 
                                   id="group_name"
                                   required
                                   placeholder="Ex: Équipe Commerciale"
                                   class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        
                        <div class="mb-4">
                            <label for="group_members" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Membres du groupe
                            </label>
                            <select name="user_ids[]" 
                                    id="group_members"
                                    multiple 
                                    required
                                    size="5"
                                    class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @foreach($users ?? [] as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                Maintenez Ctrl (ou Cmd sur Mac) pour sélectionner plusieurs membres
                            </p>
                        </div>
                        
                        <div class="mb-4">
                            <label for="group_description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Description (optionnel)
                            </label>
                            <textarea x-model="groupDescription" 
                                      name="description" 
                                      id="group_description"
                                      rows="3"
                                      placeholder="Décrivez le but de ce groupe..."
                                      class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                        </div>
                        
                        <div class="flex justify-end gap-3 mt-6">
                            <button type="button" 
                                    @click="showNewConversationModal = false"
                                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white dark:bg-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Annuler
                            </button>
                            <button type="submit"
                                    class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Créer le groupe
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    [x-cloak] { 
        display: none !important; 
    }
</style>

