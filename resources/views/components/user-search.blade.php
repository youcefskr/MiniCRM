<div class="bg-white p-4 rounded-lg shadow mb-6">
    <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-wrap gap-4">
        <div class="flex-1">
            <input
                type="text"
                name="search"
                value="{{ $search }}"
                placeholder="Rechercher par nom ou email..."
                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
            >
        </div>
        
        <div class="w-64">
            <select
                name="role"
                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
            >
                <option value="">Tous les rôles</option>
                @foreach($roles as $role)
                    <option value="{{ $role->name }}" {{ $roleFilter == $role->name ? 'selected' : '' }}>
                        {{ ucfirst($role->name) }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <button
                type="submit"
                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500"
            >
                Rechercher
            </button>
        </div>
    </form>
</div>