<div x-show="showDeleteModal" 
     x-cloak
     style="display: none"
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

    <div class="relative bg-white dark:bg-zinc-900 w-full max-w-md rounded-xl shadow-2xl border border-zinc-200 dark:border-zinc-800"
         x-show="showDeleteModal"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         @click.away="showDeleteModal = false">
        
        <div class="p-6">
            <h3 class="text-xl font-semibold text-red-600 dark:text-red-500 mb-4">
                Supprimer le contact
            </h3>
            <p class="text-zinc-600 dark:text-zinc-400">
                Êtes-vous sûr de vouloir supprimer le contact <span x-text="selectedContact?.nom + ' ' + (selectedContact?.prenom || '')" class="font-medium text-zinc-900 dark:text-zinc-100"></span> ? Cette action est irréversible.
            </p>
        </div>

        <div class="bg-zinc-50 dark:bg-zinc-800/50 px-6 py-4 rounded-b-xl flex justify-end space-x-3 border-t border-zinc-200 dark:border-zinc-800">
            <flux:button type="button" variant="ghost" @click="showDeleteModal = false">Annuler</flux:button>
            <form x-bind:action="'/contacts/' + selectedContact?.id" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <flux:button type="submit" variant="danger">Supprimer</flux:button>
            </form>
        </div>
    </div>
</div>