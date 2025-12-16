<x-layouts.app title="Modifier l'opportunité">
    <div class="p-6 max-w-6xl mx-auto space-y-6" x-data="{
        selectedProducts: {{ Js::from($opportunity->products->map(function($product) {
            return [
                'product_id' => $product->id,
                'quantity' => $product->pivot->quantity,
                'unit_price' => $product->pivot->unit_price,
                'discount' => $product->pivot->discount,
                'total' => $product->pivot->total_price
            ];
        })) }},
        
        addProduct() {
            this.selectedProducts.push({
                product_id: '',
                quantity: 1,
                unit_price: 0,
                discount: 0,
                total: 0
            });
        },
        
        removeProduct(index) {
            this.selectedProducts.splice(index, 1);
            this.updateMainValue();
        },
        
        updateProductPrice(index) {
            const item = this.selectedProducts[index];
            const product = products.find(p => p.id == item.product_id);
            if (product) {
                item.unit_price = product.price;
                this.calculateTotal(index);
            }
        },
        
        calculateTotal(index) {
            const item = this.selectedProducts[index];
            const subtotal = item.quantity * item.unit_price;
            item.total = subtotal * (1 - (item.discount / 100));
            this.updateMainValue();
        },
        
        get grandTotal() {
            return this.selectedProducts.reduce((sum, item) => sum + parseFloat(item.total || 0), 0);
        },
        
        updateMainValue() {
            // Mettre à jour automatiquement la valeur de l'opportunité
            document.getElementById('value').value = this.grandTotal.toFixed(2);
        }
    }">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">Modifier l'opportunité</h1>
                <p class="text-sm text-zinc-600 dark:text-zinc-400 mt-1">{{ $opportunity->title }}</p>
            </div>
            <flux:button icon="arrow-left" href="{{ route('opportunities.index') }}" variant="ghost">Retour</flux:button>
        </div>

        <form action="{{ route('opportunities.update', $opportunity) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Informations de base -->
            <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-6">
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">Informations générales</h3>
                
                <div class="space-y-4">
                    <div>
                        <flux:input label="Titre de l'opportunité" type="text" name="title" id="title" required value="{{ old('title', $opportunity->title) }}" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="contact_id" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Client</label>
                            <select name="contact_id" id="contact_id" required class="block w-full rounded-lg border-zinc-200 bg-white px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100">
                                @foreach($contacts as $contact)
                                    <option value="{{ $contact->id }}" @selected($contact->id == $opportunity->contact_id)>{{ $contact->nom }} {{ $contact->prenom }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <flux:input label="Valeur estimée (€)" type="number" name="value" id="value" required min="0" step="0.01" value="{{ old('value', $opportunity->value) }}" />
                            <p class="text-xs text-zinc-500 mt-1">✨ Se met à jour automatiquement avec les produits</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="stage" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Étape</label>
                            <select name="stage" id="stage" required class="block w-full rounded-lg border-zinc-200 bg-white px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100">
                                @foreach($stages as $key => $label)
                                    <option value="{{ $key }}" @selected($key == $opportunity->stage)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <flux:input label="Probabilité (%)" type="number" name="probability" id="probability" required min="0" max="100" value="{{ old('probability', $opportunity->probability) }}" />
                        </div>
                    </div>

                    <div>
                        <flux:input label="Date de clôture estimée" type="date" name="expected_close_date" id="expected_close_date" value="{{ old('expected_close_date', $opportunity->expected_close_date?->format('Y-m-d')) }}" />
                    </div>

                    <div>
                        <label for="notes" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Notes</label>
                        <textarea name="notes" id="notes" rows="3" class="block w-full rounded-lg border-zinc-200 bg-white px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100">{{ old('notes', $opportunity->notes) }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Section Produits -->
            <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                        Produits / Services
                    </h3>
                    <button type="button" @click="addProduct()" class="inline-flex items-center gap-2 px-3 py-1.5 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700">
                        <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        Ajouter un produit
                    </button>
                </div>

                <div class="space-y-3" x-show="selectedProducts.length > 0">
                    <template x-for="(item, index) in selectedProducts" :key="index">
                        <div class="bg-zinc-50 dark:bg-zinc-800/50 rounded-lg p-4 border border-zinc-200 dark:border-zinc-700">
                            <div class="grid grid-cols-12 gap-3 items-end">
                                <!-- Produit -->
                                <div class="col-span-12 md:col-span-4">
                                    <label class="block text-xs font-medium text-zinc-700 dark:text-zinc-300 mb-1">Produit</label>
                                    <select :name="`products[${index}][product_id]`" 
                                            x-model="item.product_id"
                                            @change="updateProductPrice(index)"
                                            required
                                            class="w-full rounded-lg border-zinc-200 bg-white px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100">
                                        <option value="">Sélectionner...</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}">
                                                {{ $product->name }} - {{ number_format($product->price, 2) }} €
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Quantité -->
                                <div class="col-span-3 md:col-span-2">
                                    <label class="block text-xs font-medium text-zinc-700 dark:text-zinc-300 mb-1">Qté</label>
                                    <input type="number" 
                                           :name="`products[${index}][quantity]`"
                                           x-model.number="item.quantity"
                                           @input="calculateTotal(index)"
                                           min="1" 
                                           required
                                           class="w-full rounded-lg border-zinc-200 bg-white px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100">
                                </div>

                                <!-- Prix unitaire -->
                                <div class="col-span-3 md:col-span-2">
                                    <label class="block text-xs font-medium text-zinc-700 dark:text-zinc-300 mb-1">Prix unit.</label>
                                    <input type="number" 
                                           :name="`products[${index}][unit_price]`"
                                           x-model.number="item.unit_price"
                                           @input="calculateTotal(index)"
                                           step="0.01" 
                                           min="0"
                                           required
                                           class="w-full rounded-lg border-zinc-200 bg-white px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100">
                                </div>

                                <!-- Remise -->
                                <div class="col-span-3 md:col-span-2">
                                    <label class="block text-xs font-medium text-zinc-700 dark:text-zinc-300 mb-1">Remise %</label>
                                    <input type="number" 
                                           :name="`products[${index}][discount]`"
                                           x-model.number="item.discount"
                                           @input="calculateTotal(index)"
                                           step="0.01" 
                                           min="0" 
                                           max="100"
                                           class="w-full rounded-lg border-zinc-200 bg-white px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100">
                                </div>

                                <!-- Total -->
                                <div class="col-span-2 md:col-span-1">
                                    <label class="block text-xs font-medium text-zinc-700 dark:text-zinc-300 mb-1">Total</label>
                                    <div class="px-3 py-2 bg-zinc-100 dark:bg-zinc-900 rounded-lg text-sm font-semibold text-zinc-900 dark:text-zinc-100" x-text="(item.total || 0).toFixed(2) + ' €'"></div>
                                </div>

                                <!-- Bouton supprimer -->
                                <div class="col-span-1">
                                    <button type="button" 
                                            @click="removeProduct(index)"
                                            class="p-2 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg">
                                        <svg class="size-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>

                    <!-- Grand Total -->
                    <div class="flex justify-end items-center gap-3 pt-3 border-t border-zinc-200 dark:border-zinc-700">
                        <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Total général:</span>
                        <span class="text-xl font-bold text-zinc-900 dark:text-zinc-100" x-text="grandTotal.toFixed(2) + ' €'"></span>
                    </div>
                </div>

                <div x-show="selectedProducts.length === 0" class="text-center py-8 text-zinc-500">
                    <svg class="size-12 mx-auto mb-3 text-zinc-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                    </svg>
                    <p class="text-sm">Aucun produit ajouté</p>
                    <p class="text-xs mt-1">Cliquez sur "Ajouter un produit" pour commencer</p>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-end gap-3">
                <flux:button href="{{ route('opportunities.index') }}" variant="ghost">Annuler</flux:button>
                <flux:button type="submit" variant="primary">Enregistrer les modifications</flux:button>
            </div>
        </form>
    </div>

    <script>
        // Définir les produits globalement pour Alpine.js
        window.products = @json($products);
    </script>
</x-layouts.app>
