<x-layouts.app :title="__('Gestion des contacts')">
    <div class="p-6 space-y-6" x-data="{ 
        showCreateModal: false,
        showEditModal: false,
        showDeleteModal: false,
        selectedContact: null,
        viewMode: 'table',
        search: '{{ request('q') }}',
        selectedEntreprise: '{{ request('entreprise') }}',
        contacts: {{ Js::from($contacts->items()) }},
        entreprises: {{ Js::from($entreprises) }},

        initContact(contact) {
            this.selectedContact = contact;
        },

        get filteredContacts() {
            return this.contacts.filter(contact => {
                const matchSearch = !this.search || 
                    contact.nom.toLowerCase().includes(this.search.toLowerCase()) ||
                    contact.prenom?.toLowerCase().includes(this.search.toLowerCase()) ||
                    contact.email.toLowerCase().includes(this.search.toLowerCase()) ||
                    contact.telephone?.includes(this.search) ||
                    contact.entreprise?.toLowerCase().includes(this.search.toLowerCase());
                
                const matchEntreprise = !this.selectedEntreprise || 
                    contact.entreprise === this.selectedEntreprise;
                
                return matchSearch && matchEntreprise;
            });
        },
        
        getInitials(contact) {
            return ((contact.prenom?.[0] || '') + (contact.nom?.[0] || '')).toUpperCase() || 'C';
        },
        
        getGradientClass(index) {
            const gradients = [
                'from-blue-400 to-indigo-500',
                'from-purple-400 to-pink-500',
                'from-emerald-400 to-teal-500',
                'from-orange-400 to-red-500',
                'from-cyan-400 to-blue-500'
            ];
            return gradients[index % gradients.length];
        }
    }">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <flux:heading size="xl">Gestion des contacts</flux:heading>
                <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">
                    {{ $contacts->total() }} contact(s) • {{ count($entreprises) }} entreprise(s)
                </p>
            </div>
            <div class="flex items-center gap-3">
                <!-- Toggle View -->
                <div class="flex bg-zinc-100 dark:bg-zinc-800 p-1 rounded-xl">
                    <button @click="viewMode = 'table'"
                            :class="viewMode === 'table' ? 'bg-white dark:bg-zinc-700 shadow-sm' : ''"
                            class="p-2 rounded-lg transition">
                        <svg class="size-5 text-zinc-600 dark:text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                        </svg>
                    </button>
                    <button @click="viewMode = 'grid'"
                            :class="viewMode === 'grid' ? 'bg-white dark:bg-zinc-700 shadow-sm' : ''"
                            class="p-2 rounded-lg transition">
                        <svg class="size-5 text-zinc-600 dark:text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
                    </button>
                </div>
                
                <a href="{{ route('admin.contacts.export', request()->only(['q','entreprise'])) }}" 
                   class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-zinc-700 bg-white dark:bg-zinc-800 dark:text-zinc-300 border border-zinc-300 dark:border-zinc-700 rounded-xl hover:bg-zinc-50 dark:hover:bg-zinc-700 transition">
                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Exporter
                </a>
                <button @click="showCreateModal = true"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-indigo-500 to-purple-600 rounded-xl hover:from-indigo-600 hover:to-purple-700 shadow-lg transition">
                    <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Nouveau contact
                </button>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Total contacts</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ $contacts->total() }}</p>
                    </div>
                    <div class="size-11 rounded-xl bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center">
                        <svg class="size-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                </div>
            </div>
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Entreprises</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ count($entreprises) }}</p>
                    </div>
                    <div class="size-11 rounded-xl bg-gradient-to-br from-emerald-400 to-teal-500 flex items-center justify-center">
                        <svg class="size-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                </div>
            </div>
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Avec email</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ $contacts->filter(fn($c) => $c->email)->count() }}</p>
                    </div>
                    <div class="size-11 rounded-xl bg-gradient-to-br from-blue-400 to-cyan-500 flex items-center justify-center">
                        <svg class="size-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>
            </div>
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Avec téléphone</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ $contacts->filter(fn($c) => $c->telephone)->count() }}</p>
                    </div>
                    <div class="size-11 rounded-xl bg-gradient-to-br from-orange-400 to-pink-500 flex items-center justify-center">
                        <svg class="size-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="p-5 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="md:col-span-2 relative">
                    <input type="text" 
                           x-model="search"
                           placeholder="Rechercher par nom, email, téléphone..."
                           class="w-full pl-11 pr-4 py-3 bg-zinc-100 dark:bg-zinc-800 border-0 rounded-xl focus:ring-2 focus:ring-indigo-500">
                    <svg class="absolute left-4 top-3.5 size-5 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <div>
                    <select x-model="selectedEntreprise" 
                            class="w-full py-3 px-4 bg-zinc-100 dark:bg-zinc-800 border-0 rounded-xl focus:ring-2 focus:ring-indigo-500 text-zinc-900 dark:text-zinc-100">
                        <option value="">Toutes les entreprises</option>
                        <template x-for="entreprise in entreprises" :key="entreprise">
                            <option :value="entreprise" x-text="entreprise"></option>
                        </template>
                    </select>
                </div>
                <div class="flex items-center gap-2 text-sm text-zinc-500">
                    <span x-text="filteredContacts.length"></span> résultat(s)
                    <button x-show="search || selectedEntreprise" 
                            @click="search = ''; selectedEntreprise = ''"
                            class="ml-auto text-red-500 hover:text-red-700">
                        Effacer les filtres
                    </button>
                </div>
            </div>
        </div>

        <!-- Table View -->
        <div x-show="viewMode === 'table'" class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                    <thead class="bg-zinc-50 dark:bg-zinc-800">
                        <tr>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Contact</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Coordonnées</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Entreprise</th>
                            <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                        <template x-for="(contact, index) in filteredContacts" :key="contact.id">
                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-4">
                                        <div class="size-11 rounded-full bg-gradient-to-br flex items-center justify-center text-white font-bold shadow-lg"
                                             :class="getGradientClass(index)">
                                            <span x-text="getInitials(contact)"></span>
                                        </div>
                                        <div>
                                            <a :href="`{{ url('/contacts') }}/${contact.id}`" 
                                               class="font-semibold text-zinc-900 dark:text-zinc-100 hover:text-indigo-600 dark:hover:text-indigo-400"
                                               x-text="(contact.prenom || '') + ' ' + contact.nom">
                                            </a>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="space-y-1">
                                        <a :href="'mailto:' + contact.email" class="flex items-center gap-2 text-sm text-zinc-600 dark:text-zinc-400 hover:text-indigo-600">
                                            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                            </svg>
                                            <span x-text="contact.email"></span>
                                        </a>
                                        <a x-show="contact.telephone" :href="'tel:' + contact.telephone" class="flex items-center gap-2 text-sm text-zinc-600 dark:text-zinc-400 hover:text-indigo-600">
                                            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                            </svg>
                                            <span x-text="contact.telephone"></span>
                                        </a>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span x-show="contact.entreprise" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-sm bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300">
                                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                        <span x-text="contact.entreprise"></span>
                                    </span>
                                    <span x-show="!contact.entreprise" class="text-sm text-zinc-400">—</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex justify-end gap-1">
                                        <a :href="`{{ url('/contacts') }}/${contact.id}`"
                                           class="p-2 text-zinc-500 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 rounded-lg transition">
                                            <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>
                                        <button @click="initContact(contact); showEditModal = true"
                                                class="p-2 text-zinc-500 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition">
                                            <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        <button @click="initContact(contact); showDeleteModal = true"
                                                class="p-2 text-zinc-500 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition">
                                            <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <div x-show="filteredContacts.length === 0" class="p-12 text-center">
                <div class="size-16 mx-auto mb-4 rounded-full bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center">
                    <svg class="size-8 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <p class="text-zinc-500 dark:text-zinc-400">Aucun contact trouvé</p>
            </div>

            <div class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-700">
                {{ $contacts->links() }}
            </div>
        </div>

        <!-- Grid View -->
        <div x-show="viewMode === 'grid'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <template x-for="(contact, index) in filteredContacts" :key="contact.id">
                <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl p-6 hover:shadow-lg hover:border-indigo-300 dark:hover:border-indigo-700 transition group">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center gap-4">
                            <div class="size-14 rounded-full bg-gradient-to-br flex items-center justify-center text-white font-bold text-lg shadow-lg"
                                 :class="getGradientClass(index)">
                                <span x-text="getInitials(contact)"></span>
                            </div>
                            <div>
                                <h3 class="font-semibold text-zinc-900 dark:text-zinc-100" x-text="(contact.prenom || '') + ' ' + contact.nom"></h3>
                                <p x-show="contact.entreprise" class="text-sm text-zinc-500" x-text="contact.entreprise"></p>
                            </div>
                        </div>
                        <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition">
                            <button @click="initContact(contact); showEditModal = true"
                                    class="p-1.5 text-zinc-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg">
                                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </button>
                            <button @click="initContact(contact); showDeleteModal = true"
                                    class="p-1.5 text-zinc-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg">
                                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    
                    <div class="space-y-2">
                        <a :href="'mailto:' + contact.email" class="flex items-center gap-3 text-sm text-zinc-600 dark:text-zinc-400 hover:text-indigo-600 transition">
                            <svg class="size-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <span class="truncate" x-text="contact.email"></span>
                        </a>
                        <a x-show="contact.telephone" :href="'tel:' + contact.telephone" class="flex items-center gap-3 text-sm text-zinc-600 dark:text-zinc-400 hover:text-indigo-600 transition">
                            <svg class="size-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                            <span x-text="contact.telephone"></span>
                        </a>
                    </div>
                    
                    <div class="mt-4 pt-4 border-t border-zinc-100 dark:border-zinc-800">
                        <a :href="`{{ url('/contacts') }}/${contact.id}`" 
                           class="text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 flex items-center gap-1">
                            Voir le profil
                            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                </div>
            </template>
        </div>

        <!-- Modals -->
        @include('contacts.modals.create')
        @include('contacts.modals.edit')
        @include('contacts.modals.delete')
    </div>
</x-layouts.app>