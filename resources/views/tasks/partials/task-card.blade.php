<div class="bg-white rounded-lg shadow p-4 border-l-4" 
     :class="{
        'border-red-500': task.priority === 'haute',
        'border-yellow-500': task.priority === 'normale',
        'border-blue-500': task.priority === 'basse'
     }">

    <h4 class="font-medium text-gray-900" x-text="task.title"></h4>
    
    <p class="text-sm text-gray-600 mt-2" x-text="task.description || 'Pas de description'"></p>

    <div class="mt-4 text-sm text-gray-500 space-y-2">
        <div class="flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            Échéance: <span x-text="task.due_date ? new Date(task.due_date).toLocaleDateString('fr-FR') : 'N/A'"></span>
        </div>
        <div class="flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
            Assigné: <span x-text="task.user ? task.user.name : 'N/A'"></span>
        </div>
        <div class="flex items-center" x-show="task.contact">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H4a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
            Contact: <span x-text="task.contact ? (task.contact.nom + ' ' + task.contact.prenom) : ''"></span>
        </div>
    </div>

    <div class="mt-4 pt-2 border-t flex justify-end space-x-2">
        <button @click="initTask(task); showEditModal = true" class="text-sm text-blue-600 hover:text-blue-900">Éditer</button>
        <button @click="initTask(task); showDeleteModal = true" class="text-sm text-red-600 hover:text-red-900">Supprimer</button>
    </div>
</div>