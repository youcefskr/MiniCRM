<div x-show="showDeleteModal" 
     x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center p-4">
    
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm"
         x-show="showDeleteModal"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="showDeleteModal = false">
    </div>

    <div class="relative bg-white dark:bg-zinc-900 w-full max-w-md rounded-2xl shadow-2xl"
         x-show="showDeleteModal"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95">
        
        <template x-if="selectedContact">
            <div>
                <!-- Header -->
                <div class="p-6 text-center">
                    <div class="size-16 mx-auto mb-4 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                        <svg class="size-8 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    
                    <h3 class="text-xl font-bold text-zinc-900 dark:text-zinc-100 mb-2">Supprimer le contact</h3>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">
                        Êtes-vous sûr de vouloir supprimer ce contact ? Cette action est irréversible.
                    </p>
                    
                    <!-- Aperçu contact -->
                    <div class="mt-4 p-4 bg-zinc-50 dark:bg-zinc-800 rounded-xl flex items-center gap-4">
                        <div class="size-12 rounded-full bg-gradient-to-br from-zinc-400 to-zinc-600 flex items-center justify-center text-white font-bold">
                            <span x-text="((selectedContact.prenom?.[0] || '') + (selectedContact.nom?.[0] || '')).toUpperCase()"></span>
                        </div>
                        <div class="text-left">
                            <div class="font-semibold text-zinc-900 dark:text-zinc-100" x-text="(selectedContact.prenom || '') + ' ' + selectedContact.nom"></div>
                            <div class="text-sm text-zinc-500" x-text="selectedContact.email"></div>
                            <div x-show="selectedContact.entreprise" class="text-xs text-zinc-400" x-text="selectedContact.entreprise"></div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="p-6 border-t border-zinc-200 dark:border-zinc-700 flex gap-3">
                    <button type="button" @click="showDeleteModal = false"
                            class="flex-1 px-5 py-2.5 text-sm font-medium text-zinc-700 dark:text-zinc-300 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 rounded-xl transition">
                        Annuler
                    </button>
                    <form :action="`{{ url('/contacts') }}/${selectedContact.id}`" method="POST" class="flex-1">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="w-full px-5 py-2.5 text-sm font-medium text-white bg-gradient-to-r from-red-500 to-red-600 rounded-xl hover:from-red-600 hover:to-red-700 shadow-lg transition">
                            Supprimer
                        </button>
                    </form>
                </div>
            </div>
        </template>
    </div>
</div>