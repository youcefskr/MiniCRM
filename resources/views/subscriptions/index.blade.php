<x-layouts.app title="Abonnements">
    <div class="flex h-full flex-col gap-6">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <flux:heading size="xl">Abonnements</flux:heading>
                <flux:subheading>Gérez les abonnements et services récurrents de vos clients</flux:subheading>
            </div>
            <div class="flex gap-2">
                <flux:button icon="arrow-down-tray" variant="ghost" href="{{ route('subscriptions.export') }}">
                    Exporter
                </flux:button>
                <flux:button icon="plus" variant="primary" href="{{ route('subscriptions.create') }}">
                    Nouvel abonnement
                </flux:button>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-4">
            <!-- Total -->
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-4 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-zinc-100 dark:bg-zinc-800 rounded-lg">
                        <flux:icon.document-text class="size-5 text-zinc-600 dark:text-zinc-400" />
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ $stats['total'] }}</div>
                        <div class="text-xs text-zinc-500">Total</div>
                    </div>
                </div>
            </div>

            <!-- Actifs -->
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-4 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-green-100 dark:bg-green-900/30 rounded-lg">
                        <flux:icon.check-circle class="size-5 text-green-600 dark:text-green-400" />
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-green-600">{{ $stats['active'] }}</div>
                        <div class="text-xs text-zinc-500">Actifs</div>
                    </div>
                </div>
            </div>

            <!-- En attente -->
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-4 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg">
                        <flux:icon.clock class="size-5 text-yellow-600 dark:text-yellow-400" />
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-yellow-600">{{ $stats['pending'] }}</div>
                        <div class="text-xs text-zinc-500">En attente</div>
                    </div>
                </div>
            </div>

            <!-- Expirés -->
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-4 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-red-100 dark:bg-red-900/30 rounded-lg">
                        <flux:icon.x-circle class="size-5 text-red-600 dark:text-red-400" />
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-red-600">{{ $stats['expired'] }}</div>
                        <div class="text-xs text-zinc-500">Expirés</div>
                    </div>
                </div>
            </div>

            <!-- Renouvellement proche -->
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-4 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-orange-100 dark:bg-orange-900/30 rounded-lg">
                        <flux:icon.bell-alert class="size-5 text-orange-600 dark:text-orange-400" />
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-orange-600">{{ $stats['renewal_soon'] }}</div>
                        <div class="text-xs text-zinc-500">À renouveler</div>
                    </div>
                </div>
            </div>

            <!-- MRR -->
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-4 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                        <flux:icon.currency-dollar class="size-5 text-blue-600 dark:text-blue-400" />
                    </div>
                    <div>
                        <div class="text-lg font-bold text-blue-600">{{ number_format($stats['mrr'], 0, ',', ' ') }}</div>
                        <div class="text-xs text-zinc-500">MRR (DA)</div>
                    </div>
                </div>
            </div>

            <!-- ARR -->
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-4 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
                        <flux:icon.chart-bar class="size-5 text-purple-600 dark:text-purple-400" />
                    </div>
                    <div>
                        <div class="text-lg font-bold text-purple-600">{{ number_format($stats['arr'], 0, ',', ' ') }}</div>
                        <div class="text-xs text-zinc-500">ARR (DA)</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-4 shadow-sm">
            <form method="GET" action="{{ route('subscriptions.index') }}" class="flex flex-wrap items-end gap-4">
                <div class="flex-1 min-w-[200px]">
                    <flux:input icon="magnifying-glass" name="search" placeholder="Rechercher un abonnement..." 
                        value="{{ request('search') }}" />
                </div>
                <div class="w-40">
                    <flux:select name="status">
                        <option value="">Tous les statuts</option>
                        <option value="active" @selected(request('status') == 'active')>Actif</option>
                        <option value="pending" @selected(request('status') == 'pending')>En attente</option>
                        <option value="paused" @selected(request('status') == 'paused')>Suspendu</option>
                        <option value="cancelled" @selected(request('status') == 'cancelled')>Annulé</option>
                        <option value="expired" @selected(request('status') == 'expired')>Expiré</option>
                    </flux:select>
                </div>
                <div class="w-40">
                    <flux:select name="billing_cycle">
                        <option value="">Tous les cycles</option>
                        <option value="monthly" @selected(request('billing_cycle') == 'monthly')>Mensuel</option>
                        <option value="quarterly" @selected(request('billing_cycle') == 'quarterly')>Trimestriel</option>
                        <option value="semi_annual" @selected(request('billing_cycle') == 'semi_annual')>Semestriel</option>
                        <option value="annual" @selected(request('billing_cycle') == 'annual')>Annuel</option>
                    </flux:select>
                </div>
                <div class="w-48">
                    <flux:select name="user_id">
                        <option value="">Tous les commerciaux</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" @selected(request('user_id') == $user->id)>{{ $user->name }}</option>
                        @endforeach
                    </flux:select>
                </div>
                <flux:button type="submit" variant="primary" icon="funnel">Filtrer</flux:button>
                <flux:button type="button" variant="ghost" onclick="window.location='{{ route('subscriptions.index') }}'">
                    Réinitialiser
                </flux:button>
            </form>
        </div>

        <!-- Subscriptions Table -->
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-800">
                    <thead class="bg-zinc-50 dark:bg-zinc-800/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">
                                Abonnement
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">
                                Client
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">
                                Cycle
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">
                                Montant
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">
                                Prochaine facturation
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">
                                Statut
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">
                                Commercial
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-zinc-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-zinc-900 divide-y divide-zinc-200 dark:divide-zinc-800">
                        @forelse($subscriptions as $subscription)
                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="{{ route('subscriptions.show', $subscription) }}" class="group">
                                        <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100 group-hover:text-blue-600">
                                            {{ $subscription->name }}
                                        </div>
                                        @if($subscription->description)
                                            <div class="text-xs text-zinc-500 truncate max-w-xs">{{ Str::limit($subscription->description, 40) }}</div>
                                        @endif
                                    </a>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="{{ route('contacts.show', $subscription->contact) }}" class="flex items-center gap-3 group">
                                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-purple-500 flex items-center justify-center text-white text-xs font-bold">
                                            {{ strtoupper(substr($subscription->contact->nom, 0, 1) . substr($subscription->contact->prenom, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100 group-hover:text-blue-600">
                                                {{ $subscription->contact->nom }} {{ $subscription->contact->prenom }}
                                            </div>
                                            @if($subscription->contact->entreprise)
                                                <div class="text-xs text-zinc-500">{{ $subscription->contact->entreprise }}</div>
                                            @endif
                                        </div>
                                    </a>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-zinc-100 text-zinc-800 dark:bg-zinc-800 dark:text-zinc-200">
                                        {{ $subscription->billing_cycle_label }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">
                                        {{ number_format($subscription->amount, 2, ',', ' ') }} DA
                                    </div>
                                    <div class="text-xs text-zinc-500">
                                        TTC: {{ number_format($subscription->total_with_tax, 2, ',', ' ') }} DA
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($subscription->next_billing_date)
                                        <div class="text-sm text-zinc-900 dark:text-zinc-100">
                                            {{ $subscription->next_billing_date->format('d/m/Y') }}
                                        </div>
                                        @if($subscription->is_renewal_soon)
                                            <div class="text-xs text-orange-600 font-medium">
                                                Dans {{ $subscription->days_until_renewal }} jours
                                            </div>
                                        @elseif($subscription->is_overdue)
                                            <div class="text-xs text-red-600 font-medium">
                                                En retard
                                            </div>
                                        @endif
                                    @else
                                        <span class="text-zinc-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $statusColors = [
                                            'active' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                                            'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
                                            'paused' => 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-300',
                                            'cancelled' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
                                            'expired' => 'bg-zinc-100 text-zinc-800 dark:bg-zinc-800 dark:text-zinc-300',
                                        ];
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$subscription->status] ?? $statusColors['expired'] }}">
                                        {{ $subscription->status_label }}
                                    </span>
                                    @if($subscription->auto_renew && $subscription->status === 'active')
                                        <flux:icon.arrow-path class="inline size-3 ml-1 text-green-500" title="Renouvellement automatique" />
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center text-white text-[10px] font-bold">
                                            {{ strtoupper(substr($subscription->user->name, 0, 2)) }}
                                        </div>
                                        <span class="text-sm text-zinc-600 dark:text-zinc-400">{{ $subscription->user->name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <flux:dropdown>
                                        <flux:button variant="ghost" size="sm" icon="ellipsis-vertical" />
                                        <flux:menu>
                                            <flux:menu.item icon="eye" href="{{ route('subscriptions.show', $subscription) }}">
                                                Voir
                                            </flux:menu.item>
                                            <flux:menu.item icon="pencil" href="{{ route('subscriptions.edit', $subscription) }}">
                                                Modifier
                                            </flux:menu.item>
                                            @if($subscription->status === 'active')
                                                <flux:menu.item icon="document-text" href="{{ route('subscriptions.generate-invoice', $subscription) }}">
                                                    Générer facture
                                                </flux:menu.item>
                                                <form action="{{ route('subscriptions.pause', $subscription) }}" method="POST" class="contents">
                                                    @csrf
                                                    <flux:menu.item icon="pause" type="submit">
                                                        Suspendre
                                                    </flux:menu.item>
                                                </form>
                                            @elseif($subscription->status === 'paused')
                                                <form action="{{ route('subscriptions.resume', $subscription) }}" method="POST" class="contents">
                                                    @csrf
                                                    <flux:menu.item icon="play" type="submit">
                                                        Reprendre
                                                    </flux:menu.item>
                                                </form>
                                            @endif
                                            <flux:menu.separator />
                                            @if($subscription->status !== 'cancelled')
                                                <form action="{{ route('subscriptions.cancel', $subscription) }}" method="POST" class="contents" 
                                                    onsubmit="return confirm('Êtes-vous sûr de vouloir annuler cet abonnement ?')">
                                                    @csrf
                                                    <flux:menu.item icon="x-circle" variant="danger" type="submit">
                                                        Annuler l'abonnement
                                                    </flux:menu.item>
                                                </form>
                                            @endif
                                            <form action="{{ route('subscriptions.destroy', $subscription) }}" method="POST" class="contents"
                                                onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet abonnement ?')">
                                                @csrf
                                                @method('DELETE')
                                                <flux:menu.item icon="trash" variant="danger" type="submit">
                                                    Supprimer
                                                </flux:menu.item>
                                            </form>
                                        </flux:menu>
                                    </flux:dropdown>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <flux:icon.document-text class="size-12 text-zinc-300 dark:text-zinc-600 mb-4" />
                                        <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100 mb-1">Aucun abonnement</h3>
                                        <p class="text-zinc-500 mb-4">Commencez par créer votre premier abonnement</p>
                                        <flux:button icon="plus" variant="primary" href="{{ route('subscriptions.create') }}">
                                            Créer un abonnement
                                        </flux:button>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($subscriptions->hasPages())
                <div class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-800">
                    {{ $subscriptions->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
