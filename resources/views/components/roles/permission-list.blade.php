
<div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
    <div class="p-6 border-b border-gray-200 flex items-center justify-between">
        <x-roles.section-header 
            title="Permissions disponibles"
            icon-class="text-emerald-600"
            bg-class="bg-emerald-100" />

        <button @click="showPermissionModal = true" 
                class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg transition-colors duration-150 shadow-sm">
            <x-icons.plus class="w-5 h-5 mr-2" />
            Nouvelle permission
        </button>
    </div>
    
    <div class="p-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            @forelse($permissions as $permission)
                <x-roles.permission-item :permission="$permission" />
            @empty
                <x-roles.empty-state 
                    message="Aucune permission disponible"
                    icon="lock"
                    class="col-span-full" />
            @endforelse
        </div>
    </div>
</div>