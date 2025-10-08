<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Interaction;
use App\Models\TypeInteraction;
use App\Models\NoteInteraction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InteractionController extends Controller
{
    public function index(Contact $contact)
    {
        $interactions = $contact->interactions()
            ->with(['type', 'notes.user', 'user'])
            ->latest()
            ->get();

        $types = TypeInteraction::all();
        
        return view('interactions.index', compact('contact', 'interactions', 'types'));
    }

    public function store(Request $request, Contact $contact)
    {
        $request->validate([
            'type_id' => 'required|exists:types_interactions,id',
            'note' => 'required|string',
        ]);

        $interaction = $contact->interactions()->create([
            'type_id' => $request->type_id,
            'user_id' => Auth::id(),
        ]);

        $interaction->notes()->create([
            'contenu' => $request->note,
            'user_id' => Auth::id(),
        ]);

        return redirect()
            ->route('contacts.interactions.index', $contact)
            ->with('success', 'L\'interaction a été enregistrée avec succès.');
    }

    public function addNote(Request $request, Contact $contact, Interaction $interaction)
    {
        $request->validate([
            'note' => 'required|string',
        ]);

        $interaction->notes()->create([
            'contenu' => $request->note,
            'user_id' => Auth::id(),
        ]);

        return redirect()
            ->route('contacts.interactions.index', $contact)
            ->with('success', 'La note a été ajoutée avec succès.');
    }

    public function destroy(Contact $contact, Interaction $interaction)
    {
        $interaction->delete();

        return redirect()
            ->route('contacts.interactions.index', $contact)
            ->with('success', 'L\'interaction a été supprimée avec succès.');
    }

    public function all()
    {
        $interactions = Interaction::with(['type', 'contact', 'notes.user', 'user'])
            ->latest()
            ->paginate(15);
        
        return view('interactions.all', compact('interactions'));
    }
}