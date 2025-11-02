<x-layouts.app :title="__('Gestion des utilisateurs')">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ __('Gestion des utilisateurs') }}
            </h2>
            
        </div>
    </x-slot>

    <div class="py-6" x-data="{ 
        showCreateModal: false,
        showEditModal: false,
        showDeleteModal: false,
        selectedUser: null,
        initUser(user) {
            this.selectedUser = user;
        }
    }">
        <!-- Filtres et recherche -->
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mb-6">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Gestion des utilisateurs
            </h2>
            <div class="mt-4"></div>
            <div class="bg-white rounded-lg shadow p-6">
                <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <input type="search" 
                               name="q" 
                               value="{{ request('q') }}" 
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                               placeholder="Recherche nom ou email">
                    </div>
                    <div>
                        <select name="role" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Tous les rôles</option>
                            @foreach($roles as $r)
                                <option value="{{ $r->name }}" @selected(request('role') === $r->name)>
                                    {{ ucfirst($r->name) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex space-x-2">
                        <button type="submit" 
                                class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            Filtrer
                        </button>
                        <a href="{{ route('admin.users.index') }}" 
                           class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                            Réinitialiser
                        </a>
                    </div>
                    <div class="flex justify-end">
                        <a href="{{ route('admin.users.export', request()->only(['q','role'])) }}" 
                           class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-green-700">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Exporter CSV
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Table des utilisateurs -->
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('admin.users.bulkDestroy') }}">
                @csrf
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="p-4 flex justify-between items-center border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Liste des utilisateurs</h3>
                        <button @click="showCreateModal = true" 
                                type="button"
                                class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 transition-colors duration-150">
                            <svg class="w-5 h-5 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Ajouter un utilisateur
                        </button>
                    </div>
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left">
                                    <input type="checkbox" id="check-all" 
                                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rôles</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Créé le</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($users as $user)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <input type="checkbox" name="ids[]" value="{{ $user->id }}"
                                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="h-10 w-10 flex-shrink-0">
                                                <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                                    <span class="text-blue-600 font-medium">{{ $user->initials() }}</span>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user->email }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($user->roles as $role)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                    @switch($role->name)
                                                        @case('super-admin') bg-purple-100 text-purple-800 @break
                                                        @case('admin') bg-blue-100 text-blue-800 @break
                                                        @default bg-gray-100 text-gray-800
                                                    @endswitch">
                                                    {{ ucfirst($role->name) }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $user->created_at->format('d/m/Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        
                                        <template x-if="!user.roles.some(role => role.name === 'super-admin')">
                                            <button type="button" 
                                                    @click="initUser(user); showDeleteModal = true"
                                                    class="inline-flex items-center text-red-600 hover:text-red-900">
                                                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                                Supprimer
                                            </button>
                                        </template>

                                        <!-- Remplacer le bouton Edit existant par : -->
                                        <button type="button" 
                                                @click="initUser({{ $user->toJson() }}); showEditModal = true"
                                                class="inline-flex items-center text-blue-600 hover:text-blue-900 mr-3">
                                            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                            Éditer
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                        Aucun utilisateur trouvé
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 flex items-center justify-between">
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700"
                            onclick="return confirm('Voulez-vous vraiment supprimer les utilisateurs sélectionnés ?')">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Supprimer la sélection
                    </button>
                    
                    <div>
                        {{ $users->links() }}
                    </div>
                </div>
            </form>
        </div>

        <!-- Modals -->
        @include('users.modals.create')
        @include('users.modals.edit')
        @include('users.modals.delete')
    </div>

    @push('scripts')
    <script>
        document.getElementById('check-all')?.addEventListener('change', function(e) {
            document.querySelectorAll('input[name="ids[]"]').forEach(cb => cb.checked = e.target.checked);
        });
    </script>
    @endpush
</x-layouts.app>