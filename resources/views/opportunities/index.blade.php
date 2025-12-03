<x-layouts.app title="Opportunités">
    <div class="flex h-full flex-col gap-6" x-data="{ ...kanbanBoard(), showCreateModal: false }" @open-create-opportunity-modal.window="showCreateModal = true">
        @include('opportunities.modals.create')
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold">Pipeline des ventes</h1>
            <flux:button icon="plus" variant="primary" x-on:click="$dispatch('open-create-opportunity-modal')">Nouvelle opportunité</flux:button>
        </div>

        <!-- Statistiques -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-white dark:bg-zinc-900 overflow-hidden shadow-sm rounded-xl border border-zinc-200 dark:border-zinc-800 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-indigo-500 rounded-md p-3">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400 truncate">Total Opportunités</dt>
                            <dd class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">{{ $stats['total'] ?? 0 }}</dd>
                        </dl>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-zinc-900 overflow-hidden shadow-sm rounded-xl border border-zinc-200 dark:border-zinc-800 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400 truncate">Valeur Totale</dt>
                            <dd class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">{{ number_format($stats['total_value'] ?? 0, 0, ',', ' ') }} DA</dd>
                        </dl>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-zinc-900 overflow-hidden shadow-sm rounded-xl border border-zinc-200 dark:border-zinc-800 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400 truncate">Gagnées</dt>
                            <dd class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                                {{ $stats['par_stage']['won']->count ?? 0 }}
                                <span class="text-xs text-zinc-500 font-normal ml-1">({{ number_format($stats['par_stage']['won']->value ?? 0, 0, ',', ' ') }} DA)</span>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-zinc-900 overflow-hidden shadow-sm rounded-xl border border-zinc-200 dark:border-zinc-800 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-red-500 rounded-md p-3">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400 truncate">Perdues</dt>
                            <dd class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                                {{ $stats['par_stage']['lost']->count ?? 0 }}
                                <span class="text-xs text-zinc-500 font-normal ml-1">({{ number_format($stats['par_stage']['lost']->value ?? 0, 0, ',', ' ') }} DA)</span>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kanban Board -->
        <div class="flex h-full gap-4 overflow-x-auto pb-4">
            @foreach($stages as $key => $label)
                <div class="flex h-full w-80 min-w-[20rem] flex-col rounded-xl bg-zinc-50 p-4 dark:bg-zinc-900/50 border border-zinc-200 dark:border-zinc-800">
                    <div class="mb-4 flex items-center justify-between">
                        <h3 class="font-semibold text-zinc-900 dark:text-zinc-100">{{ $label }}</h3>
                        <span class="rounded-full bg-zinc-200 px-2 py-0.5 text-xs font-medium text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400">
                            {{ $groupedOpportunities->get($key)?->count() ?? 0 }}
                        </span>
                    </div>

                    <div 
                        class="flex flex-1 flex-col gap-3 overflow-y-auto min-h-[100px]"
                        x-on:drop="drop($event, '{{ $key }}')"
                        x-on:dragover.prevent="dragOver($event)"
                        x-on:dragleave="dragLeave($event)"
                    >
                        @forelse($groupedOpportunities->get($key) ?? [] as $opportunity)
                            <div 
                                draggable="true"
                                x-on:dragstart="dragStart($event, {{ $opportunity->id }})"
                                class="cursor-move rounded-lg border border-zinc-200 bg-white p-4 shadow-sm transition hover:shadow-md dark:border-zinc-700 dark:bg-zinc-800"
                            >
                                <div class="mb-2 flex items-start justify-between">
                                    <h4 class="font-medium text-zinc-900 dark:text-zinc-100">{{ $opportunity->title }}</h4>
                                    <flux:dropdown>
                                        <flux:button icon="ellipsis-horizontal" size="sm" variant="ghost" />
                                        <flux:menu>
                                            <flux:menu.item icon="pencil-square" href="{{ route('opportunities.edit', $opportunity) }}">Modifier</flux:menu.item>
                                            <form action="{{ route('opportunities.destroy', $opportunity) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr ?');">
                                                @csrf
                                                @method('DELETE')
                                                <flux:menu.item icon="trash" variant="danger" type="submit" as="button">Supprimer</flux:menu.item>
                                            </form>
                                        </flux:menu>
                                    </flux:dropdown>
                                </div>
                                
                                <div class="mb-3 text-sm text-zinc-500 dark:text-zinc-400">
                                    {{ $opportunity->contact->nom ?? '' }} {{ $opportunity->contact->prenom ?? 'Sans contact' }}
                                </div>

                                <div class="mb-3 flex items-center justify-between">
                                    <span class="font-bold text-zinc-900 dark:text-zinc-100">
                                        {{ number_format($opportunity->value, 0, ',', ' ') }} DA
                                    </span>
                                    <span class="rounded bg-blue-100 px-1.5 py-0.5 text-xs font-medium text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">
                                        {{ $opportunity->probability }}%
                                    </span>
                                </div>

                                <div class="flex items-center justify-between text-xs text-zinc-500 dark:text-zinc-400 border-t border-zinc-100 dark:border-zinc-700 pt-3 mt-2">
                                    <div class="flex items-center gap-1.5" title="Date de clôture estimée">
                                        <flux:icon.calendar class="size-3.5 text-zinc-400" />
                                        <span>{{ $opportunity->expected_close_date?->format('d/m/Y') ?? '-' }}</span>
                                    </div>
                                    <div class="flex items-center gap-1.5" title="Responsable">
                                        <flux:icon.user class="size-3.5 text-zinc-400" />
                                        <span class="truncate max-w-[80px]">{{ $opportunity->user->name ?? 'N/A' }}</span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="flex h-24 items-center justify-center rounded-lg border border-dashed border-zinc-300 text-sm text-zinc-400 dark:border-zinc-700">
                                Vide
                            </div>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <script>
        function kanbanBoard() {
            return {
                draggedId: null,

                dragStart(event, id) {
                    this.draggedId = id;
                    event.dataTransfer.effectAllowed = 'move';
                    event.dataTransfer.setData('text/plain', id);
                    event.target.classList.add('opacity-50');
                },

                dragOver(event) {
                    event.currentTarget.classList.add('bg-zinc-100', 'dark:bg-zinc-800');
                },

                dragLeave(event) {
                    event.currentTarget.classList.remove('bg-zinc-100', 'dark:bg-zinc-800');
                },

                async drop(event, newStage) {
                    event.currentTarget.classList.remove('bg-zinc-100', 'dark:bg-zinc-800');
                    const id = this.draggedId;
                    
                    // Optimistic UI update (optional, requires more complex DOM manipulation)
                    // For now, we'll reload or let Livewire handle it if we were using it fully.
                    // Since this is a standard controller view, we'll make an API call and reload.

                    try {
                        const response = await fetch(`/opportunities/${id}/update-stage`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({ stage: newStage })
                        });

                        if (response.ok) {
                            window.location.reload(); // Simple reload to reflect changes
                        } else {
                            alert('Erreur lors de la mise à jour.');
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert('Une erreur est survenue.');
                    }
                }
            };
        }
    </script>
</x-layouts.app>
