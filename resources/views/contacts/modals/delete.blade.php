
<div x-show="showDeleteModal" 
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4"
     aria-labelledby="modal-title">
    
    <div class="fixed inset-0 bg-black/30 backdrop-blur-sm"
         x-show="showDeleteModal"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="showDeleteModal = false">
    </div>

    <div class="relative bg-white w-full max-w-md rounded-xl shadow-2xl"
         x-show="showDeleteModal"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         @click.away="showDeleteModal = false">
        
        <div class="p-6">
            <div class="flex items-center space-x-4">
                <div class="flex-shrink-0 w-12 h-12 flex items-center justify-center rounded-full bg-red-100">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-medium text-gray-900">
                        Supprimer le contact
                    </h3>
                    <p class="mt-2 text-sm text-gray-500">
                        Êtes-vous sûr de vouloir supprimer le contact <span x-text="selectedContact?.nom + ' ' + selectedContact?.prenom" class="font-medium"></span> ? Cette action est irréversible.
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-gray-50 px-6 py-4 rounded-b-xl flex justify-end space-x-3">
            <button type="button" @click="showDeleteModal = false"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                Annuler
            </button>
            <form x-bind:action="'/contacts/' + selectedContact?.id" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700">
                    Supprimer
                </button>
            </form>
        </div>
    </div>
</div>