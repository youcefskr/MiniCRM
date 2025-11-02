<x-layouts.app :title="__('Gestion des contacts')">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ __('Gestion des contacts') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-6" x-data="{ 
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
        <!-- Filtres et recherche -->
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mb-6">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Gestion des contacts
            </h2>
            <div class="mt-4"></div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <input type="search" 
                               x-model="search"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                               placeholder="Rechercher un contact...">
                    </div>
                    <div>
                        <select x-model="selectedEntreprise" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Toutes les entreprises</option>
                            <template x-for="entreprise in entreprises" :key="entreprise">
                                <option :value="entreprise" x-text="entreprise"></option>
                            </template>
                        </select>
                    </div>
                    <div class="flex justify-end md:col-span-2">
                        <a :href="`{{ route('admin.contacts.export') }}?q=${search}&entreprise=${selectedEntreprise}`"
                           class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-green-700">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Exporter CSV
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Liste des contacts -->
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow overflow-hidden">
                
                <div class="p-4 flex justify-between items-center border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Liste des contacts</h3>
                    <button @click="showCreateModal = true" 
                            type="button"
                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 transition-colors duration-150">
                        <svg class="w-5 h-5 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Ajouter un contact
                    </button>
                </div>

                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nom</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Téléphone</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Entreprise</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <template x-for="contact in filteredContacts" :key="contact.id">
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="text-sm font-medium text-gray-900">
                                            <span x-text="contact.nom + ' ' + (contact.prenom || '')"></span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="contact.email"></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="contact.telephone"></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="contact.entreprise || '-'"></td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <button @click="initContact(contact); showEditModal = true" 
                                            class="text-blue-600 hover:text-blue-900 mr-3">Éditer</button>
                                    <button @click="initContact(contact); showDeleteModal = true" 
                                            class="text-red-600 hover:text-red-900">Supprimer</button>
                                </td>
                            </tr>
                        </template>
                        <template x-if="filteredContacts.length === 0">
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                    Aucun contact trouvé
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modals -->
        @include('contacts.modals.create')
        @include('contacts.modals.edit')
        @include('contacts.modals.delete')
    </div>
</x-layouts.app>