<div x-show="showPermissionModal" 
             x-cloak
             class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4"
             aria-labelledby="modal-title">
            
            <!-- Fond semi-transparent -->
            <div class="fixed inset-0 bg-black/30 backdrop-blur-sm"
                 x-show="showPermissionModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="showPermissionModal = false">
            </div>

            <!-- Contenu du modal -->
            <div class="relative bg-white w-full max-w-2xl rounded-xl shadow-2xl"
                 x-show="showPermissionModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 @click.away="showPermissionModal = false">
                
                <form action="{{ route('admin.permissions.store') }}" method="POST">
                    @csrf
                    <div class="p-6">
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0 w-12 h-12 flex items-center justify-center rounded-full bg-emerald-100">
                                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-xl font-semibold text-gray-900">
                                    Créer une nouvelle permission
                                </h3>
                                
                                <div class="mt-6">
                                    <div>
                                        <label for="permission_name" class="block text-sm font-medium text-gray-700">
                                            Nom de la permission
                                        </label>
                                        <input type="text" 
                                               name="name" 
                                               id="permission_name"
                                               class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring focus:ring-emerald-200"
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
                                @click="showPermissionModal = false"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                            Annuler
                        </button>
                        <button type="submit"
                                class="px-4 py-2 text-sm font-medium text-white bg-emerald-600 rounded-lg hover:bg-emerald-700">
                            Créer la permission
                        </button>
                    </div>
                </form>
            </div>
        </div>