<x-layouts.app :title="__('Messagerie')">

        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    {{ __('Messagerie') }}
                </h2>
                <span class="px-3 py-1 text-xs font-medium text-blue-600 bg-blue-100 rounded-full dark:bg-blue-900 dark:text-blue-300">
                    {{ $conversations->count() }} conversation(s)
                </span>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('messages.search') }}" 
                   class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white dark:bg-gray-800 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    Rechercher
                </a>
                <a href="{{ route('messages.create') }}" 
                   class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Nouvelle conversation
                </a>
            </div>
        </div>
    

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-xl rounded-lg overflow-hidden">
                <div class="grid grid-cols-1 lg:grid-cols-3 h-[calc(100vh-8rem)]">
                    <!-- Liste des conversations -->
                    <div class="border-r border-gray-200 dark:border-gray-700 overflow-hidden flex flex-col">
                        <div class="p-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                            <div class="relative">
                                <input type="text" 
                                       placeholder="Rechercher une conversation..." 
                                       class="w-full pl-10 pr-4 py-2 border-gray-300 dark:border-gray-600 dark:bg-gray-800 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="flex-1 overflow-y-auto">
                            @if($conversations->isEmpty())
                                <div class="p-6 text-center">
                                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-blue-100 dark:bg-blue-900 mb-4">
                                        <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Aucune conversation</h3>
                                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Commencez à discuter avec vos collègues</p>
                                </div>
                            @else
                                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($conversations as $conversation)
                                        <a href="{{ route('messages.show', $conversation) }}" 
                                           class="block hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150 {{ request()->routeIs('messages.show') && request()->route('conversation')->id === $conversation->id ? 'bg-blue-50 dark:bg-blue-900/20' : '' }}">
                                            <div class="p-4">
                                                <div class="flex items-start space-x-4">
                                                    <!-- Avatar -->
                                                    <div class="relative">
                                                        <div class="h-12 w-12 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white font-semibold shadow-lg">
                                                            {{ $conversation->type === 'private' ? (optional($conversation->other_participant)->initials() ?? 'U') : 'G' }}
                                                        </div>
                                                        @if($conversation->unread_count > 0)
                                                            <div class="absolute -top-1 -right-1 h-5 w-5 rounded-full bg-red-500 flex items-center justify-center">
                                                                <span class="text-xs font-medium text-white">{{ $conversation->unread_count }}</span>
                                                            </div>
                                                        @endif
                                                    </div>

                                                    <!-- Contenu -->
                                                    <div class="flex-1 min-w-0">
                                                        <div class="flex items-center justify-between">
                                                            <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100 truncate">
                                                                {{ $conversation->type === 'private' ? 
                                                                   (optional($conversation->other_participant)->name ?? 'Utilisateur') : 
                                                                   $conversation->name }}
                                                            </h4>
                                                            @if($conversation->lastMessage)
                                                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                                                    {{ $conversation->lastMessage->created_at->diffForHumans() }}
                                                                </span>
                                                            @endif
                                                        </div>

                                                        @if($conversation->lastMessage)
                                                            <div class="mt-1">
                                                                @if($conversation->type === 'group')
                                                                    <span class="text-sm font-medium text-gray-600 dark:text-gray-300">
                                                                        {{ $conversation->lastMessage->user->name }}:
                                                                    </span>
                                                                @endif
                                                                <p class="text-sm text-gray-500 dark:text-gray-400 truncate">
                                                                    {{ $conversation->lastMessage->content }}
                                                                </p>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Zone de conversation vide -->
                    <div class="hidden lg:flex lg:col-span-2 items-center justify-center bg-gray-50 dark:bg-gray-900">
                        <div class="text-center px-6 py-8">
                            <div class="mx-auto h-24 w-24 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center mb-6 shadow-lg">
                                <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                </svg>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-2">
                                Sélectionnez une conversation
                            </h3>
                            <p class="text-gray-600 dark:text-gray-400 mb-6 max-w-md mx-auto">
                                Choisissez une conversation dans la liste ou créez-en une nouvelle pour commencer à échanger
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
