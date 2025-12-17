<x-layouts.app title="Historique de {{ $user->name }}">
    <div class="flex h-full flex-col gap-6">
        
        <!-- Header -->
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.activity-logs.index') }}">
                <flux:button variant="ghost" size="sm" icon="arrow-left" />
            </a>
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-full bg-gradient-to-br from-blue-500 to-purple-500 flex items-center justify-center text-white text-xl font-bold">
                    {{ strtoupper(substr($user->name, 0, 2)) }}
                </div>
                <div>
                    <flux:heading size="xl">{{ $user->name }}</flux:heading>
                    <flux:subheading>{{ $user->email }}</flux:subheading>
                </div>
            </div>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-4 shadow-sm">
                <div class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ $stats['total'] }}</div>
                <div class="text-sm text-zinc-500">Total actions</div>
            </div>
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-4 shadow-sm">
                <div class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ $stats['this_month'] }}</div>
                <div class="text-sm text-zinc-500">Ce mois</div>
            </div>
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-4 shadow-sm">
                <div class="text-2xl font-bold text-green-600">{{ $stats['creates'] }}</div>
                <div class="text-sm text-zinc-500">Créations</div>
            </div>
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-4 shadow-sm">
                <div class="text-2xl font-bold text-blue-600">{{ $stats['updates'] }}</div>
                <div class="text-sm text-zinc-500">Modifications</div>
            </div>
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-4 shadow-sm">
                <div class="text-2xl font-bold text-red-600">{{ $stats['deletes'] }}</div>
                <div class="text-sm text-zinc-500">Suppressions</div>
            </div>
        </div>

        <!-- Activity List -->
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm overflow-hidden">
            <div class="p-6 border-b border-zinc-200 dark:border-zinc-800">
                <h3 class="font-semibold text-zinc-900 dark:text-zinc-100">Historique des activités</h3>
            </div>
            <div class="divide-y divide-zinc-200 dark:divide-zinc-800">
                @forelse($logs as $log)
                    <a href="{{ route('admin.activity-logs.show', $log) }}" class="block p-4 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors {{ $log->is_sensitive ? 'bg-red-50/50 dark:bg-red-900/10' : '' }}">
                        <div class="flex items-center gap-4">
                            @php
                                $bgColors = [
                                    'green' => 'bg-green-100 text-green-600',
                                    'blue' => 'bg-blue-100 text-blue-600',
                                    'red' => 'bg-red-100 text-red-600',
                                    'zinc' => 'bg-zinc-100 text-zinc-600',
                                ];
                            @endphp
                            <div class="w-10 h-10 rounded-full {{ $bgColors[$log->action_color] ?? $bgColors['zinc'] }} flex items-center justify-center">
                                <flux:icon :name="$log->action_icon" class="size-5" />
                            </div>
                            <div class="flex-1">
                                <div class="text-sm text-zinc-900 dark:text-zinc-100">
                                    <span class="font-medium">{{ $log->action_label }}</span>
                                    <span class="text-zinc-500">dans</span>
                                    <span class="font-medium">{{ $log->module_label }}</span>
                                </div>
                                <div class="text-sm text-zinc-500">{{ $log->description }}</div>
                            </div>
                            <div class="text-right">
                                <div class="text-xs text-zinc-500">{{ $log->created_at->format('d/m/Y H:i') }}</div>
                                <div class="text-xs text-zinc-400">{{ $log->created_at->diffForHumans() }}</div>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="p-12 text-center">
                        <p class="text-zinc-500">Aucune activité enregistrée</p>
                    </div>
                @endforelse
            </div>
            
            @if($logs->hasPages())
                <div class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-800">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
