<div x-show="showCreateModal" 
     x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center p-4">
    
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm"
         x-show="showCreateModal"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="showCreateModal = false">
    </div>

    <div class="relative bg-white dark:bg-zinc-900 w-full max-w-lg rounded-2xl shadow-2xl"
         x-show="showCreateModal"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95">
        
        <form action="{{ route('contacts.store') }}" method="POST">
            @csrf
            
            <!-- Header -->
            <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
                <div class="flex items-center gap-4">
                    <div class="size-12 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center">
                        <svg class="size-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-zinc-900 dark:text-zinc-100">Nouveau contact</h3>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">Ajouter un contact à votre CRM</p>
                    </div>
                </div>
            </div>

            <!-- Body -->
            <div class="p-6 space-y-4 max-h-[60vh] overflow-y-auto">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Prénom</label>
                        <input type="text" 
                               name="prenom" 
                               placeholder="Jean"
                               class="w-full px-4 py-3 bg-zinc-100 dark:bg-zinc-800 border-0 rounded-xl focus:ring-2 focus:ring-indigo-500 text-zinc-900 dark:text-zinc-100">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Nom *</label>
                        <input type="text" 
                               name="nom" 
                               required
                               placeholder="Dupont"
                               class="w-full px-4 py-3 bg-zinc-100 dark:bg-zinc-800 border-0 rounded-xl focus:ring-2 focus:ring-indigo-500 text-zinc-900 dark:text-zinc-100">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Email *</label>
                    <div class="relative">
                        <input type="email" 
                               name="email" 
                               required
                               placeholder="jean.dupont@exemple.com"
                               class="w-full pl-11 pr-4 py-3 bg-zinc-100 dark:bg-zinc-800 border-0 rounded-xl focus:ring-2 focus:ring-indigo-500 text-zinc-900 dark:text-zinc-100">
                        <svg class="absolute left-4 top-3.5 size-5 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Téléphone *</label>
                    <div class="relative">
                        <input type="tel" 
                               name="telephone" 
                               required
                               placeholder="+33 6 12 34 56 78"
                               class="w-full pl-11 pr-4 py-3 bg-zinc-100 dark:bg-zinc-800 border-0 rounded-xl focus:ring-2 focus:ring-indigo-500 text-zinc-900 dark:text-zinc-100">
                        <svg class="absolute left-4 top-3.5 size-5 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Entreprise</label>
                    <div class="relative">
                        <input type="text" 
                               name="entreprise" 
                               placeholder="ACME Inc."
                               list="entreprises-list"
                               class="w-full pl-11 pr-4 py-3 bg-zinc-100 dark:bg-zinc-800 border-0 rounded-xl focus:ring-2 focus:ring-indigo-500 text-zinc-900 dark:text-zinc-100">
                        <svg class="absolute left-4 top-3.5 size-5 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        <datalist id="entreprises-list">
                            @foreach($entreprises as $entreprise)
                                <option value="{{ $entreprise }}">
                            @endforeach
                        </datalist>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Adresse</label>
                    <textarea name="adresse" 
                              rows="2"
                              placeholder="123 Rue de Paris, 75001 Paris"
                              class="w-full px-4 py-3 bg-zinc-100 dark:bg-zinc-800 border-0 rounded-xl focus:ring-2 focus:ring-indigo-500 text-zinc-900 dark:text-zinc-100 resize-none"></textarea>
                </div>
            </div>

            <!-- Footer -->
            <div class="p-6 border-t border-zinc-200 dark:border-zinc-700 flex justify-end gap-3">
                <button type="button" @click="showCreateModal = false"
                        class="px-5 py-2.5 text-sm font-medium text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-xl transition">
                    Annuler
                </button>
                <button type="submit"
                        class="px-5 py-2.5 text-sm font-medium text-white bg-gradient-to-r from-indigo-500 to-purple-600 rounded-xl hover:from-indigo-600 hover:to-purple-700 shadow-lg transition">
                    Créer le contact
                </button>
            </div>
        </form>
    </div>
</div>