<?php


namespace App\Http\Controllers;

use App\Models\TypeInteraction;
use Illuminate\Http\Request;

class TypeInteractionController extends Controller
{
    public function index()
    {
        $types = TypeInteraction::all();
        return view('types-interactions.index', compact('types'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => ['required', 'string', 'max:255', 'unique:types_interactions'],
            'description' => ['nullable', 'string'],
            'couleur' => ['nullable', 'string', 'max:20'],
        ], [
            'nom.unique' => 'Ce type d\'interaction existe déjà.',
            'nom.required' => 'Le nom est requis.',
        ]);

        TypeInteraction::create($validated);

        return redirect()
            ->route('types-interactions.index')
            ->with('success', 'Le type d\'interaction a été créé avec succès.');
    }

    public function update(Request $request, TypeInteraction $typesInteraction)  
    {
        $validated = $request->validate([
            'nom' => ['required', 'string', 'max:255', 'unique:types_interactions,nom,' . $typesInteraction->id],
            'description' => ['nullable', 'string'],
            'couleur' => ['nullable', 'string', 'max:20'],
        ]);

        $typesInteraction->update($validated);

        return redirect()
            ->route('types-interactions.index')
            ->with('success', 'Le type d\'interaction a été mis à jour avec succès.');
    }
    public function destroy(TypeInteraction $typesInteraction)  
    {
        $typesInteraction->delete();

        return redirect()
            ->route('types-interactions.index')
            ->with('success', 'Le type d\'interaction a été supprimé avec succès.');
    }
}