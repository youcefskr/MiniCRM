<x-layouts.app title="Factures">
    <div class="flex h-full flex-col gap-6">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <flux:heading size="xl">Factures</flux:heading>
                <flux:subheading>Gérez vos factures et suivez les paiements</flux:subheading>
            </div>
            <div class="flex gap-2">
                <flux:button icon="arrow-down-tray" variant="ghost" href="{{ route('invoices.export') }}">
                    Exporter
                </flux:button>
                <flux:button icon="plus" variant="primary" href="{{ route('invoices.create') }}">
                    Nouvelle facture
                </flux:button>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-8 gap-4">
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-4 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-zinc-100 dark:bg-zinc-800 rounded-lg">
                        <flux:icon.document-text class="size-5 text-zinc-600" />
                    </div>
                    <div>
                        <div class="text-2xl font-bold">{{ $stats['total'] }}</div>
                        <div class="text-xs text-zinc-500">Total</div>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-4 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-zinc-100 dark:bg-zinc-800 rounded-lg">
                        <flux:icon.pencil class="size-5 text-zinc-600" />
                    </div>
                    <div>
                        <div class="text-2xl font-bold">{{ $stats['draft'] }}</div>
                        <div class="text-xs text-zinc-500">Brouillons</div>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-4 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                        <flux:icon.paper-airplane class="size-5 text-blue-600" />
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-blue-600">{{ $stats['sent'] }}</div>
                        <div class="text-xs text-zinc-500">Envoyées</div>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-4 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-green-100 dark:bg-green-900/30 rounded-lg">
                        <flux:icon.check-circle class="size-5 text-green-600" />
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-green-600">{{ $stats['paid'] }}</div>
                        <div class="text-xs text-zinc-500">Payées</div>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-4 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-red-100 dark:bg-red-900/30 rounded-lg">
                        <flux:icon.exclamation-circle class="size-5 text-red-600" />
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-red-600">{{ $stats['overdue'] }}</div>
                        <div class="text-xs text-zinc-500">En retard</div>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-4 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
                        <flux:icon.calendar class="size-5 text-purple-600" />
                    </div>
                    <div>
                        <div class="text-lg font-bold text-purple-600">{{ number_format($stats['total_this_month'], 0, ',', ' ') }}</div>
                        <div class="text-xs text-zinc-500">Ce mois (DA)</div>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-4 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-green-100 dark:bg-green-900/30 rounded-lg">
                        <flux:icon.banknotes class="size-5 text-green-600" />
                    </div>
                    <div>
                        <div class="text-lg font-bold text-green-600">{{ number_format($stats['total_paid_this_month'], 0, ',', ' ') }}</div>
                        <div class="text-xs text-zinc-500">Encaissé (DA)</div>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-4 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-orange-100 dark:bg-orange-900/30 rounded-lg">
                        <flux:icon.clock class="size-5 text-orange-600" />
                    </div>
                    <div>
                        <div class="text-lg font-bold text-orange-600">{{ number_format($stats['total_unpaid'], 0, ',', ' ') }}</div>
                        <div class="text-xs text-zinc-500">Impayé (DA)</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-4 shadow-sm">
            <form method="GET" action="{{ route('invoices.index') }}" class="flex flex-wrap items-end gap-4">
                <div class="flex-1 min-w-[200px]">
                    <flux:input icon="magnifying-glass" name="search" placeholder="Rechercher par numéro ou client..." 
                        value="{{ request('search') }}" />
                </div>
                <div class="w-36">
                    <flux:select name="status">
                        <option value="">Tous les statuts</option>
                        <option value="draft" @selected(request('status') == 'draft')>Brouillon</option>
                        <option value="sent" @selected(request('status') == 'sent')>Envoyée</option>
                        <option value="paid" @selected(request('status') == 'paid')>Payée</option>
                        <option value="partial" @selected(request('status') == 'partial')>Paiement partiel</option>
                        <option value="overdue" @selected(request('status') == 'overdue')>En retard</option>
                        <option value="cancelled" @selected(request('status') == 'cancelled')>Annulée</option>
                    </flux:select>
                </div>
                <div class="w-40">
                    <flux:select name="contact_id">
                        <option value="">Tous les clients</option>
                        @foreach($contacts as $contact)
                            <option value="{{ $contact->id }}" @selected(request('contact_id') == $contact->id)>
                                {{ $contact->nom }} {{ $contact->prenom }}
                            </option>
                        @endforeach
                    </flux:select>
                </div>
                <div class="w-36">
                    <flux:input type="date" name="date_from" value="{{ request('date_from') }}" placeholder="Du" />
                </div>
                <div class="w-36">
                    <flux:input type="date" name="date_to" value="{{ request('date_to') }}" placeholder="Au" />
                </div>
                <flux:button type="submit" variant="primary" icon="funnel">Filtrer</flux:button>
                <flux:button type="button" variant="ghost" onclick="window.location='{{ route('invoices.index') }}'">
                    Réinitialiser
                </flux:button>
            </form>
        </div>

        <!-- Invoices Table -->
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-800">
                    <thead class="bg-zinc-50 dark:bg-zinc-800/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase">N° Facture</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase">Client</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase">Échéance</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-zinc-500 uppercase">Montant</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-zinc-500 uppercase">Statut</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-zinc-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                        @forelse($invoices as $invoice)
                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="{{ route('invoices.show', $invoice) }}" class="group">
                                        <div class="text-sm font-medium text-blue-600 group-hover:underline">
                                            {{ $invoice->invoice_number }}
                                        </div>
                                        @if($invoice->subscription)
                                            <div class="text-xs text-zinc-500 flex items-center gap-1">
                                                <flux:icon.arrow-path class="size-3" />
                                                {{ Str::limit($invoice->subscription->name, 20) }}
                                            </div>
                                        @endif
                                    </a>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="{{ route('contacts.show', $invoice->contact) }}" class="flex items-center gap-3 group">
                                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-purple-500 flex items-center justify-center text-white text-xs font-bold">
                                            {{ strtoupper(substr($invoice->contact->nom, 0, 1) . substr($invoice->contact->prenom, 0, 1)) }}
                                        </div>
                                        <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100 group-hover:text-blue-600">
                                            {{ $invoice->contact->nom }} {{ $invoice->contact->prenom }}
                                        </div>
                                    </a>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-600 dark:text-zinc-400">
                                    {{ $invoice->issue_date->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-zinc-600 dark:text-zinc-400">
                                        {{ $invoice->due_date->format('d/m/Y') }}
                                    </div>
                                    @if($invoice->is_overdue)
                                        <div class="text-xs text-red-600 font-medium">
                                            {{ $invoice->days_overdue }} jours de retard
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">
                                        {{ number_format($invoice->total, 2, ',', ' ') }} DA
                                    </div>
                                    @if($invoice->amount_paid > 0 && $invoice->amount_paid < $invoice->total)
                                        <div class="text-xs text-green-600">
                                            Payé: {{ number_format($invoice->amount_paid, 2, ',', ' ') }} DA
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @php
                                        $statusColors = [
                                            'draft' => 'bg-zinc-100 text-zinc-800 dark:bg-zinc-800 dark:text-zinc-300',
                                            'sent' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
                                            'paid' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                                            'partial' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
                                            'overdue' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
                                            'cancelled' => 'bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400',
                                        ];
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$invoice->status] }}">
                                        {{ $invoice->status_label }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <flux:dropdown>
                                        <flux:button variant="ghost" size="sm" icon="ellipsis-vertical" />
                                        <flux:menu>
                                            <flux:menu.item icon="eye" href="{{ route('invoices.show', $invoice) }}">
                                                Voir
                                            </flux:menu.item>
                                            @if($invoice->status !== 'paid')
                                                <flux:menu.item icon="pencil" href="{{ route('invoices.edit', $invoice) }}">
                                                    Modifier
                                                </flux:menu.item>
                                            @endif
                                            <flux:menu.item icon="arrow-down-tray" href="{{ route('invoices.download', $invoice) }}">
                                                Télécharger PDF
                                            </flux:menu.item>
                                            <flux:menu.item icon="document-duplicate" href="{{ route('invoices.duplicate', $invoice) }}">
                                                Dupliquer
                                            </flux:menu.item>
                                            @if($invoice->status === 'draft')
                                                <form action="{{ route('invoices.send', $invoice) }}" method="POST" class="contents">
                                                    @csrf
                                                    <flux:menu.item icon="paper-airplane" type="submit">
                                                        Envoyer
                                                    </flux:menu.item>
                                                </form>
                                            @endif
                                            @if(!in_array($invoice->status, ['paid', 'cancelled']))
                                                <flux:menu.separator />
                                                <flux:menu.item icon="banknotes" 
                                                    x-data 
                                                    @click="$dispatch('open-payment-modal', { invoiceId: {{ $invoice->id }}, amount: {{ $invoice->amount_due }} })">
                                                    Enregistrer paiement
                                                </flux:menu.item>
                                            @endif
                                            @if($invoice->status !== 'paid')
                                                <flux:menu.separator />
                                                <form action="{{ route('invoices.cancel', $invoice) }}" method="POST" class="contents"
                                                    onsubmit="return confirm('Annuler cette facture ?')">
                                                    @csrf
                                                    <flux:menu.item icon="x-circle" variant="danger" type="submit">
                                                        Annuler
                                                    </flux:menu.item>
                                                </form>
                                            @endif
                                        </flux:menu>
                                    </flux:dropdown>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <flux:icon.document-text class="size-12 text-zinc-300 dark:text-zinc-600 mb-4" />
                                        <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100 mb-1">Aucune facture</h3>
                                        <p class="text-zinc-500 mb-4">Créez votre première facture</p>
                                        <flux:button icon="plus" variant="primary" href="{{ route('invoices.create') }}">
                                            Créer une facture
                                        </flux:button>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($invoices->hasPages())
                <div class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-800">
                    {{ $invoices->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
