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
        
        <form action="{{ route('opportunities.store') }}" method="POST">
            @csrf
            <div class="p-6">
                <h3 class="text-xl font-semibold text-zinc-900 dark:text-zinc-100 mb-6">
                    Nouvelle opportunité
                </h3>

                <div class="space-y-4">
                    <div>
                        <flux:input label="Titre de l'opportunité" type="text" name="title" id="title" required placeholder="Ex: Vente vitrine client X" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="contact_id" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Client</label>
                            <select name="contact_id" id="contact_id" required class="block w-full rounded-lg border-zinc-200 bg-white px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100">
                                <option value="" disabled selected>Sélectionner un client</option>
                                @foreach($contacts as $contact)
                                    <option value="{{ $contact->id }}">{{ $contact->nom }} {{ $contact->prenom }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <flux:input label="Valeur estimée (DA)" type="number" name="value" id="value" required min="0" step="0.01" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="stage" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Étape</label>
                            <select name="stage" id="stage" required class="block w-full rounded-lg border-zinc-200 bg-white px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100">
                                @foreach($stages as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <flux:input label="Probabilité (%)" type="number" name="probability" id="probability" required min="0" max="100" />
                        </div>
                    </div>

                    <div>
                        <flux:input label="Date de clôture estimée" type="date" name="expected_close_date" id="expected_close_date" />
                    </div>

                    <div>
                        <label for="notes" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Notes</label>
                        <textarea name="notes" id="notes" rows="3" placeholder="Détails supplémentaires..." class="block w-full rounded-lg border-zinc-200 bg-white px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100"></textarea>
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
