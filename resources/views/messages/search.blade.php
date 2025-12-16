<x-layouts.app :title="__('Rechercher dans les messages')">
    <div class="min-h-[calc(100vh-4rem)] bg-gradient-to-br from-zinc-50 to-zinc-100 dark:from-zinc-800 dark:to-zinc-900">
        <div class="max-w-4xl mx-auto p-6">
            <!-- Header -->
            <div class="flex items-center gap-4 mb-6">
                <a href="{{ route('messages.index') }}" 
                   class="p-2 text-zinc-600 dark:text-zinc-400 hover:bg-white dark:hover:bg-zinc-800 rounded-xl transition">
                    <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">Rechercher</h1>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">Trouvez des messages dans vos conversations</p>
                </div>
            </div>

            <!-- Barre de recherche -->
            <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-xl border border-zinc-200 dark:border-zinc-700 p-6 mb-6">
                <form method="GET" action="{{ route('messages.search') }}" class="flex gap-3">
                    <div class="relative flex-1">
                        <input type="text" 
                               name="q" 
                               value="{{ $query ?? '' }}"
                               placeholder="Rechercher un message..." 
                               autofocus
                               class="w-full pl-12 pr-4 py-4 bg-zinc-100 dark:bg-zinc-800 border-0 rounded-xl focus:ring-2 focus:ring-indigo-500 text-lg">
                        <svg class="absolute left-4 top-4 size-6 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <button type="submit" 
                            class="px-6 py-4 bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-semibold rounded-xl hover:from-indigo-600 hover:to-purple-700 shadow-lg transition">
                        Rechercher
                    </button>
                </form>
            </div>

            <!-- Résultats -->
            @if(isset($messages))
                @if($messages->isEmpty())
                    <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-xl border border-zinc-200 dark:border-zinc-700 p-12 text-center">
                        <div class="size-20 mx-auto mb-4 rounded-full bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center">
                            <svg class="size-10 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-zinc-900 dark:text-zinc-100 mb-2">Aucun résultat</h3>
                        <p class="text-zinc-500 dark:text-zinc-400">Aucun message ne correspond à "{{ $query }}"</p>
                    </div>
                @else
                    <div class="mb-4 flex items-center justify-between">
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">
                            <span class="font-semibold text-zinc-900 dark:text-zinc-100">{{ $messages->total() }}</span> résultat(s) pour "<span class="font-medium">{{ $query }}</span>"
                        </p>
                    </div>

                    <div class="space-y-3">
                        @foreach($messages as $message)
                            <a href="{{ route('messages.show', $message->conversation) }}" 
                               class="block bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-700 p-4 hover:shadow-md hover:border-indigo-300 dark:hover:border-indigo-700 transition">
                                <div class="flex items-start gap-4">
                                    <!-- Avatar -->
                                    <div class="flex-shrink-0">
                                        <div class="size-12 rounded-full bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center text-white font-bold">
                                            {{ $message->user->name[0] ?? 'U' }}
                                        </div>
                                    </div>

                                    <!-- Content -->
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="font-semibold text-zinc-900 dark:text-zinc-100">{{ $message->user->name }}</span>
                                            <span class="text-xs text-zinc-400">•</span>
                                            <span class="text-sm text-zinc-500 dark:text-zinc-400">
                                                {{ $message->conversation->type === 'private' ? 'Conversation privée' : $message->conversation->name }}
                                            </span>
                                        </div>
                                        
                                        <p class="text-zinc-700 dark:text-zinc-300 line-clamp-2">
                                            {!! preg_replace('/(' . preg_quote($query, '/') . ')/i', '<mark class="bg-yellow-200 dark:bg-yellow-900/50 px-0.5 rounded">$1</mark>', e($message->content)) !!}
                                        </p>
                                        
                                        <div class="flex items-center gap-2 mt-2 text-xs text-zinc-400">
                                            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            {{ $message->created_at->format('d/m/Y à H:i') }}
                                            
                                            @if($message->file_path)
                                                <span class="flex items-center gap-1 text-indigo-500">
                                                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                                    </svg>
                                                    Fichier joint
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Arrow -->
                                    <div class="flex-shrink-0 self-center">
                                        <svg class="size-5 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="mt-6">
                        {{ $messages->withQueryString()->links() }}
                    </div>
                @endif
            @else
                <!-- État initial sans recherche -->
                <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-xl border border-zinc-200 dark:border-zinc-700 p-12 text-center">
                    <div class="size-24 mx-auto mb-4 rounded-full bg-gradient-to-br from-indigo-100 to-purple-100 dark:from-indigo-900/30 dark:to-purple-900/30 flex items-center justify-center">
                        <svg class="size-12 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-zinc-900 dark:text-zinc-100 mb-2">Rechercher dans vos messages</h3>
                    <p class="text-zinc-500 dark:text-zinc-400 max-w-md mx-auto">
                        Entrez un mot-clé pour trouver des messages dans toutes vos conversations
                    </p>
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
