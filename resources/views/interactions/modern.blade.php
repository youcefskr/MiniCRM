<x-layouts.app :title="__('Interactions - Vue moderne')">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                {{ __('Interactions - Vue moderne') }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('interactions.all') }}" 
                   class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white dark:bg-gray-800 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700">
                    {{ __('Vue liste') }}
                </a>
                <a href="{{ route('interactions.dashboard') }}" 
                   class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                    </svg>
                    {{ __('Dashboard') }}
                </a>
            </div>
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
        filters: {
            type: '{{ request('type') ?? '' }}',
            date: '{{ request('date') ?? '' }}',
            contact: '{{ request('contact') ?? '' }}',
            search: '{{ request('search') ?? '' }}'
        },
        showNoteModal: false,
        selectedInteraction: null,
        selectedContact: null,
        noteContent: '',
        
        init() {
            // Auto-submit on filter change
            this.$watch('filters', () => {
                this.$refs.filterForm.submit();
            }, { deep: true });
        },
        
        openNoteModal(interactionId, contactId) {
            this.selectedInteraction = interactionId;
            this.selectedContact = contactId;
            this.noteContent = '';
            this.showNoteModal = true;
        },
        
        closeNoteModal() {
            this.showNoteModal = false;
            this.selectedInteraction = null;
            this.selectedContact = null;
            this.noteContent = '';
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
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Total interactions</dt>
                                <dd class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $stats['total'] ?? 0 }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Aujourd'hui</dt>
                                <dd class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $stats['today'] ?? 0 }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-purple-500 rounded-md p-3">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Par type</dt>
                                <dd class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                    {{ $stats['byType']->count() ?? 0 }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Cette semaine</dt>
                                <dd class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                    {{ $stats['recentContacts']->count() ?? 0 }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filtres -->
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6 mb-8">
                <form x-ref="filterForm" method="GET" action="{{ route('interactions.modern') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('Recherche') }}
                        </label>
                        <input type="text" 
                               x-model="filters.search"
                               name="search"
                               placeholder="{{ __('Contact, note...') }}"
                               class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('Type') }}
                        </label>
                        <select x-model="filters.type" 
                                name="type"
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">{{ __('Tous les types') }}</option>
                            @foreach($types ?? [] as $type)
                                <option value="{{ $type->id }}">{{ $type->nom }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('Date') }}
                        </label>
                        <select x-model="filters.date" 
                                name="date"
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">{{ __('Toutes les dates') }}</option>
                            <option value="today">{{ __('Aujourd\'hui') }}</option>
                            <option value="week">{{ __('Cette semaine') }}</option>
                            <option value="month">{{ __('Ce mois') }}</option>
                            <option value="year">{{ __('Cette année') }}</option>
                        </select>
                    </div>

                    <div class="flex items-end">
                        <a href="{{ route('interactions.modern') }}" 
                           class="w-full px-4 py-2 text-sm font-medium text-gray-700 bg-white dark:bg-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-600">
                            {{ __('Réinitialiser') }}
                        </a>
                    </div>
                </form>
            </div>

            <!-- Vue Timeline -->
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
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
                                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    @if($interaction->type->nom === 'Appel')
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                                    @elseif($interaction->type->nom === 'E-mail')
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                                    @else
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                    @endif
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <div>
                                                <div class="flex items-center justify-between">
                                                    <div class="flex items-center space-x-3">
                                                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                            <a href="{{ route('contacts.show', $interaction->contact) }}" class="hover:underline">
                                                                {{ $interaction->contact->nom }} {{ $interaction->contact->prenom }}
                                                            </a>
                                                        </p>
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $interaction->type->getBadgeClasses() }}">
                                                            {{ $interaction->type->nom }}
                                                        </span>
                                                    </div>
                                                    <time class="flex-shrink-0 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                        {{ $interaction->created_at->format('d/m/Y H:i') }}
                                                    </time>
                                                </div>
                                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                                    Par <span class="font-medium">{{ $interaction->user->name }}</span>
                                                </p>
                                            </div>
                                            <div class="mt-2 text-sm text-gray-700 dark:text-gray-300">
                                                @if($interaction->notes->count() > 0)
                                                    <div class="space-y-2">
                                                        @foreach($interaction->notes->take(2) as $note)
                                                            <div class="bg-gray-50 dark:bg-gray-700 rounded-md p-3">
                                                                <p>{{ Str::limit($note->contenu, 150) }}</p>
                                                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                                                    {{ $note->user->name }} - {{ $note->created_at->diffForHumans() }}
                                                                </p>
                                                            </div>
                                                        @endforeach
                                                        @if($interaction->notes->count() > 2)
                                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                                +{{ $interaction->notes->count() - 2 }} autre(s) note(s)
                                                            </p>
                                                        @endif
                                                    </div>
                                                @else
                                                    <p class="text-gray-400 dark:text-gray-500 italic">Aucune note</p>
                                                @endif
                                            </div>
                                            <div class="mt-3 flex space-x-3">
                                                <button @click="openNoteModal({{ $interaction->id }}, {{ $interaction->contact->id }})"
                                                        class="inline-flex items-center text-sm text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                                    </svg>
                                                    Ajouter une note
                                                </button>
                                                <a href="{{ route('contacts.interactions.index', $interaction->contact) }}"
                                                   class="inline-flex items-center text-sm text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200">
                                                    Voir toutes les notes
                                                </a>
                                            </div>
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

                <!-- Pagination -->
                @if(isset($interactions) && method_exists($interactions, 'links'))
                    <div class="mt-6">
                        {{ $interactions->links() }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Modal pour ajouter une note -->
        <div x-show="showNoteModal" 
             x-cloak
             class="fixed inset-0 z-50 overflow-y-auto"
             aria-labelledby="modal-title"
             role="dialog"
             aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showNoteModal"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                     @click="closeNoteModal()"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="showNoteModal"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form method="POST" :action="`/contacts/${selectedContact}/interactions/${selectedInteraction}/notes`">
                        @csrf
                        <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100" id="modal-title">
                                        Ajouter une note
                                    </h3>
                                    <div class="mt-4">
                                        <textarea x-model="noteContent"
                                                  name="note"
                                                  rows="4"
                                                  required
                                                  placeholder="Saisissez votre note..."
                                                  class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="submit"
                                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                                Ajouter
                            </button>
                            <button type="button"
                                    @click="closeNoteModal()"
                                    class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                Annuler
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <style>
        [x-cloak] { display: none !important; }
    </style>
</x-layouts.app>

