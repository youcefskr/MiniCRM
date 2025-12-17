<x-layouts.app title="Journal d'activité">
    <div class="flex h-full flex-col gap-6">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <flux:heading size="xl">Journal d'activité</flux:heading>
                <flux:subheading>Suivi complet de toutes les actions effectuées dans le CRM</flux:subheading>
            </div>
            <div class="flex gap-2">
                <flux:button icon="shield-check" variant="ghost" href="{{ route('admin.activity-logs.security') }}">
                    Sécurité
                </flux:button>
                <flux:button icon="arrow-down-tray" variant="ghost" href="{{ route('admin.activity-logs.export', request()->query()) }}">
                    Exporter
                </flux:button>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-8 gap-4">
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-4 shadow-sm">
                <div class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ $stats['total_today'] }}</div>
                <div class="text-xs text-zinc-500">Aujourd'hui</div>
            </div>
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-4 shadow-sm">
                <div class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ $stats['total_week'] }}</div>
                <div class="text-xs text-zinc-500">Cette semaine</div>
            </div>
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-4 shadow-sm">
                <div class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ $stats['total_month'] }}</div>
                <div class="text-xs text-zinc-500">Ce mois</div>
            </div>
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-4 shadow-sm">
                <div class="text-2xl font-bold text-green-600">{{ $stats['creates'] }}</div>
                <div class="text-xs text-zinc-500">Créations</div>
            </div>
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-4 shadow-sm">
                <div class="text-2xl font-bold text-blue-600">{{ $stats['updates'] }}</div>
                <div class="text-xs text-zinc-500">Modifications</div>
            </div>
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-4 shadow-sm">
                <div class="text-2xl font-bold text-red-600">{{ $stats['deletes'] }}</div>
                <div class="text-xs text-zinc-500">Suppressions</div>
            </div>
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-4 shadow-sm">
                <div class="text-2xl font-bold text-purple-600">{{ $stats['logins'] }}</div>
                <div class="text-xs text-zinc-500">Connexions</div>
            </div>
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-4 shadow-sm {{ $stats['sensitive_count'] > 0 ? 'border-red-300 dark:border-red-700' : '' }}">
                <div class="text-2xl font-bold text-orange-600">{{ $stats['sensitive_count'] }}</div>
                <div class="text-xs text-zinc-500">Alertes</div>
            </div>
        </div>

        <div class="grid lg:grid-cols-4 gap-6">
            <!-- Main Content (3/4) -->
            <div class="lg:col-span-3 space-y-6">
                
                <!-- Filters -->
                <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-4 shadow-sm">
                    <form method="GET" action="{{ route('admin.activity-logs.index') }}" class="flex flex-wrap items-end gap-3">
                        <div class="flex-1 min-w-[180px]">
                            <flux:input icon="magnifying-glass" name="search" placeholder="Rechercher..." 
                                value="{{ request('search') }}" size="sm" />
                        </div>
                        <div class="w-36">
                            <flux:select name="user_id" size="sm">
                                <option value="">Tous les utilisateurs</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" @selected(request('user_id') == $user->id)>{{ $user->name }}</option>
                                @endforeach
                            </flux:select>
                        </div>
                        <div class="w-32">
                            <flux:select name="action" size="sm">
                                <option value="">Toutes actions</option>
                                @foreach($actions as $action)
                                    <option value="{{ $action }}" @selected(request('action') == $action)>{{ ucfirst($action) }}</option>
                                @endforeach
                            </flux:select>
                        </div>
                        <div class="w-32">
                            <flux:select name="module" size="sm">
                                <option value="">Tous modules</option>
                                @foreach($modules as $module)
                                    <option value="{{ $module }}" @selected(request('module') == $module)>{{ ucfirst($module) }}</option>
                                @endforeach
                            </flux:select>
                        </div>
                        <div class="w-32">
                            <flux:select name="severity" size="sm">
                                <option value="">Toutes alertes</option>
                                <option value="sensitive" @selected(request('severity') == 'sensitive')>Sensibles</option>
                                <option value="danger" @selected(request('severity') == 'danger')>Critiques</option>
                                <option value="warning" @selected(request('severity') == 'warning')>Attention</option>
                            </flux:select>
                        </div>
                        <div class="w-32">
                            <flux:input type="date" name="date_from" value="{{ request('date_from') }}" size="sm" />
                        </div>
                        <div class="w-32">
                            <flux:input type="date" name="date_to" value="{{ request('date_to') }}" size="sm" />
                        </div>
                        <flux:button type="submit" variant="primary" size="sm" icon="funnel">Filtrer</flux:button>
                        <flux:button type="button" variant="ghost" size="sm" onclick="window.location='{{ route('admin.activity-logs.index') }}'">
                            Reset
                        </flux:button>
                    </form>
                </div>

                <!-- Activity Timeline -->
                <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm overflow-hidden">
                    <div class="divide-y divide-zinc-200 dark:divide-zinc-800">
                        @forelse($logs as $log)
                            <div class="p-4 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors {{ $log->is_sensitive ? 'bg-red-50/50 dark:bg-red-900/10' : '' }}">
                                <div class="flex items-start gap-4">
                                    <!-- Icon -->
                                    <div class="flex-shrink-0">
                                        @php
                                            $bgColors = [
                                                'green' => 'bg-green-100 text-green-600 dark:bg-green-900/30 dark:text-green-400',
                                                'blue' => 'bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400',
                                                'red' => 'bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400',
                                                'yellow' => 'bg-yellow-100 text-yellow-600 dark:bg-yellow-900/30 dark:text-yellow-400',
                                                'orange' => 'bg-orange-100 text-orange-600 dark:bg-orange-900/30 dark:text-orange-400',
                                                'purple' => 'bg-purple-100 text-purple-600 dark:bg-purple-900/30 dark:text-purple-400',
                                                'zinc' => 'bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400',
                                            ];
                                        @endphp
                                        <div class="w-10 h-10 rounded-full {{ $bgColors[$log->action_color] ?? $bgColors['zinc'] }} flex items-center justify-center">
                                            <flux:icon :name="$log->action_icon" class="size-5" />
                                        </div>
                                    </div>
                                    
                                    <!-- Content -->
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-start justify-between gap-4">
                                            <div>
                                                <p class="text-sm text-zinc-900 dark:text-zinc-100">
                                                    <span class="font-medium">{{ $log->user_name }}</span>
                                                    <span class="text-zinc-500">a effectué</span>
                                                    <span class="font-medium {{ $log->action === 'delete' ? 'text-red-600' : '' }}">{{ $log->action_label }}</span>
                                                    <span class="text-zinc-500">dans</span>
                                                    <span class="font-medium">{{ $log->module_label }}</span>
                                                </p>
                                                <p class="text-sm text-zinc-600 dark:text-zinc-400 mt-0.5">
                                                    {{ $log->description }}
                                                </p>
                                                @if($log->model_name)
                                                    <p class="text-xs text-zinc-500 mt-1">
                                                        Élément: <span class="font-medium">{{ $log->model_name }}</span>
                                                    </p>
                                                @endif
                                            </div>
                                            <div class="text-right flex-shrink-0">
                                                <div class="text-xs text-zinc-500">{{ $log->created_at->format('d/m/Y H:i') }}</div>
                                                <div class="text-xs text-zinc-400">{{ $log->created_at->diffForHumans() }}</div>
                                                @if($log->is_sensitive)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300 mt-1">
                                                        Sensible
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        <!-- Meta info -->
                                        <div class="flex items-center gap-4 mt-2 text-xs text-zinc-400">
                                            @if($log->ip_address)
                                                <span class="flex items-center gap-1">
                                                    <flux:icon.globe-alt class="size-3" />
                                                    {{ $log->ip_address }}
                                                </span>
                                            @endif
                                            @if($log->changed_fields)
                                                <span class="flex items-center gap-1">
                                                    <flux:icon.pencil class="size-3" />
                                                    {{ count($log->changed_fields) }} champ(s) modifié(s)
                                                </span>
                                            @endif
                                            <a href="{{ route('admin.activity-logs.show', $log) }}" class="text-blue-600 hover:underline">
                                                Détails →
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="p-12 text-center">
                                <flux:icon.document-text class="size-12 text-zinc-300 dark:text-zinc-600 mx-auto mb-4" />
                                <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100 mb-1">Aucune activité</h3>
                                <p class="text-zinc-500">Aucun log ne correspond aux critères de recherche</p>
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

            <!-- Sidebar (1/4) -->
            <div class="space-y-6">
                
                <!-- Recent Alerts -->
                @if($recentAlerts->isNotEmpty())
                    <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4">
                        <h3 class="font-semibold text-red-800 dark:text-red-200 mb-3 flex items-center gap-2">
                            <flux:icon.exclamation-triangle class="size-5" />
                            Alertes récentes
                        </h3>
                        <div class="space-y-3">
                            @foreach($recentAlerts as $alert)
                                <a href="{{ route('admin.activity-logs.show', $alert) }}" class="block p-2 bg-white/50 dark:bg-zinc-800/50 rounded-lg hover:bg-white dark:hover:bg-zinc-800 transition-colors">
                                    <div class="text-sm font-medium text-red-800 dark:text-red-200">{{ $alert->action_label }}</div>
                                    <div class="text-xs text-red-600 dark:text-red-400">{{ Str::limit($alert->description, 50) }}</div>
                                    <div class="text-xs text-red-500 mt-1">{{ $alert->created_at->diffForHumans() }}</div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Activity by Module -->
                <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-4 shadow-sm">
                    <h3 class="font-semibold text-zinc-900 dark:text-zinc-100 mb-3">Activité par module</h3>
                    <div class="space-y-3">
                        @forelse($moduleStats as $module => $count)
                            @php
                                $maxCount = max($moduleStats);
                                $percentage = $maxCount > 0 ? ($count / $maxCount * 100) : 0;
                            @endphp
                            <div>
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="text-zinc-600 dark:text-zinc-400">{{ ucfirst($module) }}</span>
                                    <span class="font-medium text-zinc-900 dark:text-zinc-100">{{ $count }}</span>
                                </div>
                                <div class="h-2 bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden">
                                    <div class="h-full bg-blue-500 rounded-full" style="width: {{ $percentage }}%"></div>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-zinc-500">Aucune donnée</p>
                        @endforelse
                    </div>
                </div>

                <!-- Active Users -->
                <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-4 shadow-sm">
                    <h3 class="font-semibold text-zinc-900 dark:text-zinc-100 mb-3">Utilisateurs actifs</h3>
                    <div class="space-y-3">
                        @forelse($activeUsers as $activeUser)
                            <a href="{{ route('admin.activity-logs.user-history', $activeUser->user_id) }}" class="flex items-center justify-between p-2 rounded-lg hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-purple-500 flex items-center justify-center text-white text-xs font-bold">
                                        {{ strtoupper(substr($activeUser->user_name, 0, 2)) }}
                                    </div>
                                    <span class="text-sm text-zinc-700 dark:text-zinc-300">{{ $activeUser->user_name }}</span>
                                </div>
                                <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $activeUser->actions_count }}</span>
                            </a>
                        @empty
                            <p class="text-sm text-zinc-500">Aucune donnée</p>
                        @endforelse
                    </div>
                </div>

                <!-- Quick Links -->
                <div class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl p-4 text-white">
                    <h3 class="font-bold mb-3">Liens rapides</h3>
                    <div class="space-y-2">
                        <a href="{{ route('admin.activity-logs.index', ['action' => 'delete']) }}" class="flex items-center justify-between p-2 bg-white/10 hover:bg-white/20 rounded-lg transition-colors">
                            <span>Suppressions</span>
                            <flux:icon.trash class="size-4" />
                        </a>
                        <a href="{{ route('admin.activity-logs.index', ['action' => 'login']) }}" class="flex items-center justify-between p-2 bg-white/10 hover:bg-white/20 rounded-lg transition-colors">
                            <span>Connexions</span>
                            <flux:icon.arrow-right-end-on-rectangle class="size-4" />
                        </a>
                        <a href="{{ route('admin.activity-logs.index', ['severity' => 'sensitive']) }}" class="flex items-center justify-between p-2 bg-white/10 hover:bg-white/20 rounded-lg transition-colors">
                            <span>Actions sensibles</span>
                            <flux:icon.shield-exclamation class="size-4" />
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
