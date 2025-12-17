<x-layouts.app :title="__('Messagerie')">
    <div class="h-[calc(100vh-4rem)] flex" x-data="{ 
        showNewConversationModal: false,
        showNewGroupModal: false,
        searchQuery: '',
        conversations: {{ Js::from($conversations) }},
        
        get filteredConversations() {
            if (!this.searchQuery.trim()) return this.conversations;
            return this.conversations.filter(c => {
                const name = c.type === 'private' 
                    ? (c.other_participant?.name || '') 
                    : c.name;
                return name.toLowerCase().includes(this.searchQuery.toLowerCase());
            });
        },
        
        getTotalUnread() {
            return this.conversations.reduce((sum, c) => sum + (c.unread_count || 0), 0);
        }
    }">
        <!-- Sidebar Conversations -->
        <div class="w-full md:w-96 bg-white dark:bg-zinc-900 border-r border-zinc-200 dark:border-zinc-700 flex flex-col">
            <!-- Header -->
            <div class="p-4 border-b border-zinc-200 dark:border-zinc-700 bg-gradient-to-r from-indigo-600 to-purple-600">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <h1 class="text-xl font-bold text-white">Messagerie</h1>
                        <span x-show="getTotalUnread() > 0" 
                              class="px-2 py-0.5 text-xs font-bold bg-red-500 text-white rounded-full"
                              x-text="getTotalUnread()"></span>
                    </div>
                    <div class="flex gap-2">
                        <button @click="showNewGroupModal = true"
                                class="p-2 text-white/80 hover:text-white hover:bg-white/10 rounded-lg transition">
                            <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </button>
                        <button @click="showNewConversationModal = true"
                                class="p-2 text-white/80 hover:text-white hover:bg-white/10 rounded-lg transition">
                            <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                        </button>
                    </div>
                </div>
                
                <!-- Search -->
                <div class="relative">
                    <input type="text" 
                           x-model="searchQuery"
                           placeholder="Rechercher une conversation..." 
                           class="w-full pl-10 pr-4 py-2 bg-white/10 border-0 rounded-xl text-white placeholder-white/60 focus:ring-2 focus:ring-white/30">
                    <svg class="absolute left-3 top-2.5 size-5 text-white/60" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>

            <!-- Conversations List -->
            <div class="flex-1 overflow-y-auto">
                @if($conversations->isEmpty())
                    <div class="p-8 text-center">
                        <div class="size-20 mx-auto mb-4 rounded-full bg-gradient-to-br from-indigo-100 to-purple-100 dark:from-indigo-900/30 dark:to-purple-900/30 flex items-center justify-center">
                            <svg class="size-10 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-2">Aucune conversation</h3>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400 mb-4">Commencez à discuter avec vos collègues</p>
                        <button @click="showNewConversationModal = true"
                                class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Nouvelle conversation
                        </button>
                    </div>
                @else
                    <template x-for="conversation in filteredConversations" :key="conversation.id">
                        <a :href="`{{ url('/messages') }}/${conversation.id}`"
                           class="block hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors border-b border-zinc-100 dark:border-zinc-800">
                            <div class="p-4">
                                <div class="flex items-start gap-3">
                                    <!-- Avatar avec indicateur en ligne -->
                                    <div class="relative flex-shrink-0">
                                        <template x-if="conversation.type === 'private'">
                                            <div class="size-12 rounded-full bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center text-white font-bold shadow-lg">
                                                <span x-text="conversation.other_participant?.name?.charAt(0)?.toUpperCase() || 'U'"></span>
                                            </div>
                                        </template>
                                        <template x-if="conversation.type === 'group'">
                                            <div class="size-12 rounded-full bg-gradient-to-br from-emerald-400 to-teal-500 flex items-center justify-center text-white shadow-lg">
                                                <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                            </div>
                                        </template>
                                        <!-- Indicateur en ligne -->
                                        <div class="absolute -bottom-0.5 -right-0.5 size-3.5 bg-green-500 border-2 border-white dark:border-zinc-900 rounded-full"></div>
                                        
                                        <!-- Badge non lus -->
                                        <template x-if="conversation.unread_count > 0">
                                            <div class="absolute -top-1 -right-1 size-5 bg-red-500 rounded-full flex items-center justify-center">
                                                <span class="text-[10px] font-bold text-white" x-text="conversation.unread_count > 9 ? '9+' : conversation.unread_count"></span>
                                            </div>
                                        </template>
                                    </div>

                                    <!-- Contenu -->
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between mb-1">
                                            <h4 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100 truncate" 
                                                :class="{ 'font-bold': conversation.unread_count > 0 }"
                                                x-text="conversation.type === 'private' ? (conversation.other_participant?.name || 'Utilisateur') : conversation.name">
                                            </h4>
                                            <span class="text-xs text-zinc-400" x-text="conversation.last_message ? new Date(conversation.last_message.created_at).toLocaleTimeString('fr-FR', {hour: '2-digit', minute: '2-digit'}) : ''"></span>
                                        </div>
                                        
                                        <template x-if="conversation.last_message">
                                            <div class="flex items-center gap-1">
                                                <!-- Check marks pour statut lu -->
                                                <template x-if="conversation.last_message.user_id === {{ Auth::id() }}">
                                                    <svg class="size-4 flex-shrink-0" :class="conversation.unread_count === 0 ? 'text-blue-500' : 'text-zinc-400'" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/>
                                                    </svg>
                                                </template>
                                                <template x-if="conversation.type === 'group' && conversation.last_message.user_id !== {{ Auth::id() }}">
                                                    <span class="text-xs font-medium text-indigo-600 dark:text-indigo-400" x-text="conversation.last_message.user?.name?.split(' ')[0] + ':'"></span>
                                                </template>
                                                <p class="text-sm truncate" 
                                                   :class="conversation.unread_count > 0 ? 'text-zinc-900 dark:text-zinc-100 font-medium' : 'text-zinc-500 dark:text-zinc-400'"
                                                   x-text="conversation.last_message.file_path ? '📎 Fichier joint' : conversation.last_message.content">
                                                </p>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </template>
                @endif
            </div>
        </div>

        <!-- Zone principale (vide) -->
        <div class="hidden md:flex flex-1 items-center justify-center bg-gradient-to-br from-zinc-50 to-zinc-100 dark:from-zinc-800 dark:to-zinc-900">
            <div class="text-center px-8">
                <div class="size-32 mx-auto mb-6 rounded-full bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center shadow-2xl">
                    <svg class="size-16 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100 mb-2">Message CRM</h2>
                <p class="text-zinc-600 dark:text-zinc-400 mb-6 max-w-md">
                    Communiquez en temps réel avec votre équipe. Sélectionnez une conversation ou créez-en une nouvelle pour commencer.
                </p>
                <div class="flex justify-center gap-3">
                    <button @click="showNewConversationModal = true"
                            class="inline-flex items-center gap-2 px-6 py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 shadow-lg hover:shadow-xl transition">
                        <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Nouvelle conversation
                    </button>
                    <button @click="showNewGroupModal = true"
                            class="inline-flex items-center gap-2 px-6 py-3 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 border border-zinc-200 dark:border-zinc-700 rounded-xl hover:bg-zinc-50 dark:hover:bg-zinc-700 shadow-lg transition">
                        <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Créer un groupe
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal Nouvelle Conversation -->
        <div x-show="showNewConversationModal" 
             x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="showNewConversationModal = false"></div>
            <div class="relative bg-white dark:bg-zinc-900 w-full max-w-md rounded-2xl shadow-2xl">
                <div class="p-6">
                    <h3 class="text-xl font-bold text-zinc-900 dark:text-zinc-100 mb-4">Nouvelle conversation</h3>
                    
                    <form action="{{ route('messages.create.private') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Sélectionner un utilisateur</label>
                            <select name="user_id" required class="w-full rounded-xl border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">-- Choisir --</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="flex justify-end gap-3">
                            <button type="button" @click="showNewConversationModal = false" class="px-4 py-2 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-lg">Annuler</button>
                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">Démarrer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Nouveau Groupe -->
        <div x-show="showNewGroupModal" 
             x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="showNewGroupModal = false"></div>
            <div class="relative bg-white dark:bg-zinc-900 w-full max-w-md rounded-2xl shadow-2xl">
                <div class="p-6">
                    <h3 class="text-xl font-bold text-zinc-900 dark:text-zinc-100 mb-4">Créer un groupe</h3>
                    
                    <form action="{{ route('messages.create.group') }}" method="POST">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Nom du groupe</label>
                                <input type="text" name="name" required placeholder="Ex: Équipe commerciale" class="w-full rounded-xl border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Participants</label>
                                <select name="user_ids[]" multiple required class="w-full rounded-xl border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 focus:ring-indigo-500 focus:border-indigo-500" size="5">
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                                <p class="text-xs text-zinc-500 mt-1">Maintenez Ctrl pour sélectionner plusieurs</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Description (optionnel)</label>
                                <textarea name="description" rows="2" placeholder="Description du groupe..." class="w-full rounded-xl border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                            </div>
                        </div>
                        
                        <div class="flex justify-end gap-3 mt-6">
                            <button type="button" @click="showNewGroupModal = false" class="px-4 py-2 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-lg">Annuler</button>
                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">Créer le groupe</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
