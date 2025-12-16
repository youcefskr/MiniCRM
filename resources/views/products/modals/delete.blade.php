<div x-show="showDeleteModal" 
     x-cloak
     style="display: none"
     class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4">
    
    <div class="fixed inset-0 bg-black/30 backdrop-blur-sm" @click="showDeleteModal = false"></div>

    <div class="relative bg-white dark:bg-zinc-900 w-full max-w-md rounded-xl shadow-2xl border border-zinc-200 dark:border-zinc-800" @click.away="showDeleteModal = false">
        
        <form :action="`{{ url('admin/products') }}/${selectedProduct?.id}`" method="POST" x-show="selectedProduct">
            @csrf
            @method('DELETE')
            <div class="p-6">
                <div class="flex items-center gap-4 mb-4">
                    <div class="flex-shrink-0 h-12 w-12 rounded-full bg-red-100 dark:bg-red-900/20 flex items-center justify-center">
                        <flux:icon.exclamation-triangle class="h-6 w-6 text-red-600 dark:text-red-400" />
                    </div>
                    <div>
                        <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100">
                            Supprimer le produit
                        </h3>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">
                            Cette action est irréversible
                        </p>
                    </div>
                </div>

                <div class="bg-zinc-50 dark:bg-zinc-800/50 rounded-lg p-4 mb-4">
                    <p class="text-sm text-zinc-700 dark:text-zinc-300">
                        Êtes-vous sûr de vouloir supprimer le produit <strong x-text="selectedProduct?.name"></strong> ?
                    </p>
                </div>
            </div>

            <div class="bg-zinc-50 dark:bg-zinc-800/50 px-6 py-4 rounded-b-xl flex justify-end space-x-3 border-t border-zinc-200 dark:border-zinc-800">
                <flux:button type="button" variant="ghost" @click="showDeleteModal = false">Annuler</flux:button>
                <flux:button type="submit" variant="danger">Supprimer</flux:button>
            </div>
        </form>
    </div>
</div>
