<x-layouts.app :title="__('Gestion des utilisateurs')">
    <div class="p-6 space-y-6" x-data="{ 
        showCreateModal: false,
        showEditModal: false,
        showDeleteModal: false,
        selectedUser: null,
        selectedIds: [],
        selectAll: false,
        
        initUser(user) {
            this.selectedUser = user;
        },
        
        toggleSelectAll() {
            if (this.selectAll) {
                this.selectedIds = {{ Js::from($users->pluck('id')) }};
            } else {
                this.selectedIds = [];
            }
        },
        
        toggleSelect(id) {
            const index = this.selectedIds.indexOf(id);
            if (index > -1) {
                this.selectedIds.splice(index, 1);
            } else {
                this.selectedIds.push(id);
            }
        }
    }">
        <!-- Header -->
        <div class="flex justify-between items-center">
            <div>
                <flux:heading size="xl">Gestion des utilisateurs</flux:heading>
                <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">{{ $users->total() }} utilisateur(s) au total</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('admin.users.export', request()->only(['q','role'])) }}" 
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
                    Nouvel utilisateur
                </button>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Total</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ $users->total() }}</p>
                    </div>
                    <div class="size-12 rounded-xl bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center">
                        <svg class="size-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            @foreach($roles->take(3) as $role)
                <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">{{ ucfirst($role->name) }}</p>
                            <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ $role->users_count ?? $role->users()->count() }}</p>
                        </div>
                        <div class="size-12 rounded-xl flex items-center justify-center
                            @switch($role->name)
                                @case('super-admin') bg-purple-100 dark:bg-purple-900/30 @break
                                @case('admin') bg-blue-100 dark:bg-blue-900/30 @break
                                @default bg-zinc-100 dark:bg-zinc-800 @break
                            @endswitch">
                            <svg class="size-6 @switch($role->name) @case('super-admin') text-purple-600 @break @case('admin') text-blue-600 @break @default text-zinc-600 @break @endswitch" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Filters -->
        <form method="GET" class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl p-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="md:col-span-2">
                    <div class="relative">
                        <input type="search" 
                               name="q" 
                               value="{{ request('q') }}" 
                               placeholder="Rechercher par nom ou email..."
                               class="w-full pl-11 pr-4 py-3 bg-zinc-100 dark:bg-zinc-800 border-0 rounded-xl focus:ring-2 focus:ring-indigo-500">
                        <svg class="absolute left-4 top-3.5 size-5 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>
                <div>
                    <select name="role" 
                            onchange="this.form.submit()"
                            class="w-full py-3 px-4 bg-zinc-100 dark:bg-zinc-800 border-0 rounded-xl focus:ring-2 focus:ring-indigo-500">
                        <option value="">Tous les rôles</option>
                        @foreach($roles as $r)
                            <option value="{{ $r->name }}" @selected(request('role') === $r->name)>
                                {{ ucfirst($r->name) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" 
                            class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition">
                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        Filtrer
                    </button>
                    @if(request()->hasAny(['q', 'role']))
                        <a href="{{ route('admin.users.index') }}" 
                           class="px-4 py-3 text-zinc-600 dark:text-zinc-400 bg-zinc-100 dark:bg-zinc-800 rounded-xl hover:bg-zinc-200 dark:hover:bg-zinc-700 transition">
                            <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </a>
                    @endif
                </div>
            </div>
        </form>

        <!-- Users Table -->
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                    <thead class="bg-zinc-50 dark:bg-zinc-800">
                        <tr>
                            <th scope="col" class="px-6 py-4 text-left">
                                <input type="checkbox" 
                                       x-model="selectAll"
                                       @change="toggleSelectAll()"
                                       class="rounded border-zinc-300 text-indigo-600 focus:ring-indigo-500">
                            </th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Utilisateur</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Rôles</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Statut</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Inscrit le</th>
                            <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                        @forelse($users as $user)
                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition">
                                <td class="px-6 py-4">
                                    <input type="checkbox" 
                                           :checked="selectedIds.includes({{ $user->id }})"
                                           @change="toggleSelect({{ $user->id }})"
                                           class="rounded border-zinc-300 text-indigo-600 focus:ring-indigo-500">
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-4">
                                        <div class="relative">
                                            <div class="size-12 rounded-full bg-gradient-to-br 
                                                @if($user->hasRole('super-admin')) from-purple-400 to-purple-600
                                                @elseif($user->hasRole('admin')) from-blue-400 to-blue-600
                                                @else from-zinc-400 to-zinc-600
                                                @endif
                                                flex items-center justify-center text-white font-bold text-lg shadow-lg">
                                                {{ $user->initials() }}
                                            </div>
                                            <div class="absolute -bottom-0.5 -right-0.5 size-3.5 bg-green-500 border-2 border-white dark:border-zinc-900 rounded-full"></div>
                                        </div>
                                        <div>
                                            <div class="font-semibold text-zinc-900 dark:text-zinc-100">{{ $user->name }}</div>
                                            <div class="text-sm text-zinc-500 dark:text-zinc-400">{{ $user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap gap-1.5">
                                        @foreach($user->roles as $role)
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold
                                                @switch($role->name)
                                                    @case('super-admin') bg-gradient-to-r from-purple-500 to-purple-600 text-white @break
                                                    @case('admin') bg-gradient-to-r from-blue-500 to-blue-600 text-white @break
                                                    @default bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 @break
                                                @endswitch">
                                                @if($role->name === 'super-admin')
                                                    <svg class="size-3 mr-1" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z"/>
                                                    </svg>
                                                @endif
                                                {{ ucfirst($role->name) }}
                                            </span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($user->email_verified_at)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400">
                                            <svg class="size-3.5" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/>
                                            </svg>
                                            Vérifié
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400">
                                            <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            En attente
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-zinc-900 dark:text-zinc-100">{{ $user->created_at->format('d/m/Y') }}</div>
                                    <div class="text-xs text-zinc-500">{{ $user->created_at->diffForHumans() }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex justify-end gap-2">
                                        <button @click="initUser({{ $user->toJson() }}); showEditModal = true"
                                                class="p-2 text-zinc-600 dark:text-zinc-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 rounded-lg transition">
                                            <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        @if(!$user->hasRole('super-admin'))
                                            <button @click="initUser({{ $user->toJson() }}); showDeleteModal = true"
                                                    class="p-2 text-zinc-600 dark:text-zinc-400 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition">
                                                <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="size-16 mx-auto mb-4 rounded-full bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center">
                                        <svg class="size-8 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                        </svg>
                                    </div>
                                    <p class="text-zinc-500 dark:text-zinc-400">Aucun utilisateur trouvé</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Footer avec pagination et actions groupées -->
            <div class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-700 flex items-center justify-between">
                <div x-show="selectedIds.length > 0" x-transition class="flex items-center gap-3">
                    <span class="text-sm text-zinc-600 dark:text-zinc-400">
                        <span class="font-semibold text-zinc-900 dark:text-zinc-100" x-text="selectedIds.length"></span> sélectionné(s)
                    </span>
                    <form method="POST" action="{{ route('admin.users.bulkDestroy') }}" class="inline">
                        @csrf
                        <template x-for="id in selectedIds" :key="id">
                            <input type="hidden" name="ids[]" :value="id">
                        </template>
                        <button type="submit" 
                                onclick="return confirm('Voulez-vous vraiment supprimer les utilisateurs sélectionnés ?')"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition">
                            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Supprimer
                        </button>
                    </form>
                </div>
                <div class="flex-1 flex justify-end">
                    {{ $users->links() }}
                </div>
            </div>
        </div>

        <!-- Modals -->
        @include('users.modals.create')
        @include('users.modals.edit')
        @include('users.modals.delete')
    </div>
</x-layouts.app>