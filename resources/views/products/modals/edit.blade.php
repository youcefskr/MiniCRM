<div x-show="showEditModal" 
     x-cloak
     style="display: none"
     class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4">
    
    <div class="fixed inset-0 bg-black/30 backdrop-blur-sm"
         @click="showEditModal = false"></div>

    <div class="relative bg-white dark:bg-zinc-900 w-full max-w-3xl rounded-xl shadow-2xl border border-zinc-200 dark:border-zinc-800" @click.away="showEditModal = false">
        
        <form :action="`{{ url('admin/products') }}/${selectedProduct?.id}`" method="POST" enctype="multipart/form-data" x-show="selectedProduct">
            @csrf
            @method('PUT')
            <div class="p-6">
                <h3 class="text-xl font-semibold text-zinc-900 dark:text-zinc-100 mb-6">
                    Modifier le produit
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <flux:input label="Nom du produit" name="name" ::value="selectedProduct?.name" required />
                    </div>

                    <flux:input label="Code produit" name="code" ::value="selectedProduct?.code" />
                    <flux:input label="Marque" name="brand" ::value="selectedProduct?.brand" required />

                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Catégorie</label>
                        <select name="category_id" required class="w-full rounded-lg border-zinc-300 bg-white text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100">
                            <template x-for="category in categories" :key="category.id">
                                <option :value="category.id" :selected="category.id === selectedProduct?.category_id" x-text="category.name"></option>
                            </template>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Type</label>
                        <select name="type" required class="w-full rounded-lg border-zinc-300 bg-white text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100">
                            <option value="product" :selected="selectedProduct?.type === 'product'">Produit physique</option>
                            <option value="service" :selected="selectedProduct?.type === 'service'">Service</option>
                            <option value="subscription" :selected="selectedProduct?.type === 'subscription'">Abonnement</option>
                        </select>
                    </div>

                    <flux:input label="Prix (€)" name="price" type="number" step="0.01" ::value="selectedProduct?.price" required />
                    <flux:input label="Quantité en stock" name="stock_quantity" type="number" ::value="selectedProduct?.stock_quantity" required />

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Description</label>
                        <textarea name="description" rows="3" x-text="selectedProduct?.description" class="w-full rounded-lg border-zinc-300 bg-white text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100"></textarea>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Image du produit</label>
                        <input type="file" name="image" accept="image/*" class="w-full text-sm text-zinc-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                        <p class="text-xs text-zinc-500 mt-1">Laisser vide pour conserver l'image actuelle</p>
                    </div>

                    <div class="md:col-span-2 flex items-center">
                        <input type="checkbox" name="is_active" ::checked="selectedProduct?.is_active" class="rounded border-zinc-300 text-indigo-600 focus:ring-indigo-500">
                        <label class="ml-2 text-sm text-zinc-700 dark:text-zinc-300">Produit actif</label>
                    </div>
                </div>
            </div>

            <div class="bg-zinc-50 dark:bg-zinc-800/50 px-6 py-4 rounded-b-xl flex justify-end space-x-3 border-t border-zinc-200 dark:border-zinc-800">
                <flux:button type="button" variant="ghost" @click="showEditModal = false">Annuler</flux:button>
                <flux:button type="submit" variant="primary">Mettre à jour</flux:button>
            </div>
        </form>
    </div>
</div>
