<x-layouts.app :title="__('Nouvelle conversation')">
    <div class="min-h-[calc(100vh-4rem)] flex items-center justify-center p-6 bg-gradient-to-br from-zinc-50 to-zinc-100 dark:from-zinc-800 dark:to-zinc-900" 
         x-data="{ 
             activeTab: 'private',
             searchQuery: '',
             selectedUsers: [],
             groupName: '',
             
             toggleUser(userId) {
                 const index = this.selectedUsers.indexOf(userId);
                 if (index > -1) {
                     this.selectedUsers.splice(index, 1);
                 } else {
                     this.selectedUsers.push(userId);
                 }
             },
             
             isSelected(userId) {
                 return this.selectedUsers.includes(userId);
             }
         }">
        <div class="w-full max-w-2xl">
            <!-- Header -->
            <div class="text-center mb-8">
                <div class="size-20 mx-auto mb-4 rounded-full bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center shadow-2xl">
                    <svg class="size-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100 mb-2">Nouvelle conversation</h1>
                <p class="text-zinc-600 dark:text-zinc-400">Démarrez une discussion privée ou créez un groupe</p>
            </div>

            <!-- Card principale -->
            <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl border border-zinc-200 dark:border-zinc-700 overflow-hidden">
                <!-- Tabs -->
                <div class="flex border-b border-zinc-200 dark:border-zinc-700">
                    <button @click="activeTab = 'private'; selectedUsers = []"
                            :class="activeTab === 'private' ? 'text-indigo-600 border-indigo-600 bg-indigo-50 dark:bg-indigo-900/20' : 'text-zinc-500 border-transparent hover:text-zinc-700 dark:hover:text-zinc-300'"
                            class="flex-1 py-4 px-6 text-sm font-semibold border-b-2 transition flex items-center justify-center gap-2">
                        <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Conversation privée
                    </button>
                    <button @click="activeTab = 'group'; selectedUsers = []"
                            :class="activeTab === 'group' ? 'text-indigo-600 border-indigo-600 bg-indigo-50 dark:bg-indigo-900/20' : 'text-zinc-500 border-transparent hover:text-zinc-700 dark:hover:text-zinc-300'"
                            class="flex-1 py-4 px-6 text-sm font-semibold border-b-2 transition flex items-center justify-center gap-2">
                        <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Créer un groupe
                    </button>
                </div>

                <div class="p-6">
                    <!-- Recherche -->
                    <div class="relative mb-4">
                        <input type="text" 
                               x-model="searchQuery"
                               placeholder="Rechercher un utilisateur..." 
                               class="w-full pl-11 pr-4 py-3 bg-zinc-100 dark:bg-zinc-800 border-0 rounded-xl focus:ring-2 focus:ring-indigo-500">
                        <svg class="absolute left-4 top-3.5 size-5 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>

                    <!-- Nom du groupe (si tab groupe) -->
                    <div x-show="activeTab === 'group'" x-transition class="mb-4">
                        <input type="text" 
                               x-model="groupName"
                               placeholder="Nom du groupe..."
                               class="w-full px-4 py-3 bg-zinc-100 dark:bg-zinc-800 border-0 rounded-xl focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <!-- Utilisateurs sélectionnés (pour groupe) -->
                    <div x-show="activeTab === 'group' && selectedUsers.length > 0" class="mb-4 flex flex-wrap gap-2">
                        @foreach($users as $user)
                            <template x-if="isSelected({{ $user->id }})">
                                <span class="inline-flex items-center gap-1 px-3 py-1 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 rounded-full text-sm">
                                    {{ $user->name }}
                                    <button @click="toggleUser({{ $user->id }})" class="hover:text-indigo-900 dark:hover:text-indigo-100">
                                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </span>
                            </template>
                        @endforeach
                    </div>

                    <!-- Liste des utilisateurs -->
                    <div class="max-h-80 overflow-y-auto space-y-2">
                        @forelse($users as $user)
                            <template x-if="'{{ strtolower($user->name) }}'.includes(searchQuery.toLowerCase()) || searchQuery === ''">
                                <!-- Pour conversation privée -->
                                <div x-show="activeTab === 'private'">
                                    <form action="{{ route('messages.create.private') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="user_id" value="{{ $user->id }}">
                                        <button type="submit" 
                                                class="w-full flex items-center gap-4 p-4 rounded-xl hover:bg-zinc-50 dark:hover:bg-zinc-800 transition text-left">
                                            <div class="relative">
                                                <div class="size-12 rounded-full bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center text-white font-bold">
                                                    {{ $user->name[0] }}
                                                </div>
                                                <div class="absolute -bottom-0.5 -right-0.5 size-3 bg-green-500 border-2 border-white dark:border-zinc-900 rounded-full"></div>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <h4 class="font-semibold text-zinc-900 dark:text-zinc-100">{{ $user->name }}</h4>
                                                <p class="text-sm text-zinc-500 truncate">{{ $user->email }}</p>
                                            </div>
                                            <svg class="size-5 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>

                                <!-- Pour groupe -->
                                <div x-show="activeTab === 'group'">
                                    <button @click="toggleUser({{ $user->id }})"
                                            :class="isSelected({{ $user->id }}) ? 'bg-indigo-50 dark:bg-indigo-900/20 border-indigo-300 dark:border-indigo-700' : 'hover:bg-zinc-50 dark:hover:bg-zinc-800 border-transparent'"
                                            class="w-full flex items-center gap-4 p-4 rounded-xl border-2 transition text-left">
                                        <div class="relative">
                                            <div class="size-12 rounded-full bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center text-white font-bold">
                                                {{ $user->name[0] }}
                                            </div>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <h4 class="font-semibold text-zinc-900 dark:text-zinc-100">{{ $user->name }}</h4>
                                            <p class="text-sm text-zinc-500 truncate">{{ $user->email }}</p>
                                        </div>
                                        <div :class="isSelected({{ $user->id }}) ? 'bg-indigo-600 border-indigo-600' : 'border-zinc-300 dark:border-zinc-600'"
                                             class="size-6 rounded-full border-2 flex items-center justify-center">
                                            <svg x-show="isSelected({{ $user->id }})" class="size-4 text-white" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/>
                                            </svg>
                                        </div>
                                    </button>
                                </div>
                            </template>
                        @empty
                            <div class="text-center py-8">
                                <p class="text-zinc-500">Aucun utilisateur disponible</p>
                            </div>
                        @endforelse
                    </div>

                    <!-- Bouton créer groupe -->
                    <div x-show="activeTab === 'group'" class="mt-6">
                        <form action="{{ route('messages.create.group') }}" method="POST">
                            @csrf
                            <input type="hidden" name="name" :value="groupName">
                            @foreach($users as $user)
                                <template x-if="isSelected({{ $user->id }})">
                                    <input type="hidden" name="user_ids[]" value="{{ $user->id }}">
                                </template>
                            @endforeach
                            <button type="submit"
                                    :disabled="selectedUsers.length < 1 || !groupName.trim()"
                                    class="w-full py-3 bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-semibold rounded-xl hover:from-indigo-600 hover:to-purple-700 disabled:opacity-50 disabled:cursor-not-allowed shadow-lg transition">
                                <span x-text="selectedUsers.length > 0 ? `Créer le groupe (${selectedUsers.length + 1} membres)` : 'Sélectionnez des membres'"></span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Retour -->
            <div class="text-center mt-6">
                <a href="{{ route('messages.index') }}" class="inline-flex items-center gap-2 text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100">
                    <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Retour à la messagerie
                </a>
            </div>
        </div>
    </div>
</x-layouts.app>