<x-layouts.app :title="__('Dashboard Interactions')">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                {{ __('Dashboard Interactions') }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('interactions.all') }}" 
                   class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white dark:bg-gray-800 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700">
                    {{ __('Vue liste') }}
                </a>
                <a href="{{ route('interactions.modern') }}" 
                   class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white dark:bg-gray-800 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700">
                    {{ __('Vue timeline') }}
                </a>
            </div>
        </div>
    </x-slot>

    @if(session('success'))
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mb-4">
            <div class="bg-green-50 dark:bg-green-900 border border-green-200 dark:border-green-700 text-green-800 dark:text-green-200 px-4 py-3 rounded-lg" role="alert">
                {{ session('success') }}
            </div>
        </div>
    @endif

    <div class="py-12" x-data="{
        filters: {
            type: '{{ request('type') ?? '' }}',
            statut: '{{ request('statut') ?? '' }}',
            date_from: '{{ request('date_from') ?? '' }}',
            date_to: '{{ request('date_to') ?? '' }}',
            search: '{{ request('search') ?? '' }}'
        },
        showFilters: false,
        init() {
            this.$watch('filters', () => {
                this.$refs.filterForm.submit();
            }, { deep: true });
        }
    }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Statistiques principales -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-transform duration-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-blue-100 text-sm font-medium">Total Interactions</p>
                            <p class="text-3xl font-bold mt-2">{{ $stats['total'] ?? 0 }}</p>
                        </div>
                        <div class="bg-white/20 rounded-lg p-3">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-transform duration-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-green-100 text-sm font-medium">Aujourd'hui</p>
                            <p class="text-3xl font-bold mt-2">{{ $stats['today'] ?? 0 }}</p>
                        </div>
                        <div class="bg-white/20 rounded-lg p-3">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-transform duration-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-purple-100 text-sm font-medium">Planifiées</p>
                            <p class="text-3xl font-bold mt-2">{{ $stats['planifiees'] ?? 0 }}</p>
                        </div>
                        <div class="bg-white/20 rounded-lg p-3">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-transform duration-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-orange-100 text-sm font-medium">Cette semaine</p>
                            <p class="text-3xl font-bold mt-2">{{ $stats['week'] ?? 0 }}</p>
                        </div>
                        <div class="bg-white/20 rounded-lg p-3">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Barre de recherche et filtres -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 mb-8">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Filtres et recherche</h3>
                    <button @click="showFilters = !showFilters" 
                            class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                    </button>
                </div>

                <form x-ref="filterForm" method="GET" action="{{ route('interactions.dashboard') }}">
                    <div class="mb-4">
                        <input type="text" 
                               x-model="filters.search"
                               name="search"
                               placeholder="Rechercher par contact, note..."
                               class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2">
                    </div>

                    <div x-show="showFilters" x-transition class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Type</label>
                            <select x-model="filters.type" name="type" 
                                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Tous</option>
                                @foreach($types ?? [] as $type)
                                    <option value="{{ $type->id }}">{{ $type->nom }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Statut</label>
                            <select x-model="filters.statut" name="statut"
                                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Tous</option>
                                <option value="planifié">Planifié</option>
                                <option value="réalisé">Réalisé</option>
                                <option value="annulé">Annulé</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date début</label>
                            <input type="date" x-model="filters.date_from" name="date_from"
                                   class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date fin</label>
                            <input type="date" x-model="filters.date_to" name="date_to"
                                   class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('interactions.dashboard') }}" 
                           class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
                            Réinitialiser
                        </a>
                    </div>
                </form>
            </div>

            <!-- Vue en grille/cartes -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($interactions as $interaction)
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300 overflow-hidden border-l-4 {{ $interaction->type->getIconBgClasses() }}">
                        <div class="p-6">
                            <!-- En-tête de la carte -->
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-2 mb-2">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $interaction->type->getBadgeClasses() }}">
                                            {{ $interaction->type->nom }}
                                        </span>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($interaction->statut === 'réalisé') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300
                                            @elseif($interaction->statut === 'planifié') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300
                                            @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300
                                            @endif">
                                            {{ ucfirst($interaction->statut) }}
                                        </span>
                                    </div>
                                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">
                                        <a href="{{ route('contacts.show', $interaction->contact) }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                                            {{ $interaction->contact->nom }} {{ $interaction->contact->prenom }}
                                        </a>
                                    </h3>
                                </div>
                                <div class="ml-2">
                                    <div class="h-10 w-10 rounded-full flex items-center justify-center {{ $interaction->type->getIconBgClasses() }}">
                                        @if(str_contains($interaction->type->nom, 'Appel'))
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                            </svg>
                                        @elseif(str_contains($interaction->type->nom, 'E-mail'))
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                            </svg>
                                        @else
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Informations -->
                            <div class="space-y-3 mb-4">
                                <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>{{ $interaction->date_interaction ? $interaction->date_interaction->format('d/m/Y H:i') : $interaction->created_at->format('d/m/Y H:i') }}</span>
                                </div>

                                <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    <span>{{ $interaction->user->name }}</span>
                                </div>
                            </div>

                            <!-- Notes -->
                            @if($interaction->notes->count() > 0)
                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3 mb-4">
                                    <p class="text-sm text-gray-700 dark:text-gray-300 line-clamp-2">
                                        {{ $interaction->notes->first()->contenu }}
                                    </p>
                                    @if($interaction->notes->count() > 1)
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                                            +{{ $interaction->notes->count() - 1 }} autre(s) note(s)
                                        </p>
                                    @endif
                                </div>
                            @endif

                            <!-- Actions -->
                            <div class="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-gray-700">
                                <a href="{{ route('contacts.interactions.index', $interaction->contact) }}" 
                                   class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 font-medium">
                                    Voir détails →
                                </a>
                                <div class="flex items-center space-x-2">
                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $interaction->created_at->diffForHumans() }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12">
                        <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                        </svg>
                        <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-gray-100">Aucune interaction</h3>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                            Aucune interaction ne correspond à vos critères de recherche.
                        </p>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if(isset($interactions) && method_exists($interactions, 'links'))
                <div class="mt-8">
                    {{ $interactions->links() }}
                </div>
            @endif

            <!-- Graphiques de répartition par type -->
            @if(isset($stats['byType']) && $stats['byType']->count() > 0)
                <div class="mt-8 bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Répartition par type</h3>
                    <div class="space-y-3">
                        @foreach($stats['byType'] as $type)
                            @php
                                $count = $type->interactions_count ?? 0;
                                $percentage = $stats['total'] > 0 ? ($count / $stats['total']) * 100 : 0;
                            @endphp
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $type->nom }}</span>
                                    <span class="text-sm text-gray-500 dark:text-gray-400">{{ $count }} ({{ number_format($percentage, 1) }}%)</span>
                                </div>
                                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 overflow-hidden">
                                    @php $widthPercent = round($percentage); @endphp
                                    <div class="h-2 rounded-full {{ $type->getIconBgClasses() }}" style="width: {{ $widthPercent }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>

