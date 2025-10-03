
<div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
    <div class="p-6 border-b border-gray-200 flex items-center justify-between">
        <x-roles.section-header 
            title="Rôles disponibles"
            icon-class="text-indigo-600"
            bg-class="bg-indigo-100" />

        <button @click="showRoleModal = true" 
                class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors duration-150 shadow-sm">
            <x-icons.plus class="w-5 h-5 mr-2" />
            Nouveau rôle
        </button>
    </div>
    
    <div class="divide-y divide-gray-200">
        @forelse($roles as $role)
            <x-roles.role-item :role="$role" />
        @empty
            <x-roles.empty-state 
                message="Aucun rôle disponible"
                icon="users" />
        @endforelse
    </div>
</div>