<x-layouts.app title="Détail de l'activité">
    <div class="flex h-full flex-col gap-6">
        
        <!-- Header -->
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.activity-logs.index') }}">
                <flux:button variant="ghost" size="sm" icon="arrow-left" />
            </a>
            <div>
                <flux:heading size="xl">Détail de l'activité</flux:heading>
                <flux:subheading>{{ $activityLog->created_at->format('d/m/Y H:i:s') }}</flux:subheading>
            </div>
        </div>

        <div class="grid lg:grid-cols-3 gap-6">
            
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- Activity Card -->
                <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm overflow-hidden {{ $activityLog->is_sensitive ? 'ring-2 ring-red-500' : '' }}">
                    <div class="p-6 border-b border-zinc-200 dark:border-zinc-800">
                        <div class="flex items-center gap-4">
                            @php
                                $bgColors = [
                                    'green' => 'bg-green-100 text-green-600',
                                    'blue' => 'bg-blue-100 text-blue-600',
                                    'red' => 'bg-red-100 text-red-600',
                                    'orange' => 'bg-orange-100 text-orange-600',
                                    'purple' => 'bg-purple-100 text-purple-600',
                                    'zinc' => 'bg-zinc-100 text-zinc-600',
                                ];
                            @endphp
                            <div class="w-14 h-14 rounded-xl {{ $bgColors[$activityLog->action_color] ?? $bgColors['zinc'] }} flex items-center justify-center">
                                <flux:icon :name="$activityLog->action_icon" class="size-7" />
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-zinc-900 dark:text-zinc-100">{{ $activityLog->action_label }}</h2>
                                <p class="text-zinc-500">{{ $activityLog->module_label }}</p>
                            </div>
                            @if($activityLog->is_sensitive)
                                <span class="ml-auto px-3 py-1 bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300 rounded-full text-sm font-medium">
                                    ⚠️ Action sensible
                                </span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="p-6">
                        <div class="prose dark:prose-invert max-w-none">
                            <p class="text-lg">{{ $activityLog->description }}</p>
                        </div>
                        
                        @if($activityLog->model_name)
                            <div class="mt-4 p-4 bg-zinc-50 dark:bg-zinc-800/50 rounded-lg">
                                <span class="text-sm text-zinc-500">Élément concerné:</span>
                                <span class="font-medium text-zinc-900 dark:text-zinc-100">{{ $activityLog->model_name }}</span>
                                @if($activityLog->model_id)
                                    <span class="text-zinc-400">(ID: {{ $activityLog->model_id }})</span>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Changed Fields -->
                @if($activityLog->action === 'update' && $activityLog->old_values && $activityLog->new_values)
                    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm overflow-hidden">
                        <div class="p-6 border-b border-zinc-200 dark:border-zinc-800">
                            <h3 class="font-semibold text-zinc-900 dark:text-zinc-100">Modifications effectuées</h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-800">
                                <thead class="bg-zinc-50 dark:bg-zinc-800/50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase">Champ</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase">Ancienne valeur</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase">Nouvelle valeur</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                                    @foreach($activityLog->changed_fields ?? [] as $field)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                                {{ $field }}
                                            </td>
                                            <td class="px-6 py-4 text-sm text-red-600 dark:text-red-400">
                                                <del>{{ $activityLog->old_values[$field] ?? '-' }}</del>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-green-600 dark:text-green-400">
                                                {{ $activityLog->new_values[$field] ?? '-' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                <!-- Old/New Values (for create/delete) -->
                @if(($activityLog->action === 'create' && $activityLog->new_values) || ($activityLog->action === 'delete' && $activityLog->old_values))
                    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm overflow-hidden">
                        <div class="p-6 border-b border-zinc-200 dark:border-zinc-800">
                            <h3 class="font-semibold text-zinc-900 dark:text-zinc-100">
                                {{ $activityLog->action === 'create' ? 'Données créées' : 'Données supprimées' }}
                            </h3>
                        </div>
                        <div class="p-6">
                            <pre class="bg-zinc-50 dark:bg-zinc-800 p-4 rounded-lg text-sm overflow-x-auto">{{ json_encode($activityLog->action === 'create' ? $activityLog->new_values : $activityLog->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                        </div>
                    </div>
                @endif

                <!-- Related Logs -->
                @if($relatedLogs->isNotEmpty())
                    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm overflow-hidden">
                        <div class="p-6 border-b border-zinc-200 dark:border-zinc-800">
                            <h3 class="font-semibold text-zinc-900 dark:text-zinc-100">Historique de cet élément</h3>
                        </div>
                        <div class="divide-y divide-zinc-200 dark:divide-zinc-800">
                            @foreach($relatedLogs as $log)
                                <a href="{{ route('admin.activity-logs.show', $log) }}" class="block p-4 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <span class="font-medium text-zinc-900 dark:text-zinc-100">{{ $log->action_label }}</span>
                                            <span class="text-zinc-500">par {{ $log->user_name }}</span>
                                        </div>
                                        <span class="text-sm text-zinc-400">{{ $log->created_at->format('d/m/Y H:i') }}</span>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                
                <!-- User Info -->
                <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm p-6">
                    <h3 class="font-semibold text-zinc-900 dark:text-zinc-100 mb-4">Utilisateur</h3>
                    @if($activityLog->user)
                        <a href="{{ route('admin.activity-logs.user-history', $activityLog->user) }}" class="flex items-center gap-4 group">
                            <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-500 to-purple-500 flex items-center justify-center text-white font-bold">
                                {{ strtoupper(substr($activityLog->user->name, 0, 2)) }}
                            </div>
                            <div>
                                <div class="font-medium text-zinc-900 dark:text-zinc-100 group-hover:text-blue-600">{{ $activityLog->user->name }}</div>
                                <div class="text-sm text-zinc-500">{{ $activityLog->user->email }}</div>
                            </div>
                        </a>
                    @else
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-full bg-zinc-200 dark:bg-zinc-700 flex items-center justify-center text-zinc-500">
                                <flux:icon.user class="size-6" />
                            </div>
                            <div>
                                <div class="font-medium text-zinc-900 dark:text-zinc-100">{{ $activityLog->user_name }}</div>
                                <div class="text-sm text-zinc-500">Utilisateur supprimé</div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Technical Details -->
                <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm p-6">
                    <h3 class="font-semibold text-zinc-900 dark:text-zinc-100 mb-4">Détails techniques</h3>
                    <dl class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-zinc-500">Date/Heure</dt>
                            <dd class="font-medium text-zinc-900 dark:text-zinc-100">{{ $activityLog->created_at->format('d/m/Y H:i:s') }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-zinc-500">Adresse IP</dt>
                            <dd class="font-mono text-zinc-900 dark:text-zinc-100">{{ $activityLog->ip_address ?? '-' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-zinc-500">Méthode HTTP</dt>
                            <dd class="font-mono text-zinc-900 dark:text-zinc-100">{{ $activityLog->method ?? '-' }}</dd>
                        </div>
                        @if($activityLog->url)
                            <div>
                                <dt class="text-zinc-500 mb-1">URL</dt>
                                <dd class="font-mono text-xs text-zinc-900 dark:text-zinc-100 break-all bg-zinc-50 dark:bg-zinc-800 p-2 rounded">{{ $activityLog->url }}</dd>
                            </div>
                        @endif
                        @if($activityLog->user_agent)
                            <div>
                                <dt class="text-zinc-500 mb-1">Navigateur</dt>
                                <dd class="text-xs text-zinc-900 dark:text-zinc-100 break-all">{{ Str::limit($activityLog->user_agent, 100) }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>

                <!-- Severity -->
                @if($activityLog->is_sensitive)
                    <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-6">
                        <div class="flex items-center gap-3 mb-3">
                            <flux:icon.shield-exclamation class="size-6 text-red-600" />
                            <h3 class="font-semibold text-red-800 dark:text-red-200">Action sensible</h3>
                        </div>
                        <p class="text-sm text-red-700 dark:text-red-300">
                            Cette action a été marquée comme sensible et a déclenché une alerte de sécurité.
                        </p>
                        <div class="mt-3">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $activityLog->severity === 'danger' ? 'bg-red-200 text-red-800' : 'bg-orange-200 text-orange-800' }}">
                                Sévérité: {{ ucfirst($activityLog->severity) }}
                            </span>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.app>
