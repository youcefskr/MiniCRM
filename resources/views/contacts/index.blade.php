<x-layouts.app :title="__('Gestion des contacts')">
    <div class="p-6 space-y-6" x-data="{ 
        showCreateModal: false,
        showEditModal: false,
        showDeleteModal: false,
        selectedContact: null,
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
                    contact.telephone.includes(this.search) ||
                    contact.entreprise?.toLowerCase().includes(this.search.toLowerCase());
                
                const matchEntreprise = !this.selectedEntreprise || 
                    contact.entreprise === this.selectedEntreprise;
                
                return matchSearch && matchEntreprise;
            });
        }
    }">
        <!-- En-tête -->
        <flux:heading size="xl">Gestion des contacts</flux:heading>

        <!-- Filtres et recherche -->
        <div class="p-6 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl shadow-sm">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <flux:input 
                    icon="magnifying-glass"
                    x-model="search"
                    placeholder="Rechercher un contact..." />
                
                <div>
                    <select x-model="selectedEntreprise" 
                            class="w-full rounded-lg border-zinc-300 bg-white text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100">
                        <option value="">Toutes les entreprises</option>
                        <template x-for="entreprise in entreprises" :key="entreprise">
                            <option :value="entreprise" x-text="entreprise"></option>
                        </template>
                    </select>
                </div>

                <div class="flex justify-end md:col-span-2 gap-2">
                    <flux:button 
                        x-bind:href="`{{ route('admin.contacts.export') }}?q=${search}&entreprise=${selectedEntreprise}`"
                        variant="ghost"
                        icon="arrow-down-tray">
                        Exporter CSV
                    </flux:button>
                    <flux:button 
                        @click="showCreateModal = true"
                        variant="primary"
                        icon="plus">
                        Nouveau contact
                    </flux:button>
                </div>
            </div>
        </div>

        <!-- Table des contacts -->
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                    <thead class="bg-zinc-50 dark:bg-zinc-800">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider dark:text-zinc-400">Nom</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider dark:text-zinc-400">Email</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider dark:text-zinc-400">Téléphone</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider dark:text-zinc-400">Entreprise</th>
                            <th scope="col" class="relative px-6 py-3">
                                <span class="sr-only">Actions</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-zinc-200 dark:bg-zinc-900 dark:divide-zinc-700">
                        <template x-for="contact in filteredContacts" :key="contact.id">
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-zinc-100 dark:bg-zinc-800">
                                            <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300" x-text="(contact.nom[0] + (contact.prenom?.[0] || '')).toUpperCase()"></span>
                                        </div>
                                        <div>
                                            <div class="font-medium text-zinc-900 dark:text-zinc-100" x-text="contact.nom + ' ' + (contact.prenom || '')"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-zinc-600 dark:text-zinc-400" x-text="contact.email"></span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-zinc-600 dark:text-zinc-400" x-text="contact.telephone"></span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-zinc-600 dark:text-zinc-400" x-text="contact.entreprise || '-'"></span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end gap-2">
                                        <flux:button 
                                            @click="initContact(contact); showEditModal = true"
                                            size="sm"
                                            variant="ghost"
                                            icon="pencil" />
                                        <flux:button 
                                            @click="initContact(contact); showDeleteModal = true"
                                            size="sm"
                                            variant="danger"
                                            icon="trash" />
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <div x-show="filteredContacts.length === 0" class="p-6 text-center text-zinc-500 dark:text-zinc-400">
                Aucun contact trouvé
            </div>
        </div>

        <!-- Modals -->
        @include('contacts.modals.create')
        @include('contacts.modals.edit')
        @include('contacts.modals.delete')
    </div>
</x-layouts.app>