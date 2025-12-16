<div x-show="showEditModal" 
     x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center p-4">
    
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm"
         x-show="showEditModal"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="showEditModal = false">
    </div>

    <div class="relative bg-white dark:bg-zinc-900 w-full max-w-lg rounded-2xl shadow-2xl"
         x-show="showEditModal"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95">
        
        <template x-if="selectedUser">
            <form :action="`{{ url('/admin/users') }}/${selectedUser.id}`" method="POST" x-data="{ 
                password: '',
                password_confirmation: '',
                showPassword: false
            }">
                @csrf
                @method('PUT')
                
                <!-- Header -->
                <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center gap-4">
                        <div class="size-14 rounded-full bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center text-white font-bold text-xl shadow-lg" 
                             x-text="selectedUser.name?.charAt(0)?.toUpperCase()">
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-zinc-900 dark:text-zinc-100">Modifier l'utilisateur</h3>
                            <p class="text-sm text-zinc-500 dark:text-zinc-400" x-text="selectedUser.email"></p>
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
                               :value="selectedUser.name"
                               class="w-full px-4 py-3 bg-zinc-100 dark:bg-zinc-800 border-0 rounded-xl focus:ring-2 focus:ring-indigo-500 text-zinc-900 dark:text-zinc-100">
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Adresse email</label>
                        <input type="email" 
                               name="email" 
                               required
                               :value="selectedUser.email"
                               class="w-full px-4 py-3 bg-zinc-100 dark:bg-zinc-800 border-0 rounded-xl focus:ring-2 focus:ring-indigo-500 text-zinc-900 dark:text-zinc-100">
                    </div>

                    <!-- Séparateur -->
                    <div class="relative">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-zinc-200 dark:border-zinc-700"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-3 bg-white dark:bg-zinc-900 text-zinc-500">Mot de passe (optionnel)</span>
                        </div>
                    </div>

                    <!-- Mot de passe -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Nouveau mot de passe</label>
                            <input :type="showPassword ? 'text' : 'password'" 
                                   name="password" 
                                   x-model="password"
                                   minlength="8"
                                   placeholder="••••••••"
                                   class="w-full px-4 py-3 bg-zinc-100 dark:bg-zinc-800 border-0 rounded-xl focus:ring-2 focus:ring-indigo-500 text-zinc-900 dark:text-zinc-100">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Confirmer</label>
                            <input :type="showPassword ? 'text' : 'password'" 
                                   name="password_confirmation" 
                                   x-model="password_confirmation"
                                   minlength="8"
                                   placeholder="••••••••"
                                   class="w-full px-4 py-3 bg-zinc-100 dark:bg-zinc-800 border-0 rounded-xl focus:ring-2 focus:ring-indigo-500 text-zinc-900 dark:text-zinc-100">
                        </div>
                    </div>

                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" x-model="showPassword" class="rounded border-zinc-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="text-sm text-zinc-600 dark:text-zinc-400">Afficher les mots de passe</span>
                    </label>

                    <!-- Rôle -->
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Rôle</label>
                        <select name="roles[]" 
                                required
                                class="w-full px-4 py-3 bg-zinc-100 dark:bg-zinc-800 border-0 rounded-xl focus:ring-2 focus:ring-indigo-500 text-zinc-900 dark:text-zinc-100">
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}" 
                                        :selected="selectedUser.roles?.some(r => r.name === '{{ $role->name }}')">
                                    {{ ucfirst($role->name) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Footer -->
                <div class="p-6 border-t border-zinc-200 dark:border-zinc-700 flex justify-end gap-3">
                    <button type="button" @click="showEditModal = false"
                            class="px-5 py-2.5 text-sm font-medium text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-xl transition">
                        Annuler
                    </button>
                    <button type="submit"
                            class="px-5 py-2.5 text-sm font-medium text-white bg-gradient-to-r from-indigo-500 to-purple-600 rounded-xl hover:from-indigo-600 hover:to-purple-700 shadow-lg transition">
                        Enregistrer
                    </button>
                </div>
            </form>
        </template>
    </div>
</div>