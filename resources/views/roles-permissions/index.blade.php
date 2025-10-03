<x-layouts.app :title="__('Gestion des rôles et permissions')">
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Gestion des rôles et permissions') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-bold mb-4">📜 Rôles disponibles</h3>
                <ul>
                    @foreach($roles as $role)
                        <li class="border-b py-2">{{ $role->name }}</li>
                    @endforeach
                </ul>

                <h3 class="text-lg font-bold mt-6 mb-4">🔐 Permissions disponibles</h3>
                <ul>
                    @foreach($permissions as $permission)
                        <li class="border-b py-2">{{ $permission->name }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</x-app-layout>
