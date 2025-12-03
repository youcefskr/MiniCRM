<x-layouts.app title="Modifier l'opportunité">
    <div class="flex h-full flex-col gap-6 max-w-2xl mx-auto w-full">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold">Modifier l'opportunité</h1>
            <flux:button icon="arrow-left" href="{{ route('opportunities.index') }}">Retour</flux:button>
        </div>

        <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 p-6">
            <form action="{{ route('opportunities.update', $opportunity) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <flux:input label="Titre de l'opportunité" type="text" name="title" id="title" required value="{{ old('title', $opportunity->title) }}" />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="contact_id" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Client</label>
                        <select name="contact_id" id="contact_id" required class="block w-full rounded-lg border-zinc-200 bg-white px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100">
                            @foreach($contacts as $contact)
                                <option value="{{ $contact->id }}" @selected($contact->id == $opportunity->contact_id)>{{ $contact->nom }} {{ $contact->prenom }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <flux:input label="Valeur estimée (DA)" type="number" name="value" id="value" required min="0" step="0.01" value="{{ old('value', $opportunity->value) }}" />
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="stage" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Étape</label>
                        <select name="stage" id="stage" required class="block w-full rounded-lg border-zinc-200 bg-white px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100">
                            @foreach($stages as $key => $label)
                                <option value="{{ $key }}" @selected($key == $opportunity->stage)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <flux:input label="Probabilité (%)" type="number" name="probability" id="probability" required min="0" max="100" value="{{ old('probability', $opportunity->probability) }}" />
                    </div>
                </div>

                <div>
                    <flux:input label="Date de clôture estimée" type="date" name="expected_close_date" id="expected_close_date" value="{{ old('expected_close_date', $opportunity->expected_close_date?->format('Y-m-d')) }}" />
                </div>

                <div>
                    <label for="notes" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Notes</label>
                    <textarea name="notes" id="notes" rows="3" class="block w-full rounded-lg border-zinc-200 bg-white px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100">{{ old('notes', $opportunity->notes) }}</textarea>
                </div>

                <div class="flex justify-end pt-4">
                    <flux:button type="submit" variant="primary">Enregistrer les modifications</flux:button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
