<x-layouts.app :title="__('Gestion des produits')">
    <div class="p-6 space-y-6" x-data="{ 
        showCreateModal: false,
        showEditModal: false,
        showDeleteModal: false,
        selectedProduct: null,
        search: '{{ request('search') }}',
        selectedCategory: '{{ request('category') }}',
        selectedType: '{{ request('type') }}',
        selectedStock: '{{ request('stock') }}',
        products: {{ Js::from($products->items()) }},
        categories: {{ Js::from($categories) }},

        initProduct(product) {
            this.selectedProduct = product;
        },

        getTypeLabel(type) {
            const types = {
                'product': 'Produit',
                'service': 'Service',
                'subscription': 'Abonnement'
            };
            return types[type] || type;
        },

        getStockBadgeClass(product) {
            if (product.type === 'service') return 'bg-gray-100 text-gray-800';
            if (product.stock_quantity <= 0) return 'bg-red-100 text-red-800';
            if (product.stock_quantity < 10) return 'bg-orange-100 text-orange-800';
            return 'bg-green-100 text-green-800';
        }
    }">
        <!-- En-tête -->
        <div class="flex justify-between items-center">
            <flux:heading size="xl">Gestion des produits</flux:heading>
            <div class="flex gap-2">
                <flux:button 
                    href="{{ route('admin.categories.index') }}"
                    variant="ghost"
                    icon="tag">
                    Catégories
                </flux:button>
            </div>
        </div>

        <!-- Statistiques -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Total produits</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ $stats['total'] }}</p>
                    </div>
                    <flux:icon.cube class="size-8 text-blue-500" />
                </div>
            </div>

            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Actifs</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ $stats['active'] }}</p>
                    </div>
                    <flux:icon.check-circle class="size-8 text-green-500" />
                </div>
            </div>

            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">En stock</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ $stats['in_stock'] }}</p>
                    </div>
                    <flux:icon.archive-box class="size-8 text-orange-500" />
                </div>
            </div>

            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Valeur totale</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ number_format($stats['total_value'], 2, ',', ' ') }} €</p>
                    </div>
                    <flux:icon.currency-euro class="size-8 text-purple-500" />
                </div>
            </div>
        </div>

        <!-- Filtres et recherche -->
        <form method="GET" action="{{ route('admin.products.index') }}" id="filterForm" 
              class="p-6 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl shadow-sm">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}"
                           placeholder="Rechercher un produit..."
                           class="w-full rounded-lg border-zinc-300 bg-white text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 pl-10"
                           x-data
                           @input.debounce.500ms="$el.form.submit()">
                    <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none" style="position: relative; margin-top: -34px; margin-left: 10px;">
                        <svg class="size-4 text-zinc-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                        </svg>
                    </div>
                </div>
                
                <div>
                    <select name="category" 
                            onchange="this.form.submit()"
                            class="w-full rounded-lg border-zinc-300 bg-white text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100">
                        <option value="">Toutes les catégories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" @selected(request('category') == $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <select name="type" 
                            onchange="this.form.submit()"
                            class="w-full rounded-lg border-zinc-300 bg-white text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100">
                        <option value="">Tous les types</option>
                        <option value="product" @selected(request('type') == 'product')>Produit</option>
                        <option value="service" @selected(request('type') == 'service')>Service</option>
                        <option value="subscription" @selected(request('type') == 'subscription')>Abonnement</option>
                    </select>
                </div>

                <div>
                    <select name="stock" 
                            onchange="this.form.submit()"
                            class="w-full rounded-lg border-zinc-300 bg-white text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100">
                        <option value="">Tous les stocks</option>
                        <option value="in_stock" @selected(request('stock') == 'in_stock')>En stock</option>
                        <option value="out_of_stock" @selected(request('stock') == 'out_of_stock')>Rupture</option>
                    </select>
                </div>

                <div class="flex justify-end gap-2">
                    <a href="{{ route('admin.products.export', request()->query()) }}" 
                       class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-zinc-700 bg-white border border-zinc-300 rounded-lg hover:bg-zinc-50 dark:bg-zinc-800 dark:text-zinc-300 dark:border-zinc-700 dark:hover:bg-zinc-700">
                        <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                        </svg>
                        Export CSV
                    </a>
                    <flux:button 
                        type="button"
                        @click="showCreateModal = true"
                        variant="primary"
                        icon="plus">
                        Nouveau produit
                    </flux:button>
                </div>
            </div>
            
            @if(request()->hasAny(['search', 'category', 'type', 'stock']))
                <div class="mt-3 flex items-center gap-2">
                    <span class="text-xs text-zinc-500">Filtres actifs:</span>
                    @if(request('search'))
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-300">
                            "{{ request('search') }}"
                        </span>
                    @endif
                    @if(request('category'))
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
                            {{ $categories->find(request('category'))?->name ?? 'Catégorie' }}
                        </span>
                    @endif
                    @if(request('type'))
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300">
                            {{ request('type') == 'product' ? 'Produit' : (request('type') == 'service' ? 'Service' : 'Abonnement') }}
                        </span>
                    @endif
                    @if(request('stock'))
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">
                            {{ request('stock') == 'in_stock' ? 'En stock' : 'Rupture' }}
                        </span>
                    @endif
                    <a href="{{ route('admin.products.index') }}" class="text-xs text-red-600 hover:text-red-800 dark:text-red-400">
                        ✕ Effacer tout
                    </a>
                </div>
            @endif
        </form>


        <!-- Table des produits -->
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                    <thead class="bg-zinc-50 dark:bg-zinc-800">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider dark:text-zinc-400">Produit</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider dark:text-zinc-400">Code</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider dark:text-zinc-400">Catégorie</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider dark:text-zinc-400">Type</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider dark:text-zinc-400">Prix</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider dark:text-zinc-400">Stock</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider dark:text-zinc-400">Statut</th>
                            <th scope="col" class="relative px-6 py-3">
                                <span class="sr-only">Actions</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-zinc-200 dark:bg-zinc-900 dark:divide-zinc-700">
                        @foreach($products as $product)
                        <tr>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    @if($product->image)
                                        <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}" class="h-10 w-10 rounded-lg object-cover">
                                    @else
                                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-zinc-100 dark:bg-zinc-800">
                                            <flux:icon.cube class="size-5 text-zinc-600" />
                                        </div>
                                    @endif
                                    <div>
                                        <div class="font-medium text-zinc-900 dark:text-zinc-100">{{ $product->name }}</div>
                                        <div class="text-sm text-zinc-500">{{ $product->brand }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-mono text-zinc-600 dark:text-zinc-400">{{ $product->code ?? '-' }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm text-zinc-600 dark:text-zinc-400">{{ $product->category->name }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    @if($product->type === 'product') Produit
                                    @elseif($product->type === 'service') Service
                                    @else Abonnement
                                    @endif
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">{{ number_format($product->price, 2, ',', ' ') }} €</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($product->type === 'service')
                                    <span class="text-sm text-zinc-500">N/A</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($product->stock_quantity <= 0) bg-red-100 text-red-800
                                        @elseif($product->stock_quantity < 10) bg-orange-100 text-orange-800
                                        @else bg-green-100 text-green-800
                                        @endif">
                                        {{ $product->stock_quantity }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($product->is_active)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Actif
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        Inactif
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end gap-2">
                                    <flux:button 
                                        href="{{ route('admin.products.show', $product) }}"
                                        size="sm"
                                        variant="ghost"
                                        icon="eye" />
                                    <flux:button 
                                        @click="initProduct({{ Js::from($product) }}); showEditModal = true"
                                        size="sm"
                                        variant="ghost"
                                        icon="pencil" />
                                    <flux:button 
                                        @click="initProduct({{ Js::from($product) }}); showDeleteModal = true"
                                        size="sm"
                                        variant="danger"
                                        icon="trash" />
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-700">
                {{ $products->links() }}
            </div>
        </div>

        <!-- Modals -->
        @include('products.modals.create')
        @include('products.modals.edit')
        @include('products.modals.delete')
    </div>
</x-layouts.app>
