<x-layouts.app title="Facture: {{ $invoice->invoice_number }}">
    <div class="flex h-full flex-col gap-6">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
            <div class="flex items-start gap-4">
                <a href="{{ route('invoices.index') }}">
                    <flux:button variant="ghost" size="sm" icon="arrow-left" />
                </a>
                <div>
                    <div class="flex items-center gap-3">
                        <flux:heading size="xl">{{ $invoice->invoice_number }}</flux:heading>
                        @php
                            $statusColors = [
                                'draft' => 'bg-zinc-100 text-zinc-800',
                                'sent' => 'bg-blue-100 text-blue-800',
                                'paid' => 'bg-green-100 text-green-800',
                                'partial' => 'bg-yellow-100 text-yellow-800',
                                'overdue' => 'bg-red-100 text-red-800',
                                'cancelled' => 'bg-zinc-100 text-zinc-600',
                            ];
                        @endphp
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $statusColors[$invoice->status] }}">
                            {{ $invoice->status_label }}
                        </span>
                    </div>
                    <flux:subheading class="flex items-center gap-2 mt-1">
                        <span>Émise le {{ $invoice->issue_date->format('d/m/Y') }}</span>
                        <span class="text-zinc-300">•</span>
                        <span>Échéance le {{ $invoice->due_date->format('d/m/Y') }}</span>
                        @if($invoice->is_overdue)
                            <span class="text-zinc-300">•</span>
                            <span class="text-red-600 font-medium">{{ $invoice->days_overdue }} jours de retard</span>
                        @endif
                    </flux:subheading>
                </div>
            </div>
            <div class="flex gap-2">
                <flux:button icon="arrow-down-tray" href="{{ route('invoices.download', $invoice) }}">
                    Télécharger PDF
                </flux:button>
                @if($invoice->status !== 'paid' && $invoice->status !== 'cancelled')
                    <flux:button icon="pencil" href="{{ route('invoices.edit', $invoice) }}">Modifier</flux:button>
                @endif
            </div>
        </div>

        <!-- Alert for overdue -->
        @if($invoice->is_overdue)
            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4">
                <div class="flex items-center gap-3">
                    <flux:icon.exclamation-circle class="size-6 text-red-600" />
                    <div>
                        <h4 class="font-medium text-red-800 dark:text-red-200">Facture en retard</h4>
                        <p class="text-sm text-red-700 dark:text-red-300">
                            Cette facture est en retard de {{ $invoice->days_overdue }} jours. 
                            Reste à payer: {{ number_format($invoice->amount_due, 2, ',', ' ') }} DA
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <div class="grid lg:grid-cols-3 gap-6">
            
            <!-- Main Content (2/3) -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- Invoice Preview -->
                <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm overflow-hidden">
                    <div class="p-8">
                        <!-- Header -->
                        <div class="flex justify-between items-start mb-8">
                            <div>
                                <h2 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">FACTURE</h2>
                                <p class="text-lg text-zinc-600 dark:text-zinc-400">{{ $invoice->invoice_number }}</p>
                            </div>
                            <div class="text-right">
                                <div class="text-sm text-zinc-500">Date d'émission</div>
                                <div class="font-medium">{{ $invoice->issue_date->format('d/m/Y') }}</div>
                                <div class="text-sm text-zinc-500 mt-2">Date d'échéance</div>
                                <div class="font-medium">{{ $invoice->due_date->format('d/m/Y') }}</div>
                            </div>
                        </div>

                        <!-- From / To -->
                        <div class="grid md:grid-cols-2 gap-8 mb-8">
                            <div>
                                <div class="text-sm font-medium text-zinc-500 mb-2">DE</div>
                                <div class="font-semibold text-zinc-900 dark:text-zinc-100">Votre Entreprise</div>
                                <div class="text-sm text-zinc-600 dark:text-zinc-400">
                                    Adresse de l'entreprise<br>
                                    Ville, Pays
                                </div>
                            </div>
                            <div>
                                <div class="text-sm font-medium text-zinc-500 mb-2">FACTURÉ À</div>
                                <div class="font-semibold text-zinc-900 dark:text-zinc-100">
                                    {{ $invoice->contact->nom }} {{ $invoice->contact->prenom }}
                                </div>
                                <div class="text-sm text-zinc-600 dark:text-zinc-400">
                                    @if($invoice->contact->entreprise){{ $invoice->contact->entreprise }}<br>@endif
                                    @if($invoice->contact->email){{ $invoice->contact->email }}<br>@endif
                                    @if($invoice->contact->telephone){{ $invoice->contact->telephone }}@endif
                                </div>
                            </div>
                        </div>

                        <!-- Items Table -->
                        <div class="border border-zinc-200 dark:border-zinc-700 rounded-lg overflow-hidden mb-8">
                            <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                                <thead class="bg-zinc-50 dark:bg-zinc-800">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-zinc-500 uppercase">Description</th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-zinc-500 uppercase">Qté</th>
                                        <th class="px-4 py-3 text-right text-xs font-medium text-zinc-500 uppercase">Prix unit.</th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-zinc-500 uppercase">TVA</th>
                                        <th class="px-4 py-3 text-right text-xs font-medium text-zinc-500 uppercase">Total</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                                    @foreach($invoice->items as $item)
                                        <tr>
                                            <td class="px-4 py-3">
                                                <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $item->description }}</div>
                                                @if($item->discount > 0)
                                                    <div class="text-xs text-green-600">Remise: -{{ $item->discount }}%</div>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 text-center text-sm text-zinc-600 dark:text-zinc-400">
                                                {{ $item->quantity }}
                                            </td>
                                            <td class="px-4 py-3 text-right text-sm text-zinc-600 dark:text-zinc-400">
                                                {{ number_format($item->unit_price, 2, ',', ' ') }} DA
                                            </td>
                                            <td class="px-4 py-3 text-center text-sm text-zinc-600 dark:text-zinc-400">
                                                {{ number_format($item->tax_rate, 0) }}%
                                            </td>
                                            <td class="px-4 py-3 text-right text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                                {{ number_format($item->total, 2, ',', ' ') }} DA
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Totals -->
                        <div class="flex justify-end">
                            <div class="w-72 space-y-2">
                                <div class="flex justify-between text-sm">
                                    <span class="text-zinc-500">Sous-total HT</span>
                                    <span class="text-zinc-900 dark:text-zinc-100">{{ number_format($invoice->subtotal, 2, ',', ' ') }} DA</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-zinc-500">TVA</span>
                                    <span class="text-zinc-900 dark:text-zinc-100">{{ number_format($invoice->tax_amount, 2, ',', ' ') }} DA</span>
                                </div>
                                @if($invoice->discount_amount > 0)
                                    <div class="flex justify-between text-sm">
                                        <span class="text-zinc-500">Remise</span>
                                        <span class="text-green-600">-{{ number_format($invoice->discount_amount, 2, ',', ' ') }} DA</span>
                                    </div>
                                @endif
                                <div class="border-t pt-2 flex justify-between text-lg font-bold">
                                    <span>Total TTC</span>
                                    <span class="text-zinc-900 dark:text-zinc-100">{{ number_format($invoice->total, 2, ',', ' ') }} DA</span>
                                </div>
                                @if($invoice->amount_paid > 0)
                                    <div class="flex justify-between text-sm">
                                        <span class="text-green-600">Payé</span>
                                        <span class="text-green-600">-{{ number_format($invoice->amount_paid, 2, ',', ' ') }} DA</span>
                                    </div>
                                    <div class="flex justify-between text-lg font-bold">
                                        <span>Reste à payer</span>
                                        <span class="text-orange-600">{{ number_format($invoice->amount_due, 2, ',', ' ') }} DA</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Notes -->
                        @if($invoice->notes || $invoice->terms)
                            <div class="mt-8 pt-8 border-t border-zinc-200 dark:border-zinc-700">
                                @if($invoice->notes)
                                    <div class="mb-4">
                                        <div class="text-sm font-medium text-zinc-500 mb-1">Notes</div>
                                        <div class="text-sm text-zinc-600 dark:text-zinc-400">{{ $invoice->notes }}</div>
                                    </div>
                                @endif
                                @if($invoice->terms)
                                    <div>
                                        <div class="text-sm font-medium text-zinc-500 mb-1">Conditions</div>
                                        <div class="text-sm text-zinc-600 dark:text-zinc-400">{{ $invoice->terms }}</div>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right Sidebar -->
            <div class="space-y-6">
                
                <!-- Payment Summary -->
                <div class="bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl shadow-lg p-6 text-white">
                    <h3 class="font-bold text-lg mb-4">Récapitulatif</h3>
                    
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-blue-100">Total facture</span>
                            <span class="font-semibold">{{ number_format($invoice->total, 2, ',', ' ') }} DA</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-blue-100">Montant payé</span>
                            <span class="font-semibold">{{ number_format($invoice->amount_paid, 2, ',', ' ') }} DA</span>
                        </div>
                        <div class="border-t border-white/20 pt-3 flex justify-between text-lg">
                            <span>Reste à payer</span>
                            <span class="font-bold">{{ number_format($invoice->amount_due, 2, ',', ' ') }} DA</span>
                        </div>
                    </div>
                    
                    @if($invoice->status !== 'paid' && $invoice->status !== 'cancelled')
                        <div class="mt-6 pt-4 border-t border-white/20">
                            <form action="{{ route('invoices.mark-as-paid', $invoice) }}" method="POST" class="space-y-3"
                                x-data="{ showForm: false }">
                                @csrf
                                <template x-if="!showForm">
                                    <button type="button" @click="showForm = true" 
                                        class="w-full py-3 bg-white text-blue-600 font-semibold rounded-lg hover:bg-blue-50 transition-colors">
                                        Marquer comme payée
                                    </button>
                                </template>
                                <template x-if="showForm">
                                    <div class="space-y-3">
                                        <select name="payment_method" required 
                                            class="w-full rounded-lg border-0 text-zinc-900 text-sm">
                                            <option value="">Mode de paiement</option>
                                            <option value="cash">Espèces</option>
                                            <option value="transfer">Virement bancaire</option>
                                            <option value="check">Chèque</option>
                                            <option value="card">Carte bancaire</option>
                                            <option value="ccp">CCP</option>
                                        </select>
                                        <input type="text" name="payment_reference" placeholder="Référence (optionnel)" 
                                            class="w-full rounded-lg border-0 text-zinc-900 text-sm" />
                                        <button type="submit" 
                                            class="w-full py-2 bg-white text-blue-600 font-semibold rounded-lg hover:bg-blue-50">
                                            Confirmer le paiement
                                        </button>
                                    </div>
                                </template>
                            </form>
                        </div>
                    @endif
                </div>

                <!-- Client Info -->
                <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm p-6">
                    <h3 class="font-semibold text-zinc-900 dark:text-zinc-100 mb-4">Client</h3>
                    <a href="{{ route('contacts.show', $invoice->contact) }}" class="flex items-center gap-4 group">
                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-500 to-purple-500 flex items-center justify-center text-white font-bold">
                            {{ strtoupper(substr($invoice->contact->nom, 0, 1) . substr($invoice->contact->prenom, 0, 1)) }}
                        </div>
                        <div>
                            <div class="font-medium text-zinc-900 dark:text-zinc-100 group-hover:text-blue-600">
                                {{ $invoice->contact->nom }} {{ $invoice->contact->prenom }}
                            </div>
                            @if($invoice->contact->email)
                                <div class="text-sm text-zinc-500">{{ $invoice->contact->email }}</div>
                            @endif
                        </div>
                    </a>
                </div>

                <!-- Subscription Link -->
                @if($invoice->subscription)
                    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm p-6">
                        <h3 class="font-semibold text-zinc-900 dark:text-zinc-100 mb-4">Abonnement lié</h3>
                        <a href="{{ route('subscriptions.show', $invoice->subscription) }}" 
                            class="block p-4 bg-zinc-50 dark:bg-zinc-800 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-700 transition-colors">
                            <div class="flex items-center gap-2">
                                <flux:icon.arrow-path class="size-4 text-green-500" />
                                <span class="font-medium text-zinc-900 dark:text-zinc-100">{{ $invoice->subscription->name }}</span>
                            </div>
                            <div class="text-sm text-zinc-500 mt-1">{{ $invoice->subscription->billing_cycle_label }}</div>
                        </a>
                    </div>
                @endif

                <!-- Payment Info -->
                @if($invoice->status === 'paid')
                    <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl p-6">
                        <div class="flex items-center gap-3 mb-4">
                            <flux:icon.check-circle class="size-6 text-green-600" />
                            <h3 class="font-semibold text-green-800 dark:text-green-200">Payée</h3>
                        </div>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-green-700 dark:text-green-300">Date de paiement</span>
                                <span class="font-medium text-green-800 dark:text-green-200">{{ $invoice->paid_date->format('d/m/Y') }}</span>
                            </div>
                            @if($invoice->payment_method)
                                <div class="flex justify-between">
                                    <span class="text-green-700 dark:text-green-300">Mode</span>
                                    <span class="font-medium text-green-800 dark:text-green-200">{{ $invoice->payment_method_label }}</span>
                                </div>
                            @endif
                            @if($invoice->payment_reference)
                                <div class="flex justify-between">
                                    <span class="text-green-700 dark:text-green-300">Référence</span>
                                    <span class="font-medium text-green-800 dark:text-green-200">{{ $invoice->payment_reference }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Quick Actions -->
                <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm p-6">
                    <h4 class="font-semibold text-zinc-900 dark:text-zinc-100 mb-4">Actions</h4>
                    <div class="space-y-2">
                        <flux:button class="w-full" icon="arrow-down-tray" href="{{ route('invoices.download', $invoice) }}">
                            Télécharger PDF
                        </flux:button>
                        <flux:button class="w-full" variant="ghost" icon="document-duplicate" href="{{ route('invoices.duplicate', $invoice) }}">
                            Dupliquer
                        </flux:button>
                        @if($invoice->status === 'draft')
                            <form action="{{ route('invoices.send', $invoice) }}" method="POST">
                                @csrf
                                <flux:button class="w-full" type="submit" icon="paper-airplane">
                                    Envoyer
                                </flux:button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
