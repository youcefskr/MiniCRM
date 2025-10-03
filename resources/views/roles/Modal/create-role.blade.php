
<div x-show="showRoleModal" 
             x-cloak
             class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4"
             aria-labelledby="modal-title">
            
            <!-- Fond semi-transparent -->
            <div class="fixed inset-0 bg-black/30 backdrop-blur-sm"
                 x-show="showRoleModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="showRoleModal = false">
            </div>

            <!-- Contenu du modal -->
            <div class="relative bg-white w-full max-w-2xl rounded-xl shadow-2xl"
                 x-show="showRoleModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 @click.away="showRoleModal = false">
                
                <form action="{{ route('admin.roles.store') }}" method="POST">
                    @csrf
                    <div class="p-6">
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0 w-12 h-12 flex items-center justify-center rounded-full bg-indigo-100">
                                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-xl font-semibold text-gray-900">
                                    Créer un nouveau rôle
                                </h3>
                                
                                <div class="mt-6 space-y-6">
                                    <div>
                                        <label for="role_name" class="block text-sm font-medium text-gray-700">
                                            Nom du rôle
                                        </label>
                                        <input type="text" 
                                               name="name" 
                                               id="role_name"
                                               class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200"
                                               placeholder="Ex: Éditeur"
                                               required>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Permissions associées
                                        </label>
                                        <div class="max-h-[240px] overflow-y-auto rounded-lg border border-gray-200 divide-y divide-gray-200">
                                            @foreach($permissions as $permission)
                                                <label class="flex items-center p-3 hover:bg-gray-50">
                                                    <input type="checkbox" 
                                                           name="permissions[]" 
                                                           value="{{ $permission->name }}"
                                                           class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                                    <span class="ml-3 text-sm text-gray-700">
                                                        {{ $permission->name }}
                                                    </span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-6 py-4 rounded-b-xl flex justify-end space-x-3">
                        <button type="button"
                                @click="showRoleModal = false"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                            Annuler
                        </button>
                        <button type="submit"
                                class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700">
                            Créer le rôle
                        </button>
                    </div>
                </form>
            </div>
        </div>
