<div x-show="showCreateModal" 
     x-cloak
     style="display: none"
     class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4"
     aria-labelledby="modal-title">
    
    <div class="fixed inset-0 bg-black/30 backdrop-blur-sm"
         x-show="showCreateModal"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="showCreateModal = false">
    </div>

    <div class="relative bg-white dark:bg-zinc-900 w-full max-w-3xl rounded-xl shadow-2xl border border-zinc-200 dark:border-zinc-800"
         x-show="showCreateModal"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         @click.away="showCreateModal = false">
        
        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="p-6">
                <h3 class="text-xl font-semibold text-zinc-900 dark:text-zinc-100 mb-6">
                    Créer un nouveau produit
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <flux:input label="Nom du produit" name="name" id="name" required placeholder="Ex: MacBook Pro 16" />
                    </div>

                    <flux:input label="Code produit" name="code" id="code" placeholder="Généré automatiquement si vide" />
                    <flux:input label="Marque" name="brand" id="brand" required placeholder="Ex: Apple" />

                    <div>
                        <label for="category_id" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Catégorie</label>
                        <select name="category_id" id="category_id" required class="w-full rounded-lg border-zinc-300 bg-white text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100">
                            <option value="">Sélectionner une cat égorie</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="type" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Type</label>
                        <select name="type" id="type" required class="w-full rounded-lg border-zinc-300 bg-white text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100">
                            <option value="product">Produit physique</option>
                            <option value="service">Service</option>
                            <option value="subscription">Abonnement</option>
                        </select>
                    </div>

                    <flux:input label="Prix (€)" name="price" id="price" type="number" step="0.01" min="0" required placeholder="0.00" />
                    <flux:input label="Quantité en stock" name="stock_quantity" id="stock_quantity" type="number" min="0" required placeholder="0" />

                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Description</label>
                        <textarea name="description" id="description" rows="3" placeholder="Description détaillée du produit" class="w-full rounded-lg border-zinc-300 bg-white text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100"></textarea>
                    </div>

                    <div class="md:col-span-2">
                        <label for="image" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Image du produit</label>
                        <input type="file" name="image" id="image" accept="image/*" class="w-full text-sm text-zinc-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-indigo-900 dark:file:text-indigo-300">
                    </div>

                    <div class="md:col-span-2 flex items-center">
                        <input type="checkbox" name="is_active" id="is_active" checked class="rounded border-zinc-300 text-indigo-600 focus:ring-indigo-500">
                        <label for="is_active" class="ml-2 text-sm text-zinc-700 dark:text-zinc-300">Produit actif</label>
                    </div>
                </div>
            </div>

            <div class="bg-zinc-50 dark:bg-zinc-800/50 px-6 py-4 rounded-b-xl flex justify-end space-x-3 border-t border-zinc-200 dark:border-zinc-800">
                <flux:button type="button" variant="ghost" @click="showCreateModal = false">Annuler</flux:button>
                <flux:button type="submit" variant="primary">Créer le produit</flux:button>
            </div>
        </form>
    </div>
</div>
