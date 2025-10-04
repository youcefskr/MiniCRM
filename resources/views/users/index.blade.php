<x-layouts.app :title="__('Gestion des utilisateurs')">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ __('Gestion des utilisateurs') }}
            </h2>
            <div class="flex space-x-3">
                <button @click="showCreateModal = true" 
                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors duration-150 shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-5 h-5 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Nouveau utilisateur
                </button>
                <a href="{{ route('admin.roles.index') }}" 
                   class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors duration-150 shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-5 h-5 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    Gérer les rôles
                </a>
            </div>
        </div>
    </x-slot>

    @include('components.flash-messages')

    <div x-data="{ 
        showCreateModal: false,
        showEditModal: false,
        showDeleteModal: false,
        selectedUser: null,
        initUser(user) {
            this.selectedUser = {
                id: user.id,
                name: user.name,
                email: user.email,
                roles: user.roles
            };
        }
    }" class="py-6">
        <!-- Ajout d'un bouton de test pour vérifier Alpine.js -->
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mb-4">
            <button 
                @click="showCreateModal = true"
                class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Nouveau utilisateur
            </button>
        </div>

        <!-- Liste des utilisateurs -->
        @include('users.users-list')

        <!-- Modals -->
        @include('users.modals.create')
        @include('users.modals.edit')
        @include('users.modals.delete')

        <!-- Ajout d'un debugger Alpine.js -->
        <div x-data class="hidden">
            <template x-if="$store.showCreateModal">
                <p>Modal State: <span x-text="$store.showCreateModal"></span></p>
            </template>
        </div>

        
    </div>
</x-layouts.app>