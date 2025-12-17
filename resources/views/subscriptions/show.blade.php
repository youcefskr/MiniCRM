<x-layouts.app title="Abonnement: {{ $subscription->name }}">
    <div class="flex h-full flex-col gap-6">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
            <div class="flex items-start gap-4">
                <a href="{{ route('subscriptions.index') }}" class="mt-1">
                    <flux:button variant="ghost" size="sm" icon="arrow-left" />
                </a>
                <div>
                    <div class="flex items-center gap-3">
                        <flux:heading size="xl">{{ $subscription->name }}</flux:heading>
                        @php
                            $statusColors = [
                                'active' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                                'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
                                'paused' => 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-300',
                                'cancelled' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
                                'expired' => 'bg-zinc-100 text-zinc-800 dark:bg-zinc-800 dark:text-zinc-300',
                            ];
                        @endphp
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $statusColors[$subscription->status] }}">
                            {{ $subscription->status_label }}
                        </span>
                    </div>
                    <flux:subheading class="flex items-center gap-2 mt-1">
                        <span>{{ $subscription->billing_cycle_label }}</span>
                        <span class="text-zinc-300">•</span>
                        <span>Créé le {{ $subscription->created_at->format('d/m/Y') }}</span>
                        @if($subscription->auto_renew)
                            <span class="text-zinc-300">•</span>
                            <span class="text-green-600 flex items-center gap-1">
                                <flux:icon.arrow-path class="size-4" />
                                Renouvellement auto
                            </span>
                        @endif
                    </flux:subheading>
                </div>
            </div>
            <div class="flex gap-2">
                @if($subscription->status === 'active')
                    <flux:button icon="document-text" variant="primary" href="{{ route('subscriptions.generate-invoice', $subscription) }}">
                        Générer facture
                    </flux:button>
                    <form action="{{ route('subscriptions.pause', $subscription) }}" method="POST">
                        @csrf
                        <flux:button icon="pause" variant="ghost" type="submit">Suspendre</flux:button>
                    </form>
                @elseif($subscription->status === 'paused')
                    <form action="{{ route('subscriptions.resume', $subscription) }}" method="POST">
                        @csrf
                        <flux:button icon="play" variant="primary" type="submit">Reprendre</flux:button>
                    </form>
                @endif
                <flux:button icon="pencil" href="{{ route('subscriptions.edit', $subscription) }}">Modifier</flux:button>
            </div>
        </div>

        <!-- Alert if renewal is soon -->
        @if($subscription->is_renewal_soon)
            <div class="bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800 rounded-xl p-4">
                <div class="flex items-center gap-3">
                    <flux:icon.bell-alert class="size-6 text-orange-600" />
                    <div>
                        <h4 class="font-medium text-orange-800 dark:text-orange-200">Renouvellement proche</h4>
                        <p class="text-sm text-orange-700 dark:text-orange-300">
                            Cet abonnement sera renouvelé dans {{ $subscription->days_until_renewal }} jours 
                            (le {{ $subscription->next_renewal_date->format('d/m/Y') }})
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Main Grid -->
        <div class="grid lg:grid-cols-3 gap-6">
            
            <!-- Left Column (2/3) -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- Subscription Details -->
                <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm overflow-hidden">
                    <div class="p-6 border-b border-zinc-200 dark:border-zinc-800">
                        <h3 class="font-semibold text-zinc-900 dark:text-zinc-100">Détails de l'abonnement</h3>
                    </div>
                    <div class="p-6">
                        <dl class="grid grid-cols-2 gap-6">
                            <div>
                                <dt class="text-sm font-medium text-zinc-500">Montant HT</dt>
                                <dd class="mt-1 text-2xl font-bold text-zinc-900 dark:text-zinc-100">
                                    {{ number_format($subscription->amount, 2, ',', ' ') }} DA
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-zinc-500">Montant TTC</dt>
                                <dd class="mt-1 text-2xl font-bold text-green-600">
                                    {{ number_format($subscription->total_with_tax, 2, ',', ' ') }} DA
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-zinc-500">Taux TVA</dt>
                                <dd class="mt-1 text-lg text-zinc-900 dark:text-zinc-100">
                                    {{ number_format($subscription->tax_rate, 0) }}%
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-zinc-500">Valeur annuelle</dt>
                                <dd class="mt-1 text-lg text-zinc-900 dark:text-zinc-100">
                                    {{ number_format($subscription->annual_value, 2, ',', ' ') }} DA
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-zinc-500">Date de début</dt>
                                <dd class="mt-1 text-lg text-zinc-900 dark:text-zinc-100">
                                    {{ $subscription->start_date->format('d/m/Y') }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-zinc-500">Prochaine facturation</dt>
                                <dd class="mt-1 text-lg text-zinc-900 dark:text-zinc-100">
                                    {{ $subscription->next_billing_date?->format('d/m/Y') ?? 'Non définie' }}
                                </dd>
                            </div>
                            @if($subscription->end_date)
                                <div>
                                    <dt class="text-sm font-medium text-zinc-500">Date de fin</dt>
                                    <dd class="mt-1 text-lg text-zinc-900 dark:text-zinc-100">
                                        {{ $subscription->end_date->format('d/m/Y') }}
                                    </dd>
                                </div>
                            @endif
                            <div>
                                <dt class="text-sm font-medium text-zinc-500">Nombre de facturations</dt>
                                <dd class="mt-1 text-lg text-zinc-900 dark:text-zinc-100">
                                    {{ $subscription->billing_count }}
                                </dd>
                            </div>
                            <div class="col-span-2">
                                <dt class="text-sm font-medium text-zinc-500">Total facturé</dt>
                                <dd class="mt-1 text-2xl font-bold text-blue-600">
                                    {{ number_format($subscription->total_billed, 2, ',', ' ') }} DA
                                </dd>
                            </div>
                        </dl>

                        @if($subscription->description)
                            <div class="mt-6 pt-6 border-t border-zinc-200 dark:border-zinc-800">
                                <dt class="text-sm font-medium text-zinc-500 mb-2">Description</dt>
                                <dd class="text-zinc-700 dark:text-zinc-300">{{ $subscription->description }}</dd>
                            </div>
                        @endif

                        @if($subscription->terms)
                            <div class="mt-4">
                                <dt class="text-sm font-medium text-zinc-500 mb-2">Conditions particulières</dt>
                                <dd class="text-zinc-700 dark:text-zinc-300">{{ $subscription->terms }}</dd>
                            </div>
                        @endif

                        @if($subscription->notes)
                            <div class="mt-4">
                                <dt class="text-sm font-medium text-zinc-500 mb-2">Notes</dt>
                                <dd class="text-zinc-700 dark:text-zinc-300">{{ $subscription->notes }}</dd>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Products/Services -->
                @if($subscription->products->count() > 0)
                    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm overflow-hidden">
                        <div class="p-6 border-b border-zinc-200 dark:border-zinc-800">
                            <h3 class="font-semibold text-zinc-900 dark:text-zinc-100">Produits/Services inclus</h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-800">
                                <thead class="bg-zinc-50 dark:bg-zinc-800/50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase">Produit</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-zinc-500 uppercase">Quantité</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-zinc-500 uppercase">Prix unitaire</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-zinc-500 uppercase">Remise</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-zinc-500 uppercase">Total</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                                    @foreach($subscription->products as $product)
                                        @php
                                            $discountedPrice = $product->pivot->unit_price * (1 - $product->pivot->discount / 100);
                                            $lineTotal = $discountedPrice * $product->pivot->quantity;
                                        @endphp
                                        <tr>
                                            <td class="px-6 py-4">
                                                <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $product->name }}</div>
                                            </td>
                                            <td class="px-6 py-4 text-center text-sm text-zinc-600 dark:text-zinc-400">
                                                {{ $product->pivot->quantity }}
                                            </td>
                                            <td class="px-6 py-4 text-right text-sm text-zinc-600 dark:text-zinc-400">
                                                {{ number_format($product->pivot->unit_price, 2, ',', ' ') }} DA
                                            </td>
                                            <td class="px-6 py-4 text-center text-sm">
                                                @if($product->pivot->discount > 0)
                                                    <span class="text-green-600">-{{ $product->pivot->discount }}%</span>
                                                @else
                                                    <span class="text-zinc-400">-</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 text-right text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                                {{ number_format($lineTotal, 2, ',', ' ') }} DA
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                <!-- Invoice History -->
                <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm overflow-hidden">
                    <div class="p-6 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between">
                        <h3 class="font-semibold text-zinc-900 dark:text-zinc-100">Historique des factures</h3>
                        @if($subscription->status === 'active')
                            <flux:button icon="plus" size="sm" href="{{ route('subscriptions.generate-invoice', $subscription) }}">
                                Générer
                            </flux:button>
                        @endif
                    </div>
                    @if($subscription->invoices->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-800">
                                <thead class="bg-zinc-50 dark:bg-zinc-800/50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase">N° Facture</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase">Date</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-zinc-500 uppercase">Montant</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-zinc-500 uppercase">Statut</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-zinc-500 uppercase">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                                    @foreach($subscription->invoices as $invoice)
                                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                                            <td class="px-6 py-4">
                                                <a href="{{ route('invoices.show', $invoice) }}" class="text-sm font-medium text-blue-600 hover:underline">
                                                    {{ $invoice->invoice_number }}
                                                </a>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-zinc-600 dark:text-zinc-400">
                                                {{ $invoice->issue_date->format('d/m/Y') }}
                                            </td>
                                            <td class="px-6 py-4 text-right text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                                {{ number_format($invoice->total, 2, ',', ' ') }} DA
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                @php
                                                    $invStatusColors = [
                                                        'draft' => 'bg-zinc-100 text-zinc-800 dark:bg-zinc-800 dark:text-zinc-300',
                                                        'sent' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
                                                        'paid' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                                                        'partial' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
                                                        'overdue' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
                                                        'cancelled' => 'bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400',
                                                    ];
                                                @endphp
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $invStatusColors[$invoice->status] }}">
                                                    {{ $invoice->status_label }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-right">
                                                <flux:button variant="ghost" size="sm" icon="eye" href="{{ route('invoices.show', $invoice) }}" />
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="p-8 text-center">
                            <flux:icon.document-text class="size-12 text-zinc-300 dark:text-zinc-600 mx-auto mb-4" />
                            <p class="text-zinc-500">Aucune facture générée pour cet abonnement</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Right Column (1/3) -->
            <div class="space-y-6">
                
                <!-- Client Info -->
                <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm p-6">
                    <h3 class="font-semibold text-zinc-900 dark:text-zinc-100 mb-4">Client</h3>
                    <a href="{{ route('contacts.show', $subscription->contact) }}" class="flex items-center gap-4 group">
                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-500 to-purple-500 flex items-center justify-center text-white font-bold">
                            {{ strtoupper(substr($subscription->contact->nom, 0, 1) . substr($subscription->contact->prenom, 0, 1)) }}
                        </div>
                        <div>
                            <div class="font-medium text-zinc-900 dark:text-zinc-100 group-hover:text-blue-600">
                                {{ $subscription->contact->nom }} {{ $subscription->contact->prenom }}
                            </div>
                            @if($subscription->contact->entreprise)
                                <div class="text-sm text-zinc-500">{{ $subscription->contact->entreprise }}</div>
                            @endif
                            @if($subscription->contact->email)
                                <div class="text-sm text-zinc-500">{{ $subscription->contact->email }}</div>
                            @endif
                        </div>
                    </a>
                </div>

                <!-- Commercial -->
                <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm p-6">
                    <h3 class="font-semibold text-zinc-900 dark:text-zinc-100 mb-4">Commercial responsable</h3>
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center text-white font-bold">
                            {{ strtoupper(substr($subscription->user->name, 0, 2)) }}
                        </div>
                        <div>
                            <div class="font-medium text-zinc-900 dark:text-zinc-100">{{ $subscription->user->name }}</div>
                            <div class="text-sm text-zinc-500">{{ $subscription->user->email }}</div>
                        </div>
                    </div>
                </div>

                <!-- Opportunity Link -->
                @if($subscription->opportunity)
                    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm p-6">
                        <h3 class="font-semibold text-zinc-900 dark:text-zinc-100 mb-4">Opportunité liée</h3>
                        <a href="{{ route('opportunities.show', $subscription->opportunity) }}" class="block p-4 bg-zinc-50 dark:bg-zinc-800 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-700 transition-colors">
                            <div class="font-medium text-zinc-900 dark:text-zinc-100">{{ $subscription->opportunity->title }}</div>
                            <div class="text-sm text-zinc-500">{{ number_format($subscription->opportunity->value, 0, ',', ' ') }} DA</div>
                        </a>
                    </div>
                @endif

                <!-- Quick Actions -->
                <div class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl shadow-md p-6 text-white">
                    <h3 class="font-bold text-lg mb-2">Actions rapides</h3>
                    <p class="text-indigo-100 text-sm mb-4">Gérez cet abonnement</p>
                    
                    <div class="space-y-2">
                        <a href="{{ route('subscriptions.edit', $subscription) }}" class="flex items-center justify-between p-3 bg-white/10 hover:bg-white/20 rounded-lg transition-colors">
                            <span class="font-medium">Modifier</span>
                            <flux:icon.pencil class="size-4" />
                        </a>
                        @if($subscription->status === 'active')
                            <a href="{{ route('subscriptions.generate-invoice', $subscription) }}" class="flex items-center justify-between p-3 bg-white/10 hover:bg-white/20 rounded-lg transition-colors">
                                <span class="font-medium">Générer facture</span>
                                <flux:icon.document-text class="size-4" />
                            </a>
                        @endif
                        <a href="{{ route('contacts.show', $subscription->contact) }}" class="flex items-center justify-between p-3 bg-white/10 hover:bg-white/20 rounded-lg transition-colors">
                            <span class="font-medium">Voir le client</span>
                            <flux:icon.user class="size-4" />
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
