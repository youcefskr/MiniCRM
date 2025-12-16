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
        
        <form action="{{ route('admin.users.store') }}" method="POST" x-data="{ 
            password: '',
            password_confirmation: '',
            showPassword: false,
            isValid() {
                return this.password.length >= 8 && this.password === this.password_confirmation;
            }
        }">
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
                        <h3 class="text-xl font-bold text-zinc-900 dark:text-zinc-100">Nouvel utilisateur</h3>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">Créer un nouveau compte utilisateur</p>
                    </div>
                </div>
            </div>

            <!-- Body -->
            <div class="p-6 space-y-5">
                <!-- Nom -->
                <div>
                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Nom complet</label>
                    <input type="text" 
                           name="name" 
                           required
                           placeholder="Jean Dupont"
                           class="w-full px-4 py-3 bg-zinc-100 dark:bg-zinc-800 border-0 rounded-xl focus:ring-2 focus:ring-indigo-500 text-zinc-900 dark:text-zinc-100">
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Adresse email</label>
                    <input type="email" 
                           name="email" 
                           required
                           placeholder="jean.dupont@exemple.com"
                           class="w-full px-4 py-3 bg-zinc-100 dark:bg-zinc-800 border-0 rounded-xl focus:ring-2 focus:ring-indigo-500 text-zinc-900 dark:text-zinc-100">
                </div>

                <!-- Mot de passe -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Mot de passe</label>
                        <div class="relative">
                            <input :type="showPassword ? 'text' : 'password'" 
                                   name="password" 
                                   required
                                   x-model="password"
                                   minlength="8"
                                   placeholder="••••••••"
                                   class="w-full px-4 py-3 bg-zinc-100 dark:bg-zinc-800 border-0 rounded-xl focus:ring-2 focus:ring-indigo-500 text-zinc-900 dark:text-zinc-100">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Confirmer</label>
                        <input :type="showPassword ? 'text' : 'password'" 
                               name="password_confirmation" 
                               required
                               x-model="password_confirmation"
                               minlength="8"
                               placeholder="••••••••"
                               class="w-full px-4 py-3 bg-zinc-100 dark:bg-zinc-800 border-0 rounded-xl focus:ring-2 focus:ring-indigo-500 text-zinc-900 dark:text-zinc-100">
                    </div>
                </div>

                <!-- Afficher mot de passe -->
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" x-model="showPassword" class="rounded border-zinc-300 text-indigo-600 focus:ring-indigo-500">
                    <span class="text-sm text-zinc-600 dark:text-zinc-400">Afficher les mots de passe</span>
                </label>

                <!-- Validation mot de passe -->
                <div class="flex items-center gap-4 text-xs">
                    <span :class="password.length >= 8 ? 'text-green-600' : 'text-zinc-400'" class="flex items-center gap-1">
                        <svg x-show="password.length >= 8" class="size-4" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/>
                        </svg>
                        <svg x-show="password.length < 8" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <circle cx="12" cy="12" r="10" stroke-width="2"/>
                        </svg>
                        8 caractères min.
                    </span>
                    <span :class="password === password_confirmation && password.length > 0 ? 'text-green-600' : 'text-zinc-400'" class="flex items-center gap-1">
                        <svg x-show="password === password_confirmation && password.length > 0" class="size-4" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/>
                        </svg>
                        <svg x-show="!(password === password_confirmation && password.length > 0)" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <circle cx="12" cy="12" r="10" stroke-width="2"/>
                        </svg>
                        Mots de passe identiques
                    </span>
                </div>

                <!-- Rôle -->
                <div>
                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Rôle</label>
                    <select name="roles[]" 
                            required
                            class="w-full px-4 py-3 bg-zinc-100 dark:bg-zinc-800 border-0 rounded-xl focus:ring-2 focus:ring-indigo-500 text-zinc-900 dark:text-zinc-100">
                        <option value="">Sélectionner un rôle...</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}">{{ ucfirst($role->name) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Footer -->
            <div class="p-6 border-t border-zinc-200 dark:border-zinc-700 flex justify-end gap-3">
                <button type="button" @click="showCreateModal = false"
                        class="px-5 py-2.5 text-sm font-medium text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-xl transition">
                    Annuler
                </button>
                <button type="submit"
                        :disabled="!isValid()"
                        class="px-5 py-2.5 text-sm font-medium text-white bg-gradient-to-r from-indigo-500 to-purple-600 rounded-xl hover:from-indigo-600 hover:to-purple-700 disabled:opacity-50 disabled:cursor-not-allowed shadow-lg transition">
                    Créer l'utilisateur
                </button>
            </div>
        </form>
    </div>
</div>