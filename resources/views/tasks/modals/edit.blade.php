<div x-show="showEditModal" x-cloak
     style="display: none"
     class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4">
    
    <div class="fixed inset-0 bg-black/30 backdrop-blur-sm" @click="showEditModal = false"></div>

    <div class="relative bg-white dark:bg-zinc-900 w-full max-w-2xl rounded-xl shadow-2xl border border-zinc-200 dark:border-zinc-800" @click.away="showEditModal = false">
        
        <form x-bind:action="'/tasks/' + selectedTask?.id" method="POST">
            @csrf
            @method('PUT')
            <div class="p-6">
                <h3 class="text-xl font-semibold text-zinc-900 dark:text-zinc-100 mb-6">
                    Modifier la tâche
                </h3>

                <div class="space-y-4">
                    <div>
                        <flux:input label="Titre" type="text" name="title" id="edit_title" required x-model="selectedTask.title" />
                    </div>

                    <div>
                        <label for="edit_description" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Description</label>
                        <textarea name="description" id="edit_description" rows="3" x-model="selectedTask.description" class="block w-full rounded-lg border-zinc-200 bg-white px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100"></textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <flux:input label="Date d'échéance" type="date" name="due_date" id="edit_due_date" x-model="selectedTask.due_date" />
                        </div>
                        
                        <div>
                            <label for="edit_priority" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Priorité</label>
                            <select name="priority" id="edit_priority" required x-model="selectedTask.priority" class="block w-full rounded-lg border-zinc-200 bg-white px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100">
                                <option value="basse">Basse</option>
                                <option value="normale">Normale</option>
                                <option value="haute">Haute</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="edit_status" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Statut</label>
                            <select name="status" id="edit_status" required x-model="selectedTask.status" class="block w-full rounded-lg border-zinc-200 bg-white px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100">
                                <option value="en attente">En attente</option>
                                <option value="en cours">En cours</option>
                                <option value="terminee">Terminée</option>
                            </select>
                        </div>

                        <div>
                            <label for="edit_user_id" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Assigner à</label>
                            <select name="user_id" id="edit_user_id" required x-model="selectedTask.user_id" class="block w-full rounded-lg border-zinc-200 bg-white px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100">
                                <template x-for="user in users" :key="user.id">
                                    <option :value="user.id" x-text="user.name"></option>
                                </template>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label for="edit_contact_id" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Lier à un contact (Optionnel)</label>
                        <select name="contact_id" id="edit_contact_id" x-model="selectedTask.contact_id" class="block w-full rounded-lg border-zinc-200 bg-white px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100">
                            <option value="">Aucun contact</option>
                            <template x-for="contact in contacts" :key="contact.id">
                                <option :value="contact.id" x-text="contact.nom + ' ' + (contact.prenom || '')"></option>
                            </template>
                        </select>
                    </div>
                </div>
            </div>

            <div class="bg-zinc-50 dark:bg-zinc-800/50 px-6 py-4 rounded-b-xl flex justify-end space-x-3 border-t border-zinc-200 dark:border-zinc-800">
                <flux:button variant="ghost" @click="showEditModal = false">Annuler</flux:button>
                <flux:button type="submit" variant="primary">Mettre à jour</flux:button>
            </div>
        </form>
    </div>
</div>