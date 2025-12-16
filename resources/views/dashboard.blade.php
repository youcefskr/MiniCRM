<x-layouts.app title="Tableau de bord">
    <div class="flex h-full flex-col gap-8">
        
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl">Bonjour, {{ Auth::user()->name }}</flux:heading>
                <flux:subheading>Voici un aperçu de votre activité aujourd'hui.</flux:subheading>
            </div>
            <div class="flex gap-2">
                <flux:button icon="plus" variant="primary" href="{{ route('contacts.index') }}">Nouveau contact</flux:button>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Contacts -->
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-6 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-2 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                        <flux:icon.users class="size-6 text-blue-600 dark:text-blue-400" />
                    </div>
                    <span class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Total</span>
                </div>
                <div class="text-3xl font-bold text-zinc-900 dark:text-zinc-100">{{ $stats['contacts_count'] }}</div>
                <div class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">Contacts enregistrés</div>
            </div>

            <!-- Opportunités -->
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-6 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-2 bg-green-50 dark:bg-green-900/20 rounded-lg">
                        <flux:icon.currency-dollar class="size-6 text-green-600 dark:text-green-400" />
                    </div>
                    <span class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Pipeline</span>
                </div>
                <div class="text-3xl font-bold text-zinc-900 dark:text-zinc-100">{{ number_format($stats['opportunities_value'], 0, ',', ' ') }} DA</div>
                <div class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">{{ $stats['opportunities_count'] }} opportunités en cours</div>
            </div>

            <!-- Tâches -->
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-6 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-2 bg-orange-50 dark:bg-orange-900/20 rounded-lg">
                        <flux:icon.clipboard-document-list class="size-6 text-orange-600 dark:text-orange-400" />
                    </div>
                    <span class="text-xs font-medium text-zinc-500 dark:text-zinc-400">À faire</span>
                </div>
                <div class="text-3xl font-bold text-zinc-900 dark:text-zinc-100">{{ $stats['tasks_pending'] }}</div>
                <div class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">Tâches en attente</div>
            </div>

            <!-- Interactions -->
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-6 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-2 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
                        <flux:icon.chat-bubble-left-right class="size-6 text-purple-600 dark:text-purple-400" />
                    </div>
                    <span class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Aujourd'hui</span>
                </div>
                <div class="text-3xl font-bold text-zinc-900 dark:text-zinc-100">{{ $stats['interactions_today'] }}</div>
                <div class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">Interactions réalisées</div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Left Column (2/3) -->
            <div class="lg:col-span-2 space-y-8">
                
                <!-- Recent Opportunities -->
                <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm overflow-hidden">
                    <div class="p-6 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between">
                        <h3 class="font-semibold text-zinc-900 dark:text-zinc-100">Opportunités récentes</h3>
                        <flux:button variant="ghost" size="sm" href="{{ route('opportunities.index') }}">Voir tout</flux:button>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-800">
                            <thead class="bg-zinc-50 dark:bg-zinc-800/50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">Titre</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">Client</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">Valeur</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">Étape</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-zinc-900 divide-y divide-zinc-200 dark:divide-zinc-800">
                                @forelse($recentOpportunities as $opportunity)
                                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $opportunity->title }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-zinc-600 dark:text-zinc-400">{{ $opportunity->contact->nom }} {{ $opportunity->contact->prenom }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ number_format($opportunity->value, 0, ',', ' ') }} DA</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
                                                {{ ucfirst($opportunity->stage) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 text-center text-sm text-zinc-500">Aucune opportunité récente</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Recent Interactions -->
                <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="font-semibold text-zinc-900 dark:text-zinc-100">Dernières interactions</h3>
                        <flux:button variant="ghost" size="sm" href="{{ route('interactions.index') }}">Voir tout</flux:button>
                    </div>
                    <div class="flow-root">
                        <ul class="-mb-8">
                            @forelse($recentInteractions as $interaction)
                                <li>
                                    <div class="relative pb-8">
                                        @if(!$loop->last)
                                            <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-zinc-200 dark:bg-zinc-700" aria-hidden="true"></span>
                                        @endif
                                        <div class="relative flex space-x-3">
                                            <div>
                                                <span class="h-8 w-8 rounded-full flex items-center justify-center ring-8 ring-white dark:ring-zinc-900 {{ $interaction->type->getIconBgClasses() }}">
                                                    <flux:icon.chat-bubble-left-ellipsis class="size-4 text-white" />
                                                </span>
                                            </div>
                                            <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                                <div>
                                                    <p class="text-sm text-zinc-500 dark:text-zinc-400">
                                                        <span class="font-medium text-zinc-900 dark:text-zinc-100">{{ $interaction->type->nom }}</span>
                                                        avec <a href="{{ route('contacts.show', $interaction->contact) }}" class="font-medium text-zinc-900 dark:text-zinc-100 hover:underline">{{ $interaction->contact->nom }}</a>
                                                    </p>
                                                </div>
                                                <div class="text-right text-sm whitespace-nowrap text-zinc-500 dark:text-zinc-400">
                                                    <time datetime="{{ $interaction->date_interaction }}">{{ $interaction->date_interaction->diffForHumans() }}</time>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @empty
                                <li class="text-center text-sm text-zinc-500">Aucune interaction récente</li>
                            @endforelse
                        </ul>
                    </div>
                </div>

            </div>

            <!-- Right Column (1/3) -->
            <div class="space-y-8">
                
                <!-- Tasks -->
                <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="font-semibold text-zinc-900 dark:text-zinc-100">Tâches à venir</h3>
                        <flux:button variant="ghost" size="sm" href="{{ route('tasks.index') }}">Voir tout</flux:button>
                    </div>
                    <div class="space-y-4">
                        @forelse($recentTasks as $task)
                            <div class="flex items-start gap-3 p-3 rounded-lg hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors border border-transparent hover:border-zinc-200 dark:hover:border-zinc-700">
                                <div class="flex-shrink-0 mt-0.5">
                                    <div class="h-4 w-4 rounded border-2 border-zinc-300 dark:border-zinc-600"></div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100 truncate">
                                        {{ $task->title }}
                                    </p>
                                    <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">
                                        {{ $task->due_date ? $task->due_date->format('d/m/Y') : 'Pas de date' }}
                                        @if($task->contact)
                                            • {{ $task->contact->nom }}
                                        @endif
                                    </p>
                                </div>
                                <div>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                        @if($task->priority === 'haute') bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300
                                        @elseif($task->priority === 'normale') bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300
                                        @else bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300
                                        @endif">
                                        {{ ucfirst($task->priority) }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-sm text-zinc-500 py-4">Aucune tâche en attente</div>
                        @endforelse
                    </div>
                    <div class="mt-6 pt-4 border-t border-zinc-100 dark:border-zinc-800">
                        <flux:button class="w-full" variant="ghost" icon="plus" href="{{ route('tasks.index') }}">Ajouter une tâche</flux:button>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl shadow-md p-6 text-white">
                    <h3 class="font-bold text-lg mb-2">Actions rapides</h3>
                    <p class="text-indigo-100 text-sm mb-6">Accédez rapidement aux fonctionnalités principales.</p>
                    
                    <div class="space-y-3">
                        <a href="{{ route('contacts.index') }}" class="flex items-center justify-between p-3 bg-white/10 hover:bg-white/20 rounded-lg transition-colors backdrop-blur-sm">
                            <span class="font-medium">Créer un contact</span>
                            <flux:icon.plus class="size-4" />
                        </a>
                        <a href="{{ route('opportunities.index') }}" class="flex items-center justify-between p-3 bg-white/10 hover:bg-white/20 rounded-lg transition-colors backdrop-blur-sm">
                            <span class="font-medium">Nouvelle opportunité</span>
                            <flux:icon.currency-dollar class="size-4" />
                        </a>
                        <a href="{{ route('tasks.index') }}" class="flex items-center justify-between p-3 bg-white/10 hover:bg-white/20 rounded-lg transition-colors backdrop-blur-sm">
                            <span class="font-medium">Planifier une tâche</span>
                            <flux:icon.calendar class="size-4" />
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-layouts.app>
