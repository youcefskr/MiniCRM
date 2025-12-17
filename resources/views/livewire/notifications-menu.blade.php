<div wire:poll.10s>
    <flux:dropdown position="bottom" align="end">
        <flux:button icon="bell" variant="ghost" class="relative text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-200">
            @if($this->unreadCount > 0)
                <span class="absolute top-2 right-2 -mt-1 -mr-1 flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-red-500"></span>
                </span>
            @endif
        </flux:button>

        <flux:menu class="w-80">
            <div class="px-4 py-3 border-b border-zinc-200 dark:border-zinc-700 flex justify-between items-center">
                <span class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Notifications</span>
                <div class="flex gap-2">
                    @if($this->unreadCount > 0)
                        <button wire:click="markAllAsRead" class="text-xs text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300" title="Tout marquer comme lu">
                            <flux:icon icon="check-circle" class="size-4" />
                        </button>
                    @endif
                    @if($this->notifications->count() > 0)
                        <button wire:click="clearAll" class="text-xs text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300" title="Tout effacer">
                            <flux:icon icon="trash" class="size-4" />
                        </button>
                    @endif
                </div>
            </div>

            <div class="max-h-96 overflow-y-auto">
                @forelse($this->notifications as $notification)
                    <div class="relative group border-b border-zinc-100 dark:border-zinc-800 last:border-0 flex hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                        <a wire:click="markAsRead('{{ $notification->id }}')" href="{{ $notification->data['action_url'] ?? '#' }}" class="flex-1 flex flex-col items-start gap-1 p-3 pr-10 {{ $notification->read_at ? 'opacity-75' : 'bg-blue-50/30 dark:bg-blue-900/10' }}">
                            <div class="flex items-center gap-2 w-full">
                                <flux:icon :icon="$notification->data['icon'] ?? 'bell'" class="size-4 text-zinc-500" />
                                <span class="font-medium text-sm text-zinc-900 dark:text-zinc-100">{{ $notification->data['title'] ?? 'Notification' }}</span>
                            </div>
                            <p class="text-xs text-zinc-600 dark:text-zinc-400 line-clamp-2 pl-6">
                                {{ $notification->data['message'] ?? '' }}
                            </p>
                            <span class="text-[10px] text-zinc-400 pl-6 mt-1">{{ $notification->created_at->diffForHumans() }}</span>
                        </a>
                        
                        <button wire:click.stop="delete('{{ $notification->id }}')" class="absolute top-3 right-2 p-1.5 text-zinc-400 hover:text-red-600 hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-md transition-colors z-10" title="Supprimer">
                            <flux:icon icon="trash" class="size-4" />
                        </button>
                    </div>
                @empty
                    <div class="p-8 text-center">
                        <flux:icon icon="bell-slash" class="size-8 text-zinc-300 mx-auto mb-2" />
                        <p class="text-sm text-zinc-500">Aucune notification</p>
                    </div>
                @endforelse
            </div>
        </flux:menu>
    </flux:dropdown>
</div>
