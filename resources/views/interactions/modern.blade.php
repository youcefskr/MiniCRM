<x-layouts.app :title="__('Interactions')">
    <div class="flex h-full flex-col gap-8" x-data="{
        filters: {
            type: '{{ request('type') ?? '' }}',
            date: '{{ request('date') ?? '' }}',
            search: '{{ request('search') ?? '' }}'
        },
        showNoteModal: false,
        selectedInteraction: null,
        selectedContact: null,
        noteContent: '',
        
        init() {
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
        
        <!-- Header -->
        <div class="flex items-center justify-between">
            <flux:heading size="xl">Interactions</flux:heading>
            <flux:button href="{{ route('dashboard') }}" icon="arrow-left" variant="ghost">Retour au tableau de bord</flux:button>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-6 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-2 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                        <flux:icon.chat-bubble-left-right class="size-6 text-blue-600 dark:text-blue-400" />
                    </div>
                    <span class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Total</span>
                </div>
                <div class="text-3xl font-bold text-zinc-900 dark:text-zinc-100">{{ $stats['total'] ?? 0 }}</div>
                <div class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">Interactions totales</div>
            </div>

            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-6 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-2 bg-green-50 dark:bg-green-900/20 rounded-lg">
                        <flux:icon.calendar class="size-6 text-green-600 dark:text-green-400" />
                    </div>
                    <span class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Aujourd'hui</span>
                </div>
                <div class="text-3xl font-bold text-zinc-900 dark:text-zinc-100">{{ $stats['today'] ?? 0 }}</div>
                <div class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">Nouvelles interactions</div>
            </div>

            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-6 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-2 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
                        <flux:icon.tag class="size-6 text-purple-600 dark:text-purple-400" />
                    </div>
                    <span class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Types</span>
                </div>
                <div class="text-3xl font-bold text-zinc-900 dark:text-zinc-100">{{ $stats['byType']->count() ?? 0 }}</div>
                <div class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">Catégories actives</div>
            </div>

            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-6 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-2 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg">
                        <flux:icon.users class="size-6 text-yellow-600 dark:text-yellow-400" />
                    </div>
                    <span class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Cette semaine</span>
                </div>
                <div class="text-3xl font-bold text-zinc-900 dark:text-zinc-100">{{ $stats['recentContacts']->count() ?? 0 }}</div>
                <div class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">Contacts actifs</div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-6 shadow-sm">
            <form x-ref="filterForm" method="GET" action="{{ route('interactions.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <flux:input icon="magnifying-glass" x-model="filters.search" name="search" placeholder="Rechercher (contact, note...)" />
                
                <select x-model="filters.type" name="type" class="w-full rounded-lg border-zinc-200 bg-white px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100">
                    <option value="">Tous les types</option>
                    @foreach($types ?? [] as $type)
                        <option value="{{ $type->id }}">{{ $type->nom }}</option>
                    @endforeach
                </select>

                <select x-model="filters.date" name="date" class="w-full rounded-lg border-zinc-200 bg-white px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100">
                    <option value="">Toutes les dates</option>
                    <option value="today">Aujourd'hui</option>
                    <option value="week">Cette semaine</option>
                    <option value="month">Ce mois</option>
                    <option value="year">Cette année</option>
                </select>

                <div class="flex items-center">
                    <flux:button href="{{ route('interactions.index') }}" variant="ghost" class="w-full">Réinitialiser</flux:button>
                </div>
            </form>
        </div>

        <!-- Timeline -->
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm p-6">
            <div class="flow-root">
                <ul class="-mb-8">
                    @forelse($interactions as $interaction)
                        <li>
                            <div class="relative pb-8">
                                @if(!$loop->last)
                                    <span class="absolute top-5 left-5 -ml-px h-full w-0.5 bg-zinc-200 dark:bg-zinc-700" aria-hidden="true"></span>
                                @endif
                                <div class="relative flex items-start space-x-3">
                                    <div class="relative">
                                        <div class="h-10 w-10 rounded-full flex items-center justify-center ring-8 ring-white dark:ring-zinc-900 {{ $interaction->type->getIconBgClasses() }}">
                                            <flux:icon.chat-bubble-left-ellipsis class="size-5 text-white" />
                                        </div>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div>
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center space-x-3">
                                                    <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                                        <a href="{{ route('contacts.show', $interaction->contact) }}" class="hover:underline">
                                                            {{ $interaction->contact->nom }} {{ $interaction->contact->prenom }}
                                                        </a>
                                                    </p>
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $interaction->type->getBadgeClasses() }}">
                                                        {{ $interaction->type->nom }}
                                                    </span>
                                                </div>
                                                <time class="flex-shrink-0 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">
                                                    {{ $interaction->date_interaction ? $interaction->date_interaction->format('d/m/Y H:i') : $interaction->created_at->format('d/m/Y H:i') }}
                                                </time>
                                            </div>
                                            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                                                Par <span class="font-medium">{{ $interaction->user->name }}</span>
                                            </p>
                                        </div>
                                        <div class="mt-2 text-sm text-zinc-700 dark:text-zinc-300">
                                            @if($interaction->notes->count() > 0)
                                                <div class="space-y-2">
                                                    @foreach($interaction->notes->take(2) as $note)
                                                        <div class="bg-zinc-50 dark:bg-zinc-800/50 rounded-lg p-3 border border-zinc-100 dark:border-zinc-700">
                                                            <p>{{ Str::limit($note->contenu, 150) }}</p>
                                                            <p class="mt-1 text-xs text-zinc-400 dark:text-zinc-500">
                                                                {{ $note->user->name }} - {{ $note->created_at->diffForHumans() }}
                                                            </p>
                                                        </div>
                                                    @endforeach
                                                    @if($interaction->notes->count() > 2)
                                                        <p class="text-xs text-zinc-500 dark:text-zinc-400 italic">
                                                            +{{ $interaction->notes->count() - 2 }} autre(s) note(s)
                                                        </p>
                                                    @endif
                                                </div>
                                            @else
                                                <p class="text-zinc-400 dark:text-zinc-500 italic text-sm">Aucune note pour le moment.</p>
                                            @endif
                                        </div>
                                        <div class="mt-3 flex space-x-3">
                                            <button @click="openNoteModal({{ $interaction->id }}, {{ $interaction->contact->id }})"
                                                    class="inline-flex items-center text-sm text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 transition-colors">
                                                <flux:icon.pencil-square class="size-4 mr-1" />
                                                Ajouter une note
                                            </button>
                                            <a href="{{ route('contacts.interactions.index', $interaction->contact) }}"
                                               class="inline-flex items-center text-sm text-zinc-600 dark:text-zinc-400 hover:text-zinc-800 dark:hover:text-zinc-200 transition-colors">
                                                Voir le fil complet
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    @empty
                        <li class="text-center py-12">
                            <div class="flex flex-col items-center justify-center">
                                <flux:icon.chat-bubble-left class="size-12 text-zinc-300 dark:text-zinc-600 mb-3" />
                                <h3 class="text-sm font-medium text-zinc-900 dark:text-zinc-100">Aucune interaction</h3>
                                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                                    Aucune interaction ne correspond à vos critères de recherche.
                                </p>
                            </div>
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

    <!-- Modal Ajout Note -->
    <div x-show="showNoteModal" 
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         aria-labelledby="modal-title"
         role="dialog"
         aria-modal="true">
        
        <!-- Backdrop -->
        <div x-show="showNoteModal"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-black/30 backdrop-blur-sm transition-opacity"
             @click="closeNoteModal()"></div>

        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div x-show="showNoteModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="relative transform overflow-hidden rounded-xl bg-white dark:bg-zinc-900 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-zinc-200 dark:border-zinc-800">
                
                <form method="POST" :action="`/contacts/${selectedContact}/interactions/${selectedInteraction}/notes`">
                    @csrf
                    <div class="px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                                <h3 class="text-lg font-semibold leading-6 text-zinc-900 dark:text-zinc-100" id="modal-title">
                                    Ajouter une note
                                </h3>
                                <div class="mt-4">
                                    <textarea x-model="noteContent"
                                              name="note"
                                              rows="4"
                                              required
                                              placeholder="Saisissez votre note..."
                                              class="block w-full rounded-lg border-zinc-300 bg-white px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-zinc-50 dark:bg-zinc-800/50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 border-t border-zinc-100 dark:border-zinc-800">
                        <flux:button type="submit" variant="primary" class="w-full sm:ml-3 sm:w-auto">Ajouter</flux:button>
                        <flux:button type="button" variant="ghost" @click="closeNoteModal()" class="mt-3 w-full sm:mt-0 sm:w-auto">Annuler</flux:button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
