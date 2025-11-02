<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Couleur</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nom</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($types as $type)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $couleurClasses = [
                                        'blue' => 'bg-blue-100 text-blue-800 border-blue-200',
                                        'green' => 'bg-green-100 text-green-800 border-green-200',
                                        'purple' => 'bg-purple-100 text-purple-800 border-purple-200',
                                        'yellow' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                        'orange' => 'bg-orange-100 text-orange-800 border-orange-200',
                                        'red' => 'bg-red-100 text-red-800 border-red-200',
                                        'gray' => 'bg-gray-100 text-gray-800 border-gray-200',
                                        'indigo' => 'bg-indigo-100 text-indigo-800 border-indigo-200',
                                        'pink' => 'bg-pink-100 text-pink-800 border-pink-200',
                                    ];
                                    $couleur = $type->couleur ?? 'gray';
                                    $classes = $couleurClasses[$couleur] ?? $couleurClasses['gray'];
                                @endphp
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium border {{ $classes }}">
                                    {{ ucfirst($couleur) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $type->nom }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ $type->description ?? 'Aucune description' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end space-x-3">
                                    <button @click="initType({{ json_encode([
                                        'id' => $type->id,
                                        'nom' => $type->nom,
                                        'description' => $type->description ?? '',
                                        'couleur' => $type->couleur ?? ''
                                    ]) }}); showEditModal = true"
                                            class="text-blue-600 hover:text-blue-900">Modifier</button>
                                    <button @click="initType({{ json_encode([
                                        'id' => $type->id,
                                        'nom' => $type->nom,
                                        'description' => $type->description ?? '',
                                        'couleur' => $type->couleur ?? ''
                                    ]) }}); showDeleteModal = true"
                                            class="text-red-600 hover:text-red-900">Supprimer</button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                Aucun type d'interaction trouvé
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>