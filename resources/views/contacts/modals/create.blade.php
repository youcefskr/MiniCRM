
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

    <div class="relative bg-white w-full max-w-2xl rounded-xl shadow-2xl"
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
                <h3 class="text-xl font-semibold text-gray-900 mb-6">
                    Créer un nouveau contact
                </h3>

                <div class="space-y-4">
                    <div>
                        <label for="nom" class="block text-sm font-medium text-gray-700">Nom</label>
                        <input type="text" 
                               name="nom" 
                               id="nom" 
                               required
                               class="mt-1 block w-full h-12 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-base">
                    </div>

                    <div>
                        <label for="prenom" class="block text-sm font-medium text-gray-700">Prénom</label>
                        <input type="text" 
                               name="prenom" 
                               id="prenom"
                               class="mt-1 block w-full h-12 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-base">
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" 
                               name="email" 
                               id="email" 
                               required
                               class="mt-1 block w-full h-12 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-base">
                    </div>

                    <div>
                        <label for="telephone" class="block text-sm font-medium text-gray-700">Téléphone</label>
                        <input type="text" 
                               name="telephone" 
                               id="telephone" 
                               required
                               class="mt-1 block w-full h-12 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-base">
                    </div>

                    <div>
                        <label for="entreprise" class="block text-sm font-medium text-gray-700">Entreprise</label>
                        <input type="text" 
                               name="entreprise" 
                               id="entreprise"
                               class="mt-1 block w-full h-12 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-base">
                    </div>

                    <div>
                        <label for="adresse" class="block text-sm font-medium text-gray-700">Adresse</label>
                        <textarea name="adresse" 
                                  id="adresse"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-base"
                                  rows="3"></textarea>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 px-6 py-4 rounded-b-xl flex justify-end space-x-3">
                <button type="button" @click="showCreateModal = false"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                    Annuler
                </button>
                <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                    Créer
                </button>
            </div>
        </form>
    </div>
</div>