<x-layouts.app :title="__('Interactions - :name', ['name' => $contact->nom])">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                {{ __('Interactions avec') }} {{ $contact->nom }} {{ $contact->prenom }}
            </h2>
            <a href="{{ route('contacts.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white dark:bg-gray-800 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700">
                <svg class="w-5 h-5 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                {{ __('Retour aux contacts') }}
            </a>
        </div>
    </x-slot>

    @if(session('success'))
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mb-4">
            <div class="bg-green-50 dark:bg-green-900 border border-green-200 dark:border-green-700 text-green-800 dark:text-green-200 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    <div class="py-12" x-data="{
        showCreateModal: false,
        showEditModal: false,
        showDeleteModal: false,
        selectedInteraction: null,
        filters: {
            type: '{{ request('type') ?? '' }}',
            statut: '{{ request('statut') ?? '' }}',
            user: '{{ request('user') ?? '' }}',
            date_from: '{{ request('date_from') ?? '' }}',
            date_to: '{{ request('date_to') ?? '' }}'
        },
        initInteraction(interaction) {
            this.selectedInteraction = interaction;
        },
        init() {
            this.$watch('filters', () => {
                this.$refs.filterForm.submit();
            }, { deep: true });
        }
    }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Statistiques -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Total</dt>
                                <dd class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $stats['total'] ?? 0 }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>

                @foreach($stats['par_statut'] ?? [] as $stat)
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 rounded-md p-3
                                @if($stat->statut === 'réalisé') bg-green-500
                                @elseif($stat->statut === 'planifié') bg-yellow-500
                                @else bg-red-500
                                @endif">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">{{ ucfirst($stat->statut) }}</dt>
                                    <dd class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $stat->count }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Filtres -->
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6 mb-8">
                <form x-ref="filterForm" method="GET" action="{{ route('contacts.interactions.index', $contact) }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Type</label>
                        <select x-model="filters.type" name="type" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Tous</option>
                            @foreach($types as $type)
                                <option value="{{ $type->id }}">{{ $type->nom }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Statut</label>
                        <select x-model="filters.statut" name="statut" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Tous</option>
                            <option value="planifié">Planifié</option>
                            <option value="réalisé">Réalisé</option>
                            <option value="annulé">Annulé</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Utilisateur</label>
                        <select x-model="filters.user" name="user" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Tous</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date début</label>
                        <input type="date" x-model="filters.date_from" name="date_from" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date fin</label>
                        <input type="date" x-model="filters.date_to" name="date_to" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                </form>

                <div class="mt-4">
                    <a href="{{ route('contacts.interactions.index', $contact) }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200">
                        Réinitialiser les filtres
                    </a>
                </div>
            </div>

            <!-- Bouton créer interaction -->
            <div class="mb-6">
                <button @click="showCreateModal = true" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Nouvelle interaction
                </button>
            </div>

            <!-- Timeline des interactions -->
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-6">Historique des interactions</h3>
                
                <div class="flow-root">
                    <ul class="-mb-8">
                        @forelse($interactions as $index => $interaction)
                            <li>
                                <div class="relative pb-8">
                                    @if(!$loop->last)
                                        <span class="absolute top-5 left-5 -ml-px h-full w-0.5 bg-gray-200 dark:bg-gray-700" aria-hidden="true"></span>
                                    @endif
                                    <div class="relative flex items-start space-x-3">
                                        <div class="relative">
                                            <div class="h-10 w-10 rounded-full flex items-center justify-center ring-8 ring-white dark:ring-gray-800 {{ $interaction->type->getIconBgClasses() }}">
                                                @if($interaction->type->nom === 'Appel téléphonique')
                                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                                    </svg>
                                                @elseif($interaction->type->nom === 'E-mail')
                                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                                    </svg>
                                                @elseif($interaction->type->nom === 'Réunion')
                                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                    </svg>
                                                @else
                                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                                                    </svg>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <div>
                                                <div class="flex items-center justify-between">
                                                    <div class="flex items-center space-x-3">
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $interaction->type->getBadgeClasses() }}">
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
                                                    <div class="flex items-center space-x-2">
                                                        <time class="flex-shrink-0 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                            {{ $interaction->date_interaction ? $interaction->date_interaction->format('d/m/Y H:i') : $interaction->created_at->format('d/m/Y H:i') }}
                                                        </time>
                                                        <button @click="initInteraction({{ json_encode([
                                                            'id' => $interaction->id,
                                                            'type_id' => $interaction->type_id,
                                                            'statut' => $interaction->statut,
                                                            'date_interaction' => $interaction->date_interaction ? $interaction->date_interaction->format('Y-m-d') : '',
                                                            'heure_interaction' => $interaction->date_interaction ? $interaction->date_interaction->format('H:i') : '',
                                                            'user_id' => $interaction->user_id
                                                        ]) }}); showEditModal = true"
                                                                class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                            </svg>
                                                        </button>
                                                        <form action="{{ route('contacts.interactions.destroy', [$contact, $interaction]) }}" method="POST" class="inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette interaction ?')" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                                </svg>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                                    Par <span class="font-medium">{{ $interaction->user->name }}</span>
                                                </p>
                                            </div>
                                            <div class="mt-2 text-sm text-gray-700 dark:text-gray-300">
                                                @if($interaction->notes->count() > 0)
                                                    <div class="space-y-2">
                                                        @foreach($interaction->notes as $note)
                                                            <div class="bg-gray-50 dark:bg-gray-700 rounded-md p-3">
                                                                <p>{{ $note->contenu }}</p>
                                                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                                                    {{ $note->user->name }} - {{ $note->created_at->format('d/m/Y H:i') }}
                                                                </p>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                            <form action="{{ route('contacts.interactions.addNote', [$contact, $interaction]) }}" method="POST" class="mt-3">
                                                @csrf
                                                <div class="flex gap-2">
                                                    <input type="text" name="note" required placeholder="Ajouter une note..." 
                                                        class="flex-1 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                                    <button type="submit" class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                                        Ajouter
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @empty
                            <li class="text-center py-12">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">Aucune interaction</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    Aucune interaction ne correspond à vos critères de recherche.
                                </p>
                            </li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>

        <!-- Modal Créer -->
        @include('interactions.modals.create', ['contact' => $contact, 'types' => $types, 'users' => $users])

        <!-- Modal Éditer -->
        @include('interactions.modals.edit', ['contact' => $contact, 'types' => $types, 'users' => $users])
    </div>
</x-layouts.app>
