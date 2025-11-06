<x-layouts.app :title="__('Gestion des Tâches')">
    <div x-data="{ 
        showCreateModal: false,
        showEditModal: false,
        showDeleteModal: false,
        selectedTask: null,
        tasks: {{ Js::from($tasks) }},
        users: {{ Js::from($users) }},
        contacts: {{ Js::from($contacts) }},

        // 1. AJOUT DE LA VARIABLE POUR GÉRER L'AFFICHAGE
        viewMode: 'kanban', // 'kanban' ou 'table'

        // Filtres
        search: '',
        filterPriority: '',
        filterUser: '',
        filterContact: '',

        initTask(task) {
            this.selectedTask = task;
        },

        get filteredTasks() {
            // Le filtre s'applique aux deux vues (Kanban et Tableau)
            return this.tasks.filter(task => {
                const matchSearch = !this.search || 
                    task.title.toLowerCase().includes(this.search.toLowerCase()) ||
                    (task.description && task.description.toLowerCase().includes(this.search.toLowerCase()));
                
                const matchPriority = !this.filterPriority || task.priority === this.filterPriority;
                const matchUser = !this.filterUser || task.user_id == this.filterUser;
                const matchContact = !this.filterContact || task.contact_id == this.filterContact;
                
                return matchSearch && matchPriority && matchUser && matchContact;
            });
        },

        // Séparer par statut pour le Kanban
        get tasksToDo() {
            return this.filteredTasks.filter(t => t.status === 'en attente');
        },
        get tasksInProgress() {
            return this.filteredTasks.filter(t => t.status === 'en cours');
        },
        get tasksDone() {
            return this.filteredTasks.filter(t => t.status === 'terminee');
        }
    }">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ __('Gestion des Tâches') }}
            </h2>
            <button @click="showCreateModal = true" 
                    type="button"
                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 transition-colors duration-150">
                <svg class="w-5 h-5 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Ajouter une tâche
            </button>
        </div>

        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mb-6">
                <div class="bg-white rounded-lg shadow p-4">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <input type="search" x-model="search" placeholder="Rechercher titre/description..." class="rounded-md border-gray-300 shadow-sm">
                        <select x-model="filterPriority" class="rounded-md border-gray-300 shadow-sm">
                            <option value="">Toutes les priorités</option>
                            <option value="basse">Basse</option>
                            <option value="normale">Normale</option>
                            <option value="haute">Haute</option>
                        </select>
                        <select x-model="filterUser" class="rounded-md border-gray-300 shadow-sm">
                            <option value="">Tous les utilisateurs</option>
                            <template x-for="user in users" :key="user.id">
                                <option :value="user.id" x-text="user.name"></option>
                            </template>
                        </select>
                        <select x-model="filterContact" class="rounded-md border-gray-300 shadow-sm">
                            <option value="">Tous les contacts</option>
                            <template x-for="contact in contacts" :key="contact.id">
                                <option :value="contact.id" x-text="contact.nom + ' ' + (contact.prenom || '')"></option>
                            </template>
                        </select>
                    </div>
                </div>
            </div>

            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mb-4">
                <div class="inline-flex rounded-md shadow-sm" role="group">
                    <button @click="viewMode = 'kanban'" type="button" 
                            class="px-4 py-2 text-sm font-medium rounded-l-lg border transition-colors duration-150"
                            :class="viewMode === 'kanban' ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'">
                        <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                        Kanban
                    </button>
                    <button @click="viewMode = 'table'" type="button" 
                            class="px-4 py-2 text-sm font-medium rounded-r-lg border transition-colors duration-150"
                            :class="viewMode === 'table' ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'">
                        <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 6h18M3 14h18M3 18h18"></path></svg>
                        Tableau
                    </button>
                </div>
            </div>

            <div x-show="viewMode === 'kanban'" x-cloak>
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-3 gap-6">
                    
                    <div class="bg-gray-100 rounded-lg p-4">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">À Faire (<span x-text="tasksToDo.length"></span>)</h3>
                        <div class="space-y-4">
                            <template x-for="task in tasksToDo" :key="task.id">
                                @include('tasks.partials.task-card')
                            </template>
                        </div>
                    </div>

                    <div class="bg-gray-100 rounded-lg p-4">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">En Cours (<span x-text="tasksInProgress.length"></span>)</h3>
                        <div class="space-y-4">
                            <template x-for="task in tasksInProgress" :key="task.id">
                                @include('tasks.partials.task-card')
                            </template>
                        </div>
                    </div>

                    <div class="bg-gray-100 rounded-lg p-4">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Terminées (<span x-text="tasksDone.length"></span>)</h3>
                        <div class="space-y-4">
                            <template x-for="task in tasksDone" :key="task.id">
                                @include('tasks.partials.task-card')
                            </template>
                        </div>
                    </div>

                </div>
            </div>

            <div x-show="viewMode === 'table'" x-cloak>
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Titre</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Priorité</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Échéance</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Assigné à</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contact</th>
                                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <template x-for="task in filteredTasks" :key="task.id">
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm font-medium text-gray-900" x-text="task.title"></div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                                        :class="{
                                                            'bg-blue-100 text-blue-800': task.status === 'en attente',
                                                            'bg-yellow-100 text-yellow-800': task.status === 'en cours',
                                                            'bg-green-100 text-green-800': task.status === 'terminee'
                                                        }"
                                                        x-text="task.status">
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="font-medium"
                                                        :class="{
                                                            'text-red-600': task.priority === 'haute',
                                                            'text-yellow-600': task.priority === 'normale',
                                                            'text-blue-600': task.priority === 'basse'
                                                        }"
                                                        x-text="task.priority.charAt(0).toUpperCase() + task.priority.slice(1)">
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
                                                    x-text="task.due_date ? new Date(task.due_date).toLocaleDateString('fr-FR') : 'N/A'">
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
                                                    x-text="task.user ? task.user.name : 'N/A'">
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
                                                    x-text="task.contact ? (task.contact.nom + ' ' + (task.contact.prenom || '')) : 'N/A'">
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                    <div class="flex items-center justify-end space-x-3">
                                                        <button @click="initTask(task); showEditModal = true"
                                                                class="text-blue-600 hover:text-blue-900">Modifier</button>
                                                        <button @click="initTask(task); showDeleteModal = true"
                                                                class="text-red-600 hover:text-red-900">Supprimer</button>
                                                    </div>
                                                </td>
                                            </tr>
                                        </template>
                                        
                                        <tr x-show="filteredTasks.length === 0">
                                            <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                                Aucune tâche trouvée (selon vos filtres)
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        @include('tasks.modals.create')
        @include('tasks.modals.edit')
        @include('tasks.modals.delete')
    </div>
</x-layouts.app>