<div x-show="showCreateModal" 
     x-cloak
     style="display: none"
     class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4"
     aria-labelledby="modal-title">
    
    <div class="fixed inset-0 bg-black/30 backdrop-blur-sm"
         x-show="showCreateModal"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="showCreateModal = false">
    </div>

    <div class="relative bg-white dark:bg-zinc-900 w-full max-w-2xl rounded-xl shadow-2xl border border-zinc-200 dark:border-zinc-800"
         x-show="showCreateModal"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         @click.away="showCreateModal = false">
        
        <form action="{{ route('tasks.store') }}" method="POST">
            @csrf
            <div class="p-6">
                <h3 class="text-xl font-semibold text-zinc-900 dark:text-zinc-100 mb-6">
                    Nouvelle tâche
                </h3>

                @if ($errors->any())
                    <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-600 rounded-lg">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="space-y-4">
                    <div>
                        <flux:input label="Titre" type="text" name="title" id="title" required />
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Description</label>
                        <textarea name="description" id="description" rows="3" class="block w-full rounded-lg border-zinc-200 bg-white px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100"></textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <flux:input label="Date d'échéance" type="date" name="due_date" id="due_date" />
                        </div>
                        
                        <div>
                            <label for="priority" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Priorité</label>
                            <select name="priority" id="priority" required class="block w-full rounded-lg border-zinc-200 bg-white px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100">
                                <option value="basse">Basse</option>
                                <option value="normale" selected>Normale</option>
                                <option value="haute">Haute</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="status" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Statut</label>
                            <select name="status" id="status" required class="block w-full rounded-lg border-zinc-200 bg-white px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100">
                                <option value="en attente" selected>À faire</option>
                                <option value="en cours">En cours</option>
                                <option value="terminee">Terminée</option>
                            </select>
                        </div>

                        <div>
                            <label for="user_id" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Assigné à</label>
                            <select name="user_id" id="user_id" required class="block w-full rounded-lg border-zinc-200 bg-white px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100">
                                <template x-for="user in users" :key="user.id">
                                    <option :value="user.id" x-text="user.name"></option>
                                </template>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label for="contact_id" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Contact associé</label>
                        <select name="contact_id" id="contact_id" class="block w-full rounded-lg border-zinc-200 bg-white px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100">
                            <option value="">Aucun contact</option>
                            <template x-for="contact in contacts" :key="contact.id">
                                <option :value="contact.id" x-text="contact.nom + ' ' + (contact.prenom || '')"></option>
                            </template>
                        </select>
                    </div>
                </div>
            </div>

            <div class="bg-zinc-50 dark:bg-zinc-800/50 px-6 py-4 rounded-b-xl flex justify-end space-x-3 border-t border-zinc-200 dark:border-zinc-800">
                <flux:button variant="ghost" @click="showCreateModal = false">Annuler</flux:button>
                <flux:button type="submit" variant="primary">Créer</flux:button>
            </div>
        </form>
    </div>
</div>