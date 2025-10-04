<div x-show="showEditModal" 
     x-cloak
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

    <div class="relative bg-white w-full max-w-2xl rounded-xl shadow-2xl"
         x-show="showEditModal"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         @click.away="showEditModal = false">
        
        <form x-bind:action="'/admin/users/' + selectedUser?.id" 
              method="POST" 
              x-data="{ 
                  password: '',
                  password_confirmation: '',
                  checkPasswords() {
                      if (!this.password) return true;
                      return this.password === this.password_confirmation;
                  }
              }">
            @csrf
            @method('PUT')
            <div class="p-6">
                <h3 class="text-xl font-semibold text-gray-900 mb-6">
                    Modifier l'utilisateur
                </h3>

                <div class="space-y-4">
                    <div>
                        <label for="edit_name" class="block text-sm font-medium text-gray-700">Nom</label>
                        <input type="text" 
                               name="name" 
                               id="edit_name" 
                               required
                               x-model="selectedUser?.name"
                               class="mt-1 block w-full h-12 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-base">
                    </div>

                    <div>
                        <label for="edit_email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" 
                               name="email" 
                               id="edit_email" 
                               required
                               x-model="selectedUser?.email"
                               class="mt-1 block w-full h-12 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-base">
                    </div>

                    <div>
                        <label for="edit_password" class="block text-sm font-medium text-gray-700">
                            Nouveau mot de passe (laisser vide pour ne pas modifier)
                        </label>
                        <input type="password" 
                               name="password" 
                               id="edit_password"
                               x-model="password"
                               minlength="8"
                               class="mt-1 block w-full h-12 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-base">
                    </div>

                    <div class="mt-2">
                        <label for="edit_password_confirmation" class="block text-sm font-medium text-gray-700">
                            Confirmer le nouveau mot de passe
                        </label>
                        <input type="password" 
                               name="password_confirmation" 
                               id="edit_password_confirmation"
                               x-model="password_confirmation"
                               minlength="8"
                               class="mt-1 block w-full h-12 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-base">
                    </div>

                    <div class="mt-2 flex items-center">
                        <input type="checkbox" id="showEditPassword" class="mr-2" onclick="
                            document.getElementById('edit_password').type = this.checked ? 'text' : 'password';
                            document.getElementById('edit_password_confirmation').type = this.checked ? 'text' : 'password';
                        ">
                        <label for="showEditPassword" class="text-sm text-gray-700">Afficher le mot de passe</label>
                    </div>

                    <div>
                        <label for="edit_roles" class="block text-sm font-medium text-gray-700 mb-2">Rôles</label>
                        <select name="roles[]" 
                                id="edit_roles" 
                                required
                                class="mt-1 block w-full h-12 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-base">
                            <option value="" disabled>Choisir un rôle</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}"
                                        x-bind:selected="selectedUser?.roles?.some(r => r.name === '{{ $role->name }}')">
                                    {{ ucfirst($role->name) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 px-6 py-4 rounded-b-xl flex justify-end space-x-3">
                <button type="button" @click="showEditModal = false"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                    Annuler
                </button>
                <button type="submit"
                        x-bind:disabled="!checkPasswords()"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed">
                    Mettre à jour
                </button>
            </div>
        </form>
    </div>
</div>