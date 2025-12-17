<x-layouts.app title="Nouvel abonnement">
    <div class="flex h-full flex-col gap-6">
        
        <!-- Header -->
        <div class="flex items-center gap-4">
            <a href="{{ route('subscriptions.index') }}">
                <flux:button variant="ghost" size="sm" icon="arrow-left" />
            </a>
            <div>
                <flux:heading size="xl">Nouvel abonnement</flux:heading>
                <flux:subheading>Créez un nouvel abonnement ou service récurrent</flux:subheading>
            </div>
        </div>

        <form action="{{ route('subscriptions.store') }}" method="POST" class="space-y-6" x-data="subscriptionForm()">
            @csrf
            
            <div class="grid lg:grid-cols-3 gap-6">
                <!-- Main Form (2/3) -->
                <div class="lg:col-span-2 space-y-6">
                    
                    <!-- Basic Info -->
                    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm overflow-hidden">
                        <div class="p-6 border-b border-zinc-200 dark:border-zinc-800">
                            <h3 class="font-semibold text-zinc-900 dark:text-zinc-100">Informations générales</h3>
                        </div>
                        <div class="p-6 space-y-4">
                            <div>
                                <flux:label for="name">Nom de l'abonnement *</flux:label>
                                <flux:input type="text" name="name" id="name" placeholder="Ex: Maintenance informatique annuelle" 
                                    value="{{ old('name') }}" required />
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <flux:label for="description">Description</flux:label>
                                <flux:textarea name="description" id="description" rows="3" 
                                    placeholder="Description du service inclus dans l'abonnement">{{ old('description') }}</flux:textarea>
                            </div>
                            
                            <div class="grid md:grid-cols-2 gap-4">
                                <div>
                                    <flux:label for="contact_id">Client *</flux:label>
                                    <flux:select name="contact_id" id="contact_id" required>
                                        <option value="">Sélectionner un client</option>
                                        @foreach($contacts as $contact)
                                            <option value="{{ $contact->id }}" @selected(old('contact_id') == $contact->id)>
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
                                    <flux:label for="user_id">Commercial responsable *</flux:label>
                                    <flux:select name="user_id" id="user_id" required>
                                        <option value="">Sélectionner un commercial</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" @selected(old('user_id', auth()->id()) == $user->id)>
                                                {{ $user->name }}
                                            </option>
                                        @endforeach
                                    </flux:select>
                                </div>
                            </div>
                            
                            <div>
                                <flux:label for="opportunity_id">Opportunité liée (optionnel)</flux:label>
                                <flux:select name="opportunity_id" id="opportunity_id">
                                    <option value="">Aucune opportunité</option>
                                    @foreach($opportunities as $opportunity)
                                        <option value="{{ $opportunity->id }}" @selected(old('opportunity_id') == $opportunity->id)>
                                            {{ $opportunity->title }}
                                        </option>
                                    @endforeach
                                </flux:select>
                            </div>
                        </div>
                    </div>

                    <!-- Billing -->
                    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm overflow-hidden">
                        <div class="p-6 border-b border-zinc-200 dark:border-zinc-800">
                            <h3 class="font-semibold text-zinc-900 dark:text-zinc-100">Facturation</h3>
                        </div>
                        <div class="p-6 space-y-4">
                            <div class="grid md:grid-cols-3 gap-4">
                                <div>
                                    <flux:label for="amount">Montant HT *</flux:label>
                                    <div class="relative">
                                        <flux:input type="number" name="amount" id="amount" step="0.01" min="0" 
                                            placeholder="0.00" value="{{ old('amount') }}" required 
                                            x-model="amount" @input="calculateTotal()" />
                                        <div class="absolute right-3 top-1/2 -translate-y-1/2 text-zinc-400 text-sm">DA</div>
                                    </div>
                                    @error('amount')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div>
                                    <flux:label for="tax_rate">Taux TVA (%)</flux:label>
                                    <flux:input type="number" name="tax_rate" id="tax_rate" step="0.01" min="0" max="100" 
                                        value="{{ old('tax_rate', 19) }}" x-model="taxRate" @input="calculateTotal()" />
                                </div>
                                
                                <div>
                                    <flux:label>Montant TTC</flux:label>
                                    <div class="h-10 px-3 py-2 bg-zinc-100 dark:bg-zinc-800 rounded-lg flex items-center font-semibold text-green-600">
                                        <span x-text="formatCurrency(totalTTC)">0,00</span> DA
                                    </div>
                                </div>
                            </div>
                            
                            <div class="grid md:grid-cols-2 gap-4">
                                <div>
                                    <flux:label for="billing_cycle">Cycle de facturation *</flux:label>
                                    <flux:select name="billing_cycle" id="billing_cycle" required x-model="billingCycle">
                                        <option value="monthly">Mensuel</option>
                                        <option value="quarterly">Trimestriel</option>
                                        <option value="semi_annual">Semestriel</option>
                                        <option value="annual">Annuel</option>
                                    </flux:select>
                                </div>
                                
                                <div>
                                    <flux:label>Valeur annuelle estimée</flux:label>
                                    <div class="h-10 px-3 py-2 bg-zinc-100 dark:bg-zinc-800 rounded-lg flex items-center font-semibold text-blue-600">
                                        <span x-text="formatCurrency(annualValue)">0,00</span> DA
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Dates & Renewal -->
                    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm overflow-hidden">
                        <div class="p-6 border-b border-zinc-200 dark:border-zinc-800">
                            <h3 class="font-semibold text-zinc-900 dark:text-zinc-100">Dates et renouvellement</h3>
                        </div>
                        <div class="p-6 space-y-4">
                            <div class="grid md:grid-cols-2 gap-4">
                                <div>
                                    <flux:label for="start_date">Date de début *</flux:label>
                                    <flux:input type="date" name="start_date" id="start_date" 
                                        value="{{ old('start_date', date('Y-m-d')) }}" required />
                                    @error('start_date')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div>
                                    <flux:label for="end_date">Date de fin (optionnel)</flux:label>
                                    <flux:input type="date" name="end_date" id="end_date" 
                                        value="{{ old('end_date') }}" />
                                    <p class="mt-1 text-xs text-zinc-500">Laissez vide pour un abonnement sans date de fin</p>
                                </div>
                            </div>
                            
                            <div class="flex items-center gap-6 p-4 bg-zinc-50 dark:bg-zinc-800/50 rounded-lg">
                                <label class="flex items-center gap-3 cursor-pointer">
                                    <input type="checkbox" name="auto_renew" value="1" checked 
                                        class="rounded border-zinc-300 text-blue-600 focus:ring-blue-500" />
                                    <div>
                                        <span class="font-medium text-zinc-900 dark:text-zinc-100">Renouvellement automatique</span>
                                        <p class="text-sm text-zinc-500">L'abonnement sera renouvelé automatiquement à l'échéance</p>
                                    </div>
                                </label>
                            </div>
                            
                            <div>
                                <flux:label for="renewal_reminder_days">Rappel avant renouvellement (jours)</flux:label>
                                <flux:input type="number" name="renewal_reminder_days" id="renewal_reminder_days" 
                                    min="1" max="90" value="{{ old('renewal_reminder_days', 7) }}" />
                                <p class="mt-1 text-xs text-zinc-500">Nombre de jours avant la date de renouvellement pour envoyer un rappel</p>
                            </div>
                        </div>
                    </div>

                    <!-- Products -->
                    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm overflow-hidden">
                        <div class="p-6 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between">
                            <div>
                                <h3 class="font-semibold text-zinc-900 dark:text-zinc-100">Produits/Services inclus</h3>
                                <p class="text-sm text-zinc-500">Optionnel - Ajoutez les produits inclus dans cet abonnement</p>
                            </div>
                            <flux:button type="button" icon="plus" size="sm" @click="addProduct()">
                                Ajouter
                            </flux:button>
                        </div>
                        <div class="p-6" x-show="selectedProducts.length > 0">
                            <div class="space-y-4">
                                <template x-for="(item, index) in selectedProducts" :key="index">
                                    <div class="flex items-start gap-4 p-4 bg-zinc-50 dark:bg-zinc-800/50 rounded-lg">
                                        <div class="flex-1 grid md:grid-cols-4 gap-4">
                                            <div>
                                                <flux:label>Produit</flux:label>
                                                <select :name="'products['+index+'][id]'" 
                                                    class="w-full rounded-lg border-zinc-300 dark:border-zinc-600 dark:bg-zinc-800"
                                                    x-model="item.id" @change="updateProductPrice(index)">
                                                    <option value="">Sélectionner</option>
                                                    @foreach($products as $product)
                                                        <option value="{{ $product->id }}" data-price="{{ $product->price }}">
                                                            {{ $product->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div>
                                                <flux:label>Quantité</flux:label>
                                                <input type="number" :name="'products['+index+'][quantity]'" min="1" 
                                                    class="w-full rounded-lg border-zinc-300 dark:border-zinc-600 dark:bg-zinc-800"
                                                    x-model="item.quantity" />
                                            </div>
                                            <div>
                                                <flux:label>Prix unitaire</flux:label>
                                                <input type="number" :name="'products['+index+'][unit_price]'" step="0.01" min="0" 
                                                    class="w-full rounded-lg border-zinc-300 dark:border-zinc-600 dark:bg-zinc-800"
                                                    x-model="item.unit_price" />
                                            </div>
                                            <div>
                                                <flux:label>Remise (%)</flux:label>
                                                <input type="number" :name="'products['+index+'][discount]'" step="0.01" min="0" max="100" 
                                                    class="w-full rounded-lg border-zinc-300 dark:border-zinc-600 dark:bg-zinc-800"
                                                    x-model="item.discount" />
                                            </div>
                                        </div>
                                        <button type="button" @click="removeProduct(index)" class="mt-6 p-2 text-red-500 hover:bg-red-50 rounded-lg">
                                            <flux:icon.trash class="size-5" />
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>
                        <div class="p-6 text-center text-zinc-500" x-show="selectedProducts.length === 0">
                            <p>Aucun produit ajouté. Cliquez sur "Ajouter" pour inclure des produits.</p>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm overflow-hidden">
                        <div class="p-6 border-b border-zinc-200 dark:border-zinc-800">
                            <h3 class="font-semibold text-zinc-900 dark:text-zinc-100">Notes et conditions</h3>
                        </div>
                        <div class="p-6 space-y-4">
                            <div>
                                <flux:label for="terms">Conditions particulières</flux:label>
                                <flux:textarea name="terms" id="terms" rows="3" 
                                    placeholder="Conditions spécifiques de cet abonnement">{{ old('terms') }}</flux:textarea>
                            </div>
                            <div>
                                <flux:label for="notes">Notes internes</flux:label>
                                <flux:textarea name="notes" id="notes" rows="3" 
                                    placeholder="Notes internes (non visibles par le client)">{{ old('notes') }}</flux:textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Sidebar -->
                <div class="space-y-6">
                    
                    <!-- Summary Card -->
                    <div class="bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl shadow-lg p-6 text-white sticky top-6">
                        <h3 class="font-bold text-lg mb-4">Récapitulatif</h3>
                        
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between">
                                <span class="text-green-100">Montant HT</span>
                                <span class="font-semibold" x-text="formatCurrency(amount) + ' DA'">0,00 DA</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-green-100">TVA (<span x-text="taxRate">19</span>%)</span>
                                <span class="font-semibold" x-text="formatCurrency(taxAmount) + ' DA'">0,00 DA</span>
                            </div>
                            <div class="border-t border-white/20 pt-3 flex justify-between text-lg">
                                <span>Total TTC</span>
                                <span class="font-bold" x-text="formatCurrency(totalTTC) + ' DA'">0,00 DA</span>
                            </div>
                        </div>
                        
                        <div class="mt-6 pt-4 border-t border-white/20 space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-green-100">Cycle</span>
                                <span class="font-semibold" x-text="billingCycleLabel">Mensuel</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-green-100">Revenu annuel</span>
                                <span class="font-semibold" x-text="formatCurrency(annualValue) + ' DA'">0,00 DA</span>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm p-6">
                        <div class="space-y-3">
                            <flux:button type="submit" variant="primary" class="w-full" icon="check">
                                Créer l'abonnement
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
        function subscriptionForm() {
            return {
                amount: 0,
                taxRate: 19,
                billingCycle: 'monthly',
                selectedProducts: [],
                
                get taxAmount() {
                    return this.amount * (this.taxRate / 100);
                },
                
                get totalTTC() {
                    return parseFloat(this.amount) + this.taxAmount;
                },
                
                get annualValue() {
                    const multipliers = {
                        'monthly': 12,
                        'quarterly': 4,
                        'semi_annual': 2,
                        'annual': 1
                    };
                    return this.amount * (multipliers[this.billingCycle] || 1);
                },
                
                get billingCycleLabel() {
                    const labels = {
                        'monthly': 'Mensuel',
                        'quarterly': 'Trimestriel',
                        'semi_annual': 'Semestriel',
                        'annual': 'Annuel'
                    };
                    return labels[this.billingCycle] || 'Mensuel';
                },
                
                calculateTotal() {
                    // Trigger reactivity
                },
                
                formatCurrency(value) {
                    return new Intl.NumberFormat('fr-FR', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }).format(value || 0);
                },
                
                addProduct() {
                    this.selectedProducts.push({
                        id: '',
                        quantity: 1,
                        unit_price: 0,
                        discount: 0
                    });
                },
                
                removeProduct(index) {
                    this.selectedProducts.splice(index, 1);
                },
                
                updateProductPrice(index) {
                    const select = document.querySelector(`select[name="products[${index}][id]"]`);
                    const option = select.options[select.selectedIndex];
                    if (option && option.dataset.price) {
                        this.selectedProducts[index].unit_price = parseFloat(option.dataset.price);
                    }
                }
            };
        }
    </script>
</x-layouts.app>
