<x-layouts.app title="Nouvelle facture">
    <div class="flex h-full flex-col gap-6">
        
        <!-- Header -->
        <div class="flex items-center gap-4">
            <a href="{{ route('invoices.index') }}">
                <flux:button variant="ghost" size="sm" icon="arrow-left" />
            </a>
            <div>
                <flux:heading size="xl">Nouvelle facture</flux:heading>
                <flux:subheading>Créez une nouvelle facture pour un client</flux:subheading>
            </div>
        </div>

        <form action="{{ route('invoices.store') }}" method="POST" class="space-y-6" x-data="invoiceForm()">
            @csrf
            
            <div class="grid lg:grid-cols-3 gap-6">
                <!-- Main Form (2/3) -->
                <div class="lg:col-span-2 space-y-6">
                    
                    <!-- Client & Dates -->
                    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm overflow-hidden">
                        <div class="p-6 border-b border-zinc-200 dark:border-zinc-800">
                            <h3 class="font-semibold text-zinc-900 dark:text-zinc-100">Informations générales</h3>
                        </div>
                        <div class="p-6 space-y-4">
                            <div class="grid md:grid-cols-2 gap-4">
                                <div>
                                    <flux:label for="contact_id">Client *</flux:label>
                                    <flux:select name="contact_id" id="contact_id" required>
                                        <option value="">Sélectionner un client</option>
                                        @foreach($contacts as $contact)
                                            <option value="{{ $contact->id }}" @selected(old('contact_id', $selectedContact?->id) == $contact->id)>
                                                {{ $contact->nom }} {{ $contact->prenom }} 
                                                @if($contact->entreprise) - {{ $contact->entreprise }} @endif
                                            </option>
                                        @endforeach
                                    </flux:select>
                                    @error('contact_id')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div>
                                    <flux:label for="subscription_id">Abonnement lié (optionnel)</flux:label>
                                    <flux:select name="subscription_id" id="subscription_id">
                                        <option value="">Aucun abonnement</option>
                                        @foreach($subscriptions as $subscription)
                                            <option value="{{ $subscription->id }}" @selected(old('subscription_id', $selectedSubscription?->id) == $subscription->id)>
                                                {{ $subscription->name }} - {{ $subscription->contact->nom }}
                                            </option>
                                        @endforeach
                                    </flux:select>
                                </div>
                            </div>
                            
                            <div class="grid md:grid-cols-2 gap-4">
                                <div>
                                    <flux:label for="issue_date">Date d'émission *</flux:label>
                                    <flux:input type="date" name="issue_date" id="issue_date" 
                                        value="{{ old('issue_date', date('Y-m-d')) }}" required />
                                </div>
                                
                                <div>
                                    <flux:label for="due_date">Date d'échéance *</flux:label>
                                    <flux:input type="date" name="due_date" id="due_date" 
                                        value="{{ old('due_date', date('Y-m-d', strtotime('+30 days'))) }}" required />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Items -->
                    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm overflow-hidden">
                        <div class="p-6 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between">
                            <h3 class="font-semibold text-zinc-900 dark:text-zinc-100">Lignes de facture</h3>
                            <flux:button type="button" icon="plus" size="sm" @click="addItem()">
                                Ajouter une ligne
                            </flux:button>
                        </div>
                        <div class="p-6">
                            <div class="space-y-4">
                                <template x-for="(item, index) in items" :key="index">
                                    <div class="flex items-start gap-4 p-4 bg-zinc-50 dark:bg-zinc-800/50 rounded-lg">
                                        <div class="flex-1 grid md:grid-cols-12 gap-4">
                                            <div class="md:col-span-4">
                                                <flux:label>Description *</flux:label>
                                                <input type="text" :name="'items['+index+'][description]'" required
                                                    class="w-full rounded-lg border-zinc-300 dark:border-zinc-600 dark:bg-zinc-800 text-sm"
                                                    placeholder="Description du produit/service"
                                                    x-model="item.description" />
                                            </div>
                                            <div class="md:col-span-2">
                                                <flux:label>Quantité</flux:label>
                                                <input type="number" :name="'items['+index+'][quantity]'" min="1" 
                                                    class="w-full rounded-lg border-zinc-300 dark:border-zinc-600 dark:bg-zinc-800 text-sm"
                                                    x-model="item.quantity" @input="calculateTotals()" />
                                            </div>
                                            <div class="md:col-span-2">
                                                <flux:label>Prix unit. (DA)</flux:label>
                                                <input type="number" :name="'items['+index+'][unit_price]'" step="0.01" min="0" 
                                                    class="w-full rounded-lg border-zinc-300 dark:border-zinc-600 dark:bg-zinc-800 text-sm"
                                                    x-model="item.unit_price" @input="calculateTotals()" />
                                            </div>
                                            <div class="md:col-span-2">
                                                <flux:label>TVA (%)</flux:label>
                                                <input type="number" :name="'items['+index+'][tax_rate]'" step="0.01" min="0" max="100" 
                                                    class="w-full rounded-lg border-zinc-300 dark:border-zinc-600 dark:bg-zinc-800 text-sm"
                                                    x-model="item.tax_rate" @input="calculateTotals()" />
                                            </div>
                                            <div class="md:col-span-2">
                                                <flux:label>Remise (%)</flux:label>
                                                <input type="number" :name="'items['+index+'][discount]'" step="0.01" min="0" max="100" 
                                                    class="w-full rounded-lg border-zinc-300 dark:border-zinc-600 dark:bg-zinc-800 text-sm"
                                                    x-model="item.discount" @input="calculateTotals()" />
                                            </div>
                                        </div>
                                        <div class="flex flex-col items-end gap-2 min-w-[100px]">
                                            <div class="text-xs text-zinc-500">Total ligne</div>
                                            <div class="font-semibold text-zinc-900 dark:text-zinc-100" x-text="formatCurrency(getItemTotal(index)) + ' DA'"></div>
                                            <button type="button" @click="removeItem(index)" x-show="items.length > 1"
                                                class="p-2 text-red-500 hover:bg-red-50 rounded-lg">
                                                <flux:icon.trash class="size-4" />
                                            </button>
                                        </div>
                                    </div>
                                </template>
                            </div>
                            
                            <!-- Quick add from products -->
                            <div class="mt-4 pt-4 border-t border-zinc-200 dark:border-zinc-700">
                                <p class="text-sm text-zinc-500 mb-2">Ajouter rapidement depuis vos produits:</p>
                                <div class="flex gap-2 flex-wrap">
                                    @foreach($products as $product)
                                        <button type="button" @click="addProductItem({{ json_encode(['name' => $product->name, 'price' => $product->price]) }})"
                                            class="px-3 py-1.5 text-xs bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 rounded-lg transition-colors">
                                            {{ $product->name }} ({{ number_format($product->price, 0, ',', ' ') }} DA)
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm overflow-hidden">
                        <div class="p-6 border-b border-zinc-200 dark:border-zinc-800">
                            <h3 class="font-semibold text-zinc-900 dark:text-zinc-100">Notes et conditions</h3>
                        </div>
                        <div class="p-6 space-y-4">
                            <div>
                                <flux:label for="notes">Notes (visible sur la facture)</flux:label>
                                <flux:textarea name="notes" id="notes" rows="2" 
                                    placeholder="Notes additionnelles pour le client">{{ old('notes') }}</flux:textarea>
                            </div>
                            <div>
                                <flux:label for="terms">Conditions de paiement</flux:label>
                                <flux:textarea name="terms" id="terms" rows="2" 
                                    placeholder="Ex: Paiement à 30 jours">{{ old('terms', 'Paiement à 30 jours. En cas de retard, des pénalités pourront être appliquées.') }}</flux:textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Sidebar -->
                <div class="space-y-6">
                    
                    <!-- Totals -->
                    <div class="bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl shadow-lg p-6 text-white sticky top-6">
                        <h3 class="font-bold text-lg mb-4">Récapitulatif</h3>
                        
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between">
                                <span class="text-blue-100">Sous-total HT</span>
                                <span class="font-semibold" x-text="formatCurrency(subtotal) + ' DA'">0,00 DA</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-blue-100">TVA</span>
                                <span class="font-semibold" x-text="formatCurrency(taxTotal) + ' DA'">0,00 DA</span>
                            </div>
                            <div class="border-t border-white/20 pt-3 flex justify-between text-lg">
                                <span>Total TTC</span>
                                <span class="font-bold" x-text="formatCurrency(grandTotal) + ' DA'">0,00 DA</span>
                            </div>
                        </div>
                        
                        <div class="mt-6 pt-4 border-t border-white/20 text-sm">
                            <div class="flex justify-between">
                                <span class="text-blue-100">Nombre de lignes</span>
                                <span class="font-semibold" x-text="items.length">1</span>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm p-6">
                        <div class="space-y-3">
                            <flux:button type="submit" variant="primary" class="w-full" icon="check">
                                Créer la facture
                            </flux:button>
                            <flux:button type="button" variant="ghost" class="w-full" onclick="window.history.back()">
                                Annuler
                            </flux:button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        function invoiceForm() {
            return {
                items: [
                    { description: '', quantity: 1, unit_price: 0, tax_rate: 19, discount: 0 }
                ],
                
                get subtotal() {
                    return this.items.reduce((sum, item) => {
                        const discountedPrice = item.unit_price * (1 - (item.discount || 0) / 100);
                        return sum + (discountedPrice * (item.quantity || 1));
                    }, 0);
                },
                
                get taxTotal() {
                    return this.items.reduce((sum, item) => {
                        const discountedPrice = item.unit_price * (1 - (item.discount || 0) / 100);
                        const lineTotal = discountedPrice * (item.quantity || 1);
                        return sum + (lineTotal * (item.tax_rate || 0) / 100);
                    }, 0);
                },
                
                get grandTotal() {
                    return this.subtotal + this.taxTotal;
                },
                
                getItemTotal(index) {
                    const item = this.items[index];
                    const discountedPrice = item.unit_price * (1 - (item.discount || 0) / 100);
                    return discountedPrice * (item.quantity || 1);
                },
                
                calculateTotals() {
                    // Trigger reactivity
                },
                
                formatCurrency(value) {
                    return new Intl.NumberFormat('fr-FR', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }).format(value || 0);
                },
                
                addItem() {
                    this.items.push({
                        description: '',
                        quantity: 1,
                        unit_price: 0,
                        tax_rate: 19,
                        discount: 0
                    });
                },
                
                addProductItem(product) {
                    this.items.push({
                        description: product.name,
                        quantity: 1,
                        unit_price: product.price,
                        tax_rate: 19,
                        discount: 0
                    });
                },
                
                removeItem(index) {
                    if (this.items.length > 1) {
                        this.items.splice(index, 1);
                    }
                }
            };
        }
    </script>
</x-layouts.app>
