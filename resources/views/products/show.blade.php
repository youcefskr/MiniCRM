<x-layouts.app :title="$product->name">
    <div class="p-6 space-y-6">
        <!-- Breadcrumb -->
        <div class="flex items-center gap-2 text-sm text-zinc-600 dark:text-zinc-400">
            <a href="{{ route('admin.products.index') }}" class="hover:text-zinc-900 dark:hover:text-zinc-100">Produits</a>
            <flux:icon.chevron-right class="size-4" />
            <span class="text-zinc-900 dark:text-zinc-100">{{ $product->name }}</span>
        </div>

        <!-- En-tête -->
        <div class="flex justify-between items-start">
            <div class="flex items-start gap-4">
                @if($product->image)
                    <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}" class="h-24 w-24 rounded-xl object-cover border border-zinc-200 dark:border-zinc-700">
                @else
                    <div class="h-24 w-24 rounded-xl bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center border border-zinc-200 dark:border-zinc-700">
                        <flux:icon.cube class="size-12 text-zinc-400" />
                    </div>
                @endif
                <div>
                    <flux:heading size="xl">{{ $product->name }}</flux:heading>
                    <p class="text-zinc-600 dark:text-zinc-400 mt-1">{{ $product->brand }}</p>
                    <div class="flex items-center gap-2 mt-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            {{ $product->category->name }}
                        </span>
                        <span class="text-sm font-mono text-zinc-500">{{ $product->code }}</span>
                    </div>
                </div>
            </div>
            <div class="flex gap-2">
                <flux:button href="{{ route('admin.products.index') }}" variant="ghost" icon="arrow-left">
                    Retour
                </flux:button>
            </div>
        </div>

        <!-- Statistiques -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl p-6">
                <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Prix unitaire</p>
                <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100 mt-2">{{ $product->formatted_price }}</p>
            </div>

            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl p-6">
                <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Stock actuel</p>
                <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100 mt-2">
                    @if($product->type === 'service')
                        N/A
                    @else
                        {{ $product->stock_quantity }}
                    @endif
                </p>
            </div>

            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl p-6">
                <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Quantités vendues</p>
                <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100 mt-2">{{ $stats['total_sold'] }}</p>
            </div>

            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl p-6">
                <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Chiffre d'affaires</p>
                <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100 mt-2">{{ number_format($stats['revenue'], 2, ',', ' ') }} €</p>
            </div>
        </div>

        <!-- Informations détaillées -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Détails du produit -->
            <div class="md:col-span-2 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl p-6">
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">Détails du produit</h3>
                
                <dl class="space-y-3">
                    <div class="flex justify-between">
                        <dt class="text-sm text-zinc-600 dark:text-zinc-400">Type</dt>
                        <dd class="text-sm font-medium text-zinc-900 dark:text-zinc-100">
                            @if($product->type === 'product') Produit physique
                            @elseif($product->type === 'service') Service
                            @else Abonnement
                            @endif
                        </dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-zinc-600 dark:text-zinc-400">Statut</dt>
                        <dd class="text-sm font-medium text-zinc-900 dark:text-zinc-100">
                            @if($product->is_active)
                                <span class="text-green-600">Actif</span>
                            @else
                                <span class="text-gray-600">Inactif</span>
                            @endif
                        </dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-zinc-600 dark:text-zinc-400">Statut stock</dt>
                        <dd class="text-sm font-medium">
                            <span class="
                                @if($product->stock_status === 'Rupture') text-red-600
                                @elseif($product->stock_status === 'Stock faible') text-orange-600
                                @else text-green-600
                                @endif
                            ">{{ $product->stock_status }}</span>
                        </dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-zinc-600 dark:text-zinc-400">Créé le</dt>
                        <dd class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $product->created_at->format('d/m/Y à H:i') }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-zinc-600 dark:text-zinc-400">Dernière modification</dt>
                        <dd class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $product->updated_at->format('d/m/Y à H:i') }}</dd>
                    </div>
                </dl>

                @if($product->description)
                    <div class="mt-6 pt-6 border-t border-zinc-200 dark:border-zinc-700">
                        <h4 class="text-sm font-medium text-zinc-900 dark:text-zinc-100 mb-2">Description</h4>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ $product->description }}</p>
                    </div>
                @endif
            </div>

            <!-- Actions rapides -->
            <div class="space-y-4">
                <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl p-6">
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">Actions rapides</h3>
                    <div class="space-y-2">
                        <flux:button class="w-full justify-start" variant="ghost" icon="pencil">
                            Modifier le produit
                        </flux:button>
                        <flux:button class="w-full justify-start" variant="ghost" icon="arrow-path">
                            Ajuster le stock
                        </flux:button>
                        <flux:button class="w-full justify-start" variant="ghost" icon="document-duplicate">
                            Dupliquer
                        </flux:button>
                        <flux:button class="w-full justify-start" variant="danger" icon="trash">
                            Supprimer
                        </flux:button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Opportunités liées -->
        @if($product->opportunities->count() > 0)
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl p-6">
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">
                    Opportunités liées ({{ $product->opportunities->count() }})
                </h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                        <thead>
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-zinc-500 uppercase">Opportunité</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-zinc-500 uppercase">Contact</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-zinc-500 uppercase">Quantité</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-zinc-500 uppercase">Prix unitaire</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-zinc-500 uppercase">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                            @foreach($product->opportunities as $opportunity)
                                <tr>
                                    <td class="px-4 py-3 text-sm">
                                        <a href="{{ route('opportunities.show', $opportunity) }}" class="text-indigo-600 hover:text-indigo-800">
                                            {{ $opportunity->title }}
                                        </a>
                                    </td>
                                    <td class="px-4 py-3 text-sm">{{ $opportunity->contact->full_name }}</td>
                                    <td class="px-4 py-3 text-sm">{{ $opportunity->pivot->quantity }}</td>
                                    <td class="px-4 py-3 text-sm">{{ number_format($opportunity->pivot->unit_price, 2, ',', ' ') }} €</td>
                                    <td class="px-4 py-3 text-sm font-semibold">{{ number_format($opportunity->pivot->total_price, 2, ',', ' ') }} €</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</x-layouts.app>
