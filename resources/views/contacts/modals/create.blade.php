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
        
        <form action="{{ route('contacts.store') }}" method="POST">
            @csrf
            <div class="p-6">
                <h3 class="text-xl font-semibold text-zinc-900 dark:text-zinc-100 mb-6">
                    Créer un nouveau contact
                </h3>

                <div class="space-y-4">
                    <flux:input label="Nom" name="nom" id="nom" required placeholder="Nom du contact" />
                    <flux:input label="Prénom" name="prenom" id="prenom" placeholder="Prénom du contact" />
                    <flux:input label="Email" name="email" id="email" type="email" required placeholder="email@exemple.com" />
                    <flux:input label="Téléphone" name="telephone" id="telephone" required placeholder="+33 6 12 34 56 78" />
                    <flux:input label="Entreprise" name="entreprise" id="entreprise" placeholder="Nom de l'entreprise" />
                    
                    <div>
                        <label for="adresse" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Adresse</label>
                        <textarea name="adresse" id="adresse" rows="3" placeholder="Adresse complète" class="w-full rounded-lg border-zinc-300 bg-white text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100"></textarea>
                    </div>
                </div>
            </div>

            <div class="bg-zinc-50 dark:bg-zinc-800/50 px-6 py-4 rounded-b-xl flex justify-end space-x-3 border-t border-zinc-200 dark:border-zinc-800">
                <flux:button type="button" variant="ghost" @click="showCreateModal = false">Annuler</flux:button>
                <flux:button type="submit" variant="primary">Créer le contact</flux:button>
            </div>
        </form>
    </div>
</div>