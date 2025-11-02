<div x-show="showEditModal" 
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto"
     aria-labelledby="modal-title"
     role="dialog"
     aria-modal="true">
    
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div x-show="showEditModal"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
             @click="showEditModal = false">
        </div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div x-show="showEditModal"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
            
            <form x-bind:action="'/contacts/{{ $contact->id }}/interactions/' + selectedInteraction.id" method="POST">
                @csrf
                @method('PUT')
                <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100 mb-4" id="modal-title">
                                Modifier l'interaction
                            </h3>
                            
                            <div class="space-y-4">
                                <div>
                                    <label for="edit_type_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Type d'interaction *</label>
                                    <select name="type_id" id="edit_type_id" required 
                                            x-model="selectedInteraction.type_id"
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        @foreach($types as $type)
                                            <option value="{{ $type->id }}">{{ $type->nom }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="edit_date_interaction" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Date</label>
                                        <input type="date" name="date_interaction" id="edit_date_interaction"
                                               x-model="selectedInteraction.date_interaction"
                                               class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    </div>

                                    <div>
                                        <label for="edit_heure_interaction" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Heure</label>
                                        <input type="time" name="heure_interaction" id="edit_heure_interaction"
                                               x-model="selectedInteraction.heure_interaction"
                                               class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    </div>
                                </div>

                                <div>
                                    <label for="edit_statut" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Statut *</label>
                                    <select name="statut" id="edit_statut" required 
                                            x-model="selectedInteraction.statut"
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        <option value="réalisé">Réalisé</option>
                                        <option value="planifié">Planifié</option>
                                        <option value="annulé">Annulé</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="edit_user_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Utilisateur responsable</label>
                                    <select name="user_id" id="edit_user_id"
                                            x-model="selectedInteraction.user_id"
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label for="edit_note" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Note supplémentaire (optionnel)</label>
                                    <textarea name="note" id="edit_note" rows="4"
                                              placeholder="Ajouter une note supplémentaire..."
                                              class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Cette note sera ajoutée aux notes existantes</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Enregistrer
                    </button>
                    <button type="button"
                            @click="showEditModal = false"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Annuler
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>

