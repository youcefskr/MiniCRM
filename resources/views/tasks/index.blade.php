<x-layouts.app title="Tâches">
    <div class="flex h-full flex-col gap-6" 
         x-data="{ 
            ...kanbanBoard(), 
            showCreateModal: false, 
            showEditModal: false,
            selectedTask: {},
            users: {{ Js::from($users) }},
            contacts: {{ Js::from($contacts) }}
         }" 
         @open-create-task-modal.window="showCreateModal = true"
    >
        @include('tasks.modals.create')
        @include('tasks.modals.edit')

        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold">Tableau des tâches</h1>
            <flux:button icon="plus" variant="primary" x-on:click="showCreateModal = true">Nouvelle tâche</flux:button>
        </div>

        <!-- Statistiques -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-white dark:bg-zinc-900 overflow-hidden shadow-sm rounded-xl border border-zinc-200 dark:border-zinc-800 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-indigo-500 rounded-md p-3">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400 truncate">Total</dt>
                            <dd class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">{{ $stats['total'] ?? 0 }}</dd>
                        </dl>
                    </div>
                </div>
            </div>

            @foreach($stats['par_statut'] ?? [] as $stat)
                <div class="bg-white dark:bg-zinc-900 overflow-hidden shadow-sm rounded-xl border border-zinc-200 dark:border-zinc-800 p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 rounded-md p-3
                            @if($stat->statut === 'terminee') bg-green-500
                            @elseif($stat->statut === 'en cours') bg-blue-500
                            @else bg-gray-500
                            @endif">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                @if($stat->statut === 'terminee')
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                @elseif($stat->statut === 'en cours')
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                @else
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                @endif
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400 truncate">
                                    @if($stat->statut === 'terminee') Terminée
                                    @elseif($stat->statut === 'en cours') En cours
                                    @else En attente
                                    @endif
                                </dt>
                                <dd class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">{{ $stat->count }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Kanban Board -->
        <div class="flex h-full gap-4 overflow-x-auto pb-4">
            @foreach($statuses as $key => $label)
                <div class="flex h-full w-80 min-w-[20rem] flex-col rounded-xl bg-zinc-50 p-4 dark:bg-zinc-900/50 border border-zinc-200 dark:border-zinc-800">
                    <div class="mb-4 flex items-center justify-between">
                        <h3 class="font-semibold text-zinc-900 dark:text-zinc-100">{{ $label }}</h3>
                        <span class="rounded-full bg-zinc-200 px-2 py-0.5 text-xs font-medium text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400">
                            {{ $groupedTasks->get($key)?->count() ?? 0 }}
                        </span>
                    </div>

                    <div 
                        class="flex flex-1 flex-col gap-3 overflow-y-auto min-h-[100px]"
                        x-on:drop="drop($event, '{{ $key }}')"
                        x-on:dragover.prevent="dragOver($event)"
                        x-on:dragleave="dragLeave($event)"
                    >
                        @forelse($groupedTasks->get($key) ?? [] as $task)
                            <div 
                                draggable="true"
                                x-on:dragstart="dragStart($event, {{ $task->id }})"
                                class="cursor-move rounded-lg border border-zinc-200 bg-white p-4 shadow-sm transition hover:shadow-md dark:border-zinc-700 dark:bg-zinc-800"
                            >
                                <div class="mb-2 flex items-start justify-between">
                                    <h4 class="font-medium text-zinc-900 dark:text-zinc-100">{{ $task->title }}</h4>
                                    <flux:dropdown>
                                        <flux:button icon="ellipsis-horizontal" size="sm" variant="ghost" />
                                        <flux:menu>
                                            <flux:menu.item icon="pencil-square" x-on:click="selectedTask = {{ Js::from($task) }}; showEditModal = true">Modifier</flux:menu.item>
                                            <form action="{{ route('tasks.destroy', $task) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr ?');">
                                                @csrf
                                                @method('DELETE')
                                                <flux:menu.item icon="trash" variant="danger" type="submit" as="button">Supprimer</flux:menu.item>
                                            </form>
                                        </flux:menu>
                                    </flux:dropdown>
                                </div>
                                
                                <div class="mb-3 text-sm text-zinc-500 dark:text-zinc-400">
                                    {{ $task->contact->nom ?? '' }} {{ $task->contact->prenom ?? 'Sans contact' }}
                                </div>

                                <div class="mb-3 flex items-center justify-between">
                                    @if($task->priority === 'haute')
                                        <span class="rounded bg-red-100 px-1.5 py-0.5 text-xs font-medium text-red-700 dark:bg-red-900/30 dark:text-red-300">Haute</span>
                                    @elseif($task->priority === 'normale')
                                        <span class="rounded bg-blue-100 px-1.5 py-0.5 text-xs font-medium text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">Normale</span>
                                    @else
                                        <span class="rounded bg-gray-100 px-1.5 py-0.5 text-xs font-medium text-gray-700 dark:bg-gray-900/30 dark:text-gray-300">Basse</span>
                                    @endif
                                </div>

                                <div class="flex items-center justify-between text-xs text-zinc-500 dark:text-zinc-400 border-t border-zinc-100 dark:border-zinc-700 pt-3 mt-2">
                                    <div class="flex items-center gap-1.5" title="Date d'échéance">
                                        <flux:icon.calendar class="size-3.5 text-zinc-400" />
                                        <span>{{ $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('d/m/Y') : '-' }}</span>
                                    </div>
                                    <div class="flex items-center gap-1.5" title="Assigné à">
                                        <flux:icon.user class="size-3.5 text-zinc-400" />
                                        <span class="truncate max-w-[80px]">{{ $task->user->name ?? 'N/A' }}</span>
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

                async drop(event, newStatus) {
                    event.currentTarget.classList.remove('bg-zinc-100', 'dark:bg-zinc-800');
                    const id = this.draggedId;
                    
                    try {
                        const response = await fetch(`/tasks/${id}/update-status`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({ status: newStatus })
                        });

                        if (response.ok) {
                            window.location.reload(); 
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