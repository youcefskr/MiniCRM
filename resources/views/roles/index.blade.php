<x-layouts.app :title="__('Gestion des rôles et permissions')">
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Gestion des rôles et permissions') }}
        </h2>
    </x-slot>

    <!-- Messages Flash -->
    
@include('components.flash-messages')
    <!-- Add auto-hide script -->
    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            setTimeout(() => {
                const modal = document.querySelector('[x-data]').__x.$data
                if (modal.show) {
                    modal.show = false
                }
            }, 5000) // Auto-hide after 5 seconds
        })
    </script>
    @endpush

    <div class="py-6" x-data="{ 
        showRoleModal: false, 
        showPermissionModal: false,
        showEditRoleModal: false,
        showDeleteModal: false,
        showDeletePermissionModal: false,
        selectedRole: {
            id: null,
            name: '',
            permissions: []
        },
        availablePermissions: @js($permissions),
        itemToDelete: null,
        deleteType: null,
        deleteName: '',
        deleteAction: '',
        permissionToDelete: '',
        permissionDeleteAction: '',
        showEditPermissionModal: false,
        selectedPermission: {
            id: null,
            name: ''
        }
    }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Gestion des rôles et permissions
            </h2>
            <div class="mt-4"></div>
            <!-- Section Rôles -->
            @include('roles.roles-list')

            {{-- 🔑 Liste des permissions --}}
            @include('roles.permissions-list')
        </div>

        <!-- Modal Création Rôle -->
        @include('roles.Modal.create-role')

        <!-- Modal Création Permission -->
        @include('roles.Modal.create-permission')

        <!-- Delete Confirmation Modal -->
        @include('roles.Modal.delete-confirmation')

        <!-- Delete Permission Confirmation Modal -->
        @include('roles.Modal.delete-permission')

        <!-- Modal Édition Rôle -->
        @include('roles.Modal.edit-role')

        <!-- Modal Édition Permission -->
        @include('roles.Modal.edit-permission')
    </div>

    @push('styles')
    <style>
        [x-cloak] { display: none !important; }
    </style>
    @endpush
</x-layouts.app>