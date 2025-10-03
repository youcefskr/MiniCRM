
<div x-show="showEditPermissionModal" 
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4"
     aria-labelledby="modal-title">
    
    <!-- Fond semi-transparent -->
    <div class="fixed inset-0 bg-black/30 backdrop-blur-sm"
         x-show="showEditPermissionModal"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="showEditPermissionModal = false">
    </div>

    <!-- Modal content -->
    <div class="relative bg-white w-full max-w-2xl rounded-xl shadow-2xl"
         x-show="showEditPermissionModal"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         @click.away="showEditPermissionModal = false">
        
        <form x-bind:action="'/admin/permissions/' + selectedPermission.id" method="POST">
            @csrf
            @method('PUT')
            <div class="p-6">
                <div class="flex items-start space-x-4">
                    <div class="flex-shrink-0 w-12 h-12 flex items-center justify-center rounded-full bg-blue-100">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-xl font-semibold text-gray-900">
                            Modifier la permission
                        </h3>
                        
                        <div class="mt-6">
                            <div>
                                <label for="edit_permission_name" class="block text-sm font-medium text-gray-700">
                                    Nom de la permission
                                </label>
                                <input type="text" 
                                       name="name" 
                                       id="edit_permission_name"
                                       x-model="selectedPermission.name"
                                       class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                                       placeholder="Ex: create-articles"
                                       required>
                                <p class="mt-2 text-sm text-gray-500">
                                    Format recommandé : action-ressource (ex: create-users, edit-posts)
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 px-6 py-4 rounded-b-xl flex justify-end space-x-3">
                <button type="button"
                        @click="showEditPermissionModal = false"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                    Annuler
                </button>
                <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                    Mettre à jour
                </button>
            </div>
        </form>
    </div>
</div>