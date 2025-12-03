<div x-show="showEditModal" 
     x-cloak
     style="display: none"
     class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4"
     aria-labelledby="modal-title">
    
    <div class="fixed inset-0 bg-black/30 backdrop-blur-sm"
         x-show="showEditModal"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="showEditModal = false">
    </div>

    <div class="relative bg-white dark:bg-zinc-900 w-full max-w-2xl rounded-xl shadow-2xl border border-zinc-200 dark:border-zinc-800"
         x-show="showEditModal"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         @click.away="showEditModal = false">
        
        <form x-bind:action="'/contacts/' + selectedContact?.id" method="POST">
            @csrf
            @method('PUT')
            <div class="p-6">
                <h3 class="text-xl font-semibold text-zinc-900 dark:text-zinc-100 mb-6">
                    Modifier le contact
                </h3>

                <div class="space-y-4">
                    <flux:input label="Nom" name="nom" id="edit_nom" required x-model="selectedContact?.nom" />
                    <flux:input label="Prénom" name="prenom" id="edit_prenom" x-model="selectedContact?.prenom" />
                    <flux:input label="Email" name="email" id="edit_email" type="email" required x-model="selectedContact?.email" />
                    <flux:input label="Téléphone" name="telephone" id="edit_telephone" required x-model="selectedContact?.telephone" />
                    <flux:input label="Entreprise" name="entreprise" id="edit_entreprise" x-model="selectedContact?.entreprise" />
                    
                    <div>
                        <label for="edit_adresse" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Adresse</label>
                        <textarea name="adresse" id="edit_adresse" rows="3" x-model="selectedContact?.adresse" class="w-full rounded-lg border-zinc-300 bg-white text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100"></textarea>
                    </div>
                </div>
            </div>

            <div class="bg-zinc-50 dark:bg-zinc-800/50 px-6 py-4 rounded-b-xl flex justify-end space-x-3 border-t border-zinc-200 dark:border-zinc-800">
                <flux:button type="button" variant="ghost" @click="showEditModal = false">Annuler</flux:button>
                <flux:button type="submit" variant="primary">Mettre à jour</flux:button>
            </div>
        </form>
    </div>
</div>