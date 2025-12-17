<x-layouts.app :title="$opportunity->title">
    <div class="p-6 space-y-6">
        <!-- Breadcrumb -->
        <div class="flex items-center gap-2 text-sm text-zinc-600 dark:text-zinc-400">
            <a href="{{ route('opportunities.index') }}" class="hover:text-zinc-900 dark:hover:text-zinc-100">Opportunités</a>
            <flux:icon.chevron-right class="size-4" />
            <span class="text-zinc-900 dark:text-zinc-100">{{ $opportunity->title }}</span>
        </div>

        <!-- Header -->
        <div class="flex justify-between items-start">
            <div>
                <flux:heading size="xl">{{ $opportunity->title }}</flux:heading>
                <div class="flex items-center gap-3 mt-2">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                        @if($opportunity->stage === 'new') bg-blue-100 text-blue-800
                        @elseif($opportunity->stage === 'qualification') bg-purple-100 text-purple-800
                        @elseif($opportunity->stage === 'negotiation') bg-orange-100 text-orange-800
                        @elseif($opportunity->stage === 'proposition') bg-yellow-100 text-yellow-800
                        @elseif($opportunity->stage === 'won') bg-green-100 text-green-800
                        @else bg-red-100 text-red-800
                        @endif">
                        @switch($opportunity->stage)
                            @case('new') Prospection @break
                            @case('qualification') Qualification @break
                            @case('negotiation') Négociation @break
                            @case('proposition') Proposition @break
                            @case('won') Gagnée @break
                            @case('lost') Perdue @break
                        @endswitch
                    </span>
                    <span class="text-sm text-zinc-600 dark:text-zinc-400">
                        Probabilité: <strong>{{ $opportunity->probability }}%</strong>
                    </span>
                </div>
            </div>
            <div class="flex gap-2">
                <flux:button href="{{ route('opportunities.edit', $opportunity) }}" variant="primary" icon="pencil">
                    Modifier
                </flux:button>
                <flux:button href="{{ route('opportunities.index') }}" variant="ghost" icon="arrow-left">
                    Retour
                </flux:button>
            </div>
        </div>

        <!-- Cards de statistiques -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl p-6">
                <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Valeur estimée</p>
                <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100 mt-2">{{ number_format($opportunity->value, 2, ',', ' ') }} €</p>
            </div>

            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl p-6">
                <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Produits</p>
                <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100 mt-2">{{ $stats['products_count'] }}</p>
            </div>

            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl p-6">
                <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Total produits</p>
                <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100 mt-2">{{ number_format($stats['products_total'], 2, ',', ' ') }} €</p>
            </div>

            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl p-6">
                <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Date clôture estimée</p>
                <p class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mt-2">
                    @if($opportunity->expected_close_date)
                        {{ $opportunity->expected_close_date->format('d/m/Y') }}
                    @else
                        Non définie
                    @endif
                </p>
            </div>
        </div>

        <!-- Informations détaillées -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Détails -->
            <div class="md:col-span-2 space-y-6">
                <!-- Informations de base -->
                <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl p-6">
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">Informations</h3>
                    
                    <dl class="space-y-3">
                        <div class="flex justify-between py-2 border-b border-zinc-100 dark:border-zinc-800">
                            <dt class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Client</dt>
                            <dd class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">
                                {{ $opportunity->contact->full_name }}
                            </dd>
                        </div>
                        <div class="flex justify-between py-2 border-b border-zinc-100 dark:border-zinc-800">
                            <dt class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Commercial</dt>
                            <dd class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">
                                {{ $opportunity->user->name }}
                            </dd>
                        </div>
                        <div class="flex justify-between py-2 border-b border-zinc-100 dark:border-zinc-800">
                            <dt class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Créée le</dt>
                            <dd class="text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                {{ $opportunity->created_at->format('d/m/Y à H:i') }}
                            </dd>
                        </div>
                        <div class="flex justify-between py-2">
                            <dt class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Dernière mise à jour</dt>
                            <dd class="text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                {{ $opportunity->updated_at->format('d/m/Y à H:i') }}
                            </dd>
                        </div>
                    </dl>

                    @if($opportunity->notes)
                        <div class="mt-6 pt-6 border-t border-zinc-200 dark:border-zinc-700">
                            <h4 class="text-sm font-medium text-zinc-900 dark:text-zinc-100 mb-2">Notes</h4>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ $opportunity->notes }}</p>
                        </div>
                    @endif
                </div>

                <!-- Produits associés -->
                @if($opportunity->products->count() > 0)
                    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl p-6">
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">
                            Produits / Services ({{ $opportunity->products->count() }})
                        </h3>
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                                <thead>
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-zinc-500 uppercase">Produit</th>
                                        <th class="px-4 py-2 text-right text-xs font-medium text-zinc-500 uppercase">Qté</th>
                                        <th class="px-4 py-2 text-right text-xs font-medium text-zinc-500 uppercase">Prix unit.</th>
                                        <th class="px-4 py-2 text-right text-xs font-medium text-zinc-500 uppercase">Remise</th>
                                        <th class="px-4 py-2 text-right text-xs font-medium text-zinc-500 uppercase">Total</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                                    @foreach($opportunity->products as $product)
                                        <tr>
                                            <td class="px-4 py-3">
                                                <div class="flex items-center gap-3">
                                                    @if($product->image)
                                                        <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}" class="h-10 w-10 rounded-lg object-cover">
                                                    @else
                                                        <div class="h-10 w-10 rounded-lg bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center">
                                                            <flux:icon.cube class="size-5 text-zinc-400" />
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <div class="font-medium text-zinc-900 dark:text-zinc-100">{{ $product->name }}</div>
                                                        <div class="text-xs text-zinc-500">{{ $product->category->name }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3 text-right text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                                {{ $product->pivot->quantity }}
                                            </td>
                                            <td class="px-4 py-3 text-right text-sm text-zinc-600 dark:text-zinc-400">
                                                {{ number_format($product->pivot->unit_price, 2, ',', ' ') }} €
                                            </td>
                                            <td class="px-4 py-3 text-right text-sm text-zinc-600 dark:text-zinc-400">
                                                @if($product->pivot->discount > 0)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                                        -{{ $product->pivot->discount }}%
                                                    </span>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 text-right text-sm font-semibold text-zinc-900 dark:text-zinc-100">
                                                {{ number_format($product->pivot->total_price, 2, ',', ' ') }} €
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="border-t-2 border-zinc-300 dark:border-zinc-600">
                                    <tr>
                                        <td colspan="4" class="px-4 py-3 text-right font-semibold text-zinc-900 dark:text-zinc-100">
                                            Total
                                        </td>
                                        <td class="px-4 py-3 text-right text-lg font-bold text-zinc-900 dark:text-zinc-100">
                                            {{ number_format($stats['products_total'], 2, ',', ' ') }} €
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                 @else
                    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl p-12 text-center">
                        <svg class="size-12 mx-auto mb-3 text-zinc-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                        </svg>
                        <p class="text-zinc-600 dark:text-zinc-400 mb-4">Aucun produit associé à cette opportunité</p>
                        <flux:button href="{{ route('opportunities.edit', $opportunity) }}" variant="primary">
                            Ajouter des produits
                        </flux:button>
                    </div>
                @endif
            </div>

            <!-- Sidebar Actions -->
            <div class="space-y-4">
                <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl p-6">
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">Actions</h3>
                    <div class="space-y-2">
                        <flux:button href="{{ route('opportunities.edit', $opportunity) }}" class="w-full justify-start" variant="ghost" icon="pencil">
                            Modifier l'opportunité
                        </flux:button>
                        <flux:button class="w-full justify-start" variant="ghost" icon="document-text">
                            Générer un devis
                        </flux:button>
                        <flux:button class="w-full justify-start" variant="ghost" icon="document-duplicate">
                            Dupliquer
                        </flux:button>
                        <form action="{{ route('opportunities.destroy', $opportunity) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette opportunité ?');">
                            @csrf
                            @method('DELETE')
                            <flux:button type="submit" class="w-full justify-start" variant="danger" icon="trash">
                                Supprimer
                            </flux:button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
