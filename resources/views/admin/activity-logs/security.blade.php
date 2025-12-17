<x-layouts.app title="Tableau de bord sécurité">
    <div class="flex h-full flex-col gap-6">
        
        <!-- Header -->
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.activity-logs.index') }}">
                <flux:button variant="ghost" size="sm" icon="arrow-left" />
            </a>
            <div>
                <flux:heading size="xl">Tableau de bord sécurité</flux:heading>
                <flux:subheading>Surveillance des activités sensibles et alertes</flux:subheading>
            </div>
        </div>

        <!-- Alert Banner -->
        @if($sensitiveAlerts->isNotEmpty())
            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4">
                <div class="flex items-center gap-3">
                    <flux:icon.exclamation-triangle class="size-6 text-red-600" />
                    <div>
                        <h4 class="font-medium text-red-800 dark:text-red-200">{{ $sensitiveAlerts->count() }} alertes critiques ce mois</h4>
                        <p class="text-sm text-red-700 dark:text-red-300">Des actions sensibles ont été détectées. Veuillez vérifier les détails ci-dessous.</p>
                    </div>
                </div>
            </div>
        @endif

        <div class="grid lg:grid-cols-2 gap-6">
            
            <!-- Failed Logins -->
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm overflow-hidden">
                <div class="p-6 border-b border-zinc-200 dark:border-zinc-800 flex items-center gap-3">
                    <flux:icon.shield-exclamation class="size-5 text-orange-500" />
                    <h3 class="font-semibold text-zinc-900 dark:text-zinc-100">Tentatives de connexion échouées</h3>
                </div>
                <div class="p-6">
                    @if($failedLogins->isNotEmpty())
                        <div class="space-y-3">
                            @foreach($failedLogins as $ip)
                                <div class="flex items-center justify-between p-3 bg-orange-50 dark:bg-orange-900/20 rounded-lg">
                                    <div>
                                        <div class="font-mono text-sm text-zinc-900 dark:text-zinc-100">{{ $ip->ip_address }}</div>
                                        <div class="text-xs text-zinc-500">Adresse IP suspecte</div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-lg font-bold text-orange-600">{{ $ip->attempts }}</div>
                                        <div class="text-xs text-zinc-500">tentatives</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <flux:icon.check-circle class="size-12 text-green-500 mx-auto mb-3" />
                            <p class="text-zinc-500">Aucune tentative suspecte détectée</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Recent Deletions -->
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm overflow-hidden">
                <div class="p-6 border-b border-zinc-200 dark:border-zinc-800 flex items-center gap-3">
                    <flux:icon.trash class="size-5 text-red-500" />
                    <h3 class="font-semibold text-zinc-900 dark:text-zinc-100">Suppressions récentes</h3>
                </div>
                <div class="divide-y divide-zinc-200 dark:divide-zinc-800 max-h-80 overflow-y-auto">
                    @forelse($recentDeletions as $deletion)
                        <a href="{{ route('admin.activity-logs.show', $deletion) }}" class="block p-4 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                            <div class="flex items-start justify-between">
                                <div>
                                    <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $deletion->model_name ?? 'Élément supprimé' }}</div>
                                    <div class="text-xs text-zinc-500">{{ $deletion->module_label }} • par {{ $deletion->user_name }}</div>
                                </div>
                                <div class="text-xs text-zinc-400">{{ $deletion->created_at->diffForHumans() }}</div>
                            </div>
                        </a>
                    @empty
                        <div class="p-8 text-center">
                            <p class="text-zinc-500">Aucune suppression récente</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Role Changes -->
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm overflow-hidden">
                <div class="p-6 border-b border-zinc-200 dark:border-zinc-800 flex items-center gap-3">
                    <flux:icon.shield-check class="size-5 text-purple-500" />
                    <h3 class="font-semibold text-zinc-900 dark:text-zinc-100">Modifications de rôles</h3>
                </div>
                <div class="divide-y divide-zinc-200 dark:divide-zinc-800 max-h-80 overflow-y-auto">
                    @forelse($roleChanges as $change)
                        <a href="{{ route('admin.activity-logs.show', $change) }}" class="block p-4 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                            <div class="flex items-start justify-between">
                                <div>
                                    <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $change->model_name }}</div>
                                    <div class="text-xs text-zinc-500">Modifié par {{ $change->user_name }}</div>
                                    @if($change->old_values && $change->new_values)
                                        <div class="mt-1 text-xs">
                                            <span class="text-red-500">{{ implode(', ', $change->old_values['roles'] ?? []) }}</span>
                                            <span class="text-zinc-400">→</span>
                                            <span class="text-green-500">{{ implode(', ', $change->new_values['roles'] ?? []) }}</span>
                                        </div>
                                    @endif
                                </div>
                                <div class="text-xs text-zinc-400">{{ $change->created_at->diffForHumans() }}</div>
                            </div>
                        </a>
                    @empty
                        <div class="p-8 text-center">
                            <p class="text-zinc-500">Aucune modification de rôle</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Sensitive Alerts -->
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm overflow-hidden">
                <div class="p-6 border-b border-zinc-200 dark:border-zinc-800 flex items-center gap-3">
                    <flux:icon.bell-alert class="size-5 text-red-500" />
                    <h3 class="font-semibold text-zinc-900 dark:text-zinc-100">Alertes critiques</h3>
                </div>
                <div class="divide-y divide-zinc-200 dark:divide-zinc-800 max-h-80 overflow-y-auto">
                    @forelse($sensitiveAlerts as $alert)
                        <a href="{{ route('admin.activity-logs.show', $alert) }}" class="block p-4 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                            <div class="flex items-start gap-3">
                                <div class="w-2 h-2 mt-2 rounded-full {{ $alert->severity === 'danger' ? 'bg-red-500' : 'bg-orange-500' }}"></div>
                                <div class="flex-1">
                                    <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $alert->action_label }} - {{ $alert->module_label }}</div>
                                    <div class="text-xs text-zinc-500">{{ Str::limit($alert->description, 60) }}</div>
                                    <div class="text-xs text-zinc-400 mt-1">{{ $alert->user_name }} • {{ $alert->created_at->diffForHumans() }}</div>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="p-8 text-center">
                            <flux:icon.check-circle class="size-12 text-green-500 mx-auto mb-3" />
                            <p class="text-zinc-500">Aucune alerte critique</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Security Tips -->
        <div class="bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl p-6 text-white">
            <h3 class="font-bold text-lg mb-3">💡 Conseils de sécurité</h3>
            <div class="grid md:grid-cols-3 gap-4">
                <div class="bg-white/10 rounded-lg p-4">
                    <h4 class="font-semibold mb-2">Surveiller les connexions</h4>
                    <p class="text-sm text-blue-100">Vérifiez régulièrement les tentatives de connexion échouées pour détecter les attaques.</p>
                </div>
                <div class="bg-white/10 rounded-lg p-4">
                    <h4 class="font-semibold mb-2">Auditer les suppressions</h4>
                    <p class="text-sm text-blue-100">Toute suppression de données doit être justifiée et tracée.</p>
                </div>
                <div class="bg-white/10 rounded-lg p-4">
                    <h4 class="font-semibold mb-2">Contrôler les accès</h4>
                    <p class="text-sm text-blue-100">Les modifications de rôles doivent être validées par un administrateur.</p>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
