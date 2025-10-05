<x-layouts.app :title="__('Gestion des contacts')">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ __('Gestion des contacts') }}
            </h2>
            <button @click="showCreateModal = true" 
                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors duration-150 shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="w-5 h-5 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Nouveau contact
            </button>
        </div>
    </x-slot>

    @include('components.flash-messages')

    <div x-data="{ 
        showCreateModal: false,
        showEditModal: false,
        showDeleteModal: false,
        selectedContact: null,
        initContact(contact) {
            this.selectedContact = {
                id: contact.id,
                nom: contact.nom,
                prenom: contact.prenom,
                email: contact.email,
                telephone: contact.telephone,
                entreprise: contact.entreprise,
                adresse: contact.adresse
            };
        }
    }" class="py-6">
        <!-- Ajout d'un bouton de test comme dans users -->
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mb-4">
            <button 
                @click="showCreateModal = true"
                class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors duration-150 shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Nouveau contact
            </button>
        </div>

        <!-- Liste des contacts -->
        @include('contacts.contacts-list')

        <!-- Modals -->
        @include('contacts.modals.create')
        @include('contacts.modals.edit')
        @include('contacts.modals.delete')
    </div>
</x-layouts.app>