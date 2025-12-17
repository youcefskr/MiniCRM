<x-layouts.app :title="__('Catégories de produits')">
    <div class="p-6 space-y-6" x-data="{ 
        showCreateModal: false,
        showEditModal: false,
        showDeleteModal: false,
        selectedCategory: null,

        initCategory(category) {
            this.selectedCategory = category;
        }
    }">
        <!-- En-tête -->
        <div class="flex justify-between items-center">
            <flux:heading size="xl">Catégories de produits</flux:heading>
            <div class="flex gap-2">
                <flux:button 
                    href="{{ route('admin.products.index') }}"
                    variant="ghost"
                    icon="arrow-left">
                    Retour aux produits
                </flux:button>
                <flux:button 
                    @click="showCreateModal = true"
                    variant="primary"
                    icon="plus">
                    Nouvelle catégorie
                </flux:button>
            </div>
        </div>

        <!-- Liste des catégories -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($categories as $category)
                <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl p-6 hover:shadow-lg transition-shadow">
                    <div class="flex justify-between items-start mb-4">
                        <div class="flex items-center gap-3">
                            <div class="h-12 w-12 rounded-lg bg-indigo-100 dark:bg-indigo-900/20 flex items-center justify-center">
                                <flux:icon.tag class="size-6 text-indigo-600 dark:text-indigo-400" />
                            </div>
                            <div>
                                <h3 class="font-semibold text-zinc-900 dark:text-zinc-100">{{ $category->name }}</h3>
                                <p class="text-sm text-zinc-500">{{ $category->products_count }} produit(s)</p>
                            </div>
                        </div>
                        <div class="flex gap-1">
                            <flux:button 
                                @click="initCategory({{ Js::from($category) }}); showEditModal = true"
                                size="sm"
                                variant="ghost"
                                icon="pencil" />
                            <flux:button 
                                @click="initCategory({{ Js::from($category) }}); showDeleteModal = true"
                                size="sm"
                                variant="danger"
                                icon="trash" />
                        </div>
                    </div>
                    
                    @if($category->description)
                        <p class="text-sm text-zinc-600 dark:text-zinc-400 line-clamp-2">{{ $category->description }}</p>
                    @endif
                </div>
            @endforeach

            @if($categories->isEmpty())
                <div class="col-span-full bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl p-12 text-center">
                    <flux:icon.tag class="size-12 text-zinc-300 mx-auto mb-4" />
                    <p class="text-zinc-600 dark:text-zinc-400">Aucune catégorie pour le moment</p>
                    <flux:button @click="showCreateModal = true" variant="primary" class="mt-4">
                        Créer la première catégorie
                    </flux:button>
                </div>
            @endif
        </div>

        <!-- Modal Créer -->
        <div x-show="showCreateModal" 
             x-cloak
             style="display: none"
             class="fixed inset-0 z-50 flex items-center justify-center p-4">
            
            <div class="fixed inset-0 bg-black/30 backdrop-blur-sm" @click="showCreateModal = false"></div>

            <div class="relative bg-white dark:bg-zinc-900 w-full max-w-lg rounded-xl shadow-2xl" @click.away="showCreateModal = false">
                <form action="{{ route('admin.categories.store') }}" method="POST">
                    @csrf
                    <div class="p-6">
                        <h3 class="text-xl font-semibold text-zinc-900 dark:text-zinc-100 mb-6">
                            Créer une catégorie
                        </h3>

                        <div class="space-y-4">
                            <flux:input label="Nom de la catégorie" name="name" required placeholder="Ex: Électronique" />
                            
                            <div>
                                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Description</label>
                                <textarea name="description" rows="3" placeholder="Description de la catégorie" class="w-full rounded-lg border-zinc-300 bg-white text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="bg-zinc-50 dark:bg-zinc-800/50 px-6 py-4 rounded-b-xl flex justify-end space-x-3">
                        <flux:button type="button" variant="ghost" @click="showCreateModal = false">Annuler</flux:button>
                        <flux:button type="submit" variant="primary">Créer</flux:button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Modifier -->
        <div x-show="showEditModal" 
             x-cloak
             style="display: none"
             class="fixed inset-0 z-50 flex items-center justify-center p-4">
            
            <div class="fixed inset-0 bg-black/30 backdrop-blur-sm" @click="showEditModal = false"></div>

            <div class="relative bg-white dark:bg-zinc-900 w-full max-w-lg rounded-xl shadow-2xl" @click.away="showEditModal = false">
                <form :action="`{{ url('admin/categories') }}/${selectedCategory?.id}`" method="POST" x-show="selectedCategory">
                    @csrf
                    @method('PUT')
                    <div class="p-6">
                        <h3 class="text-xl font-semibold text-zinc-900 dark:text-zinc-100 mb-6">
                            Modifier la catégorie
                        </h3>

                        <div class="space-y-4">
                            <flux:input label="Nom de la catégorie" name="name" ::value="selectedCategory?.name" required />
                            
                            <div>
                                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Description</label>
                                <textarea name="description" rows="3" x-text="selectedCategory?.description" class="w-full rounded-lg border-zinc-300 bg-white text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="bg-zinc-50 dark:bg-zinc-800/50 px-6 py-4 rounded-b-xl flex justify-end space-x-3">
                        <flux:button type="button" variant="ghost" @click="showEditModal = false">Annuler</flux:button>
                        <flux:button type="submit" variant="primary">Mettre à jour</flux:button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Supprimer -->
        <div x-show="showDeleteModal" 
             x-cloak
             style="display: none"
             class="fixed inset-0 z-50 flex items-center justify-center p-4">
            
            <div class="fixed inset-0 bg-black/30 backdrop-blur-sm" @click="showDeleteModal = false"></div>

            <div class="relative bg-white dark:bg-zinc-900 w-full max-w-md rounded-xl shadow-2xl" @click.away="showDeleteModal = false">
                <form :action="`{{ url('admin/categories') }}/${selectedCategory?.id}`" method="POST" x-show="selectedCategory">
                    @csrf
                    @method('DELETE')
                    <div class="p-6">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="flex-shrink-0 h-12 w-12 rounded-full bg-red-100 dark:bg-red-900/20 flex items-center justify-center">
                                <flux:icon.exclamation-triangle class="h-6 w-6 text-red-600 dark:text-red-400" />
                            </div>
                            <div>
                                <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100">Supprimer la catégorie</h3>
                                <p class="text-sm text-zinc-500 dark:text-zinc-400">Cette action est irréversible</p>
                            </div>
                        </div>

                        <div class="bg-zinc-50 dark:bg-zinc-800/50 rounded-lg p-4 mb-4">
                            <p class="text-sm text-zinc-700 dark:text-zinc-300">
                                Êtes-vous sûr de vouloir supprimer la catégorie <strong x-text="selectedCategory?.name"></strong> ?
                            </p>
                            <p class="text-xs text-red-600 mt-2" x-show="selectedCategory?.products_count > 0">
                                ⚠️ Cette catégorie contient <strong x-text="selectedCategory?.products_count"></strong> produit(s). Vous devez d'abord les supprimer ou les déplacer.
                            </p>
                        </div>
                    </div>

                    <div class="bg-zinc-50 dark:bg-zinc-800/50 px-6 py-4 rounded-b-xl flex justify-end space-x-3">
                        <flux:button type="button" variant="ghost" @click="showDeleteModal = false">Annuler</flux:button>
                        <flux:button type="submit" variant="danger">Supprimer</flux:button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
