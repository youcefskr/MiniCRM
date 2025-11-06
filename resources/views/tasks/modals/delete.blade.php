<div x-show="showDeleteModal" x-cloak
     class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4">
    
    <div class="fixed inset-0 bg-black/30 backdrop-blur-sm" @click="showDeleteModal = false"></div>

    <div class="relative bg-white w-full max-w-md rounded-xl shadow-2xl" @click.away="showDeleteModal = false">
        
        <div class="p-6">
            <div class="flex items-center space-x-4">
                <div class="flex-shrink-0 w-12 h-12 flex items-center justify-center rounded-full bg-red-100">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-medium text-gray-900">
                        Supprimer la tâche
                    </h3>
                    <p class="mt-2 text-sm text-gray-500">
                        Êtes-vous sûr de vouloir supprimer la tâche "<span x-text="selectedTask?.title" class="font-medium"></span>" ? Cette action est irréversible.
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-gray-50 px-6 py-4 rounded-b-xl flex justify-end space-x-3">
            <button type="button" @click="showDeleteModal = false"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                Annuler
            </button>
            <form x-bind:action="'/tasks/' + selectedTask?.id" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700">
                    Supprimer
                </button>
            </form>
        </div>
    </div>
</div>