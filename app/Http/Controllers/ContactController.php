<?php


namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index()
    {
        $contacts = Contact::with('user')->get();
        return view('contacts.index', compact('contacts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'prenom' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:contacts,email'],
            'telephone' => ['required', 'string', 'unique:contacts,telephone'],
            'entreprise' => ['nullable', 'string', 'max:255'],
            'adresse' => ['nullable', 'string'],
        ]);

        $contact = Contact::create([
            ...$validated,
            'user_id' => auth()->id()
        ]);

        return redirect()
            ->route('contacts.index')
            ->with('success', 'Le contact a été créé avec succès.');
    }

    public function update(Request $request, Contact $contact)
    {
        $validated = $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'prenom' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:contacts,email,' . $contact->id],
            'telephone' => ['required', 'string', 'unique:contacts,telephone,' . $contact->id],
            'entreprise' => ['nullable', 'string', 'max:255'],
            'adresse' => ['nullable', 'string'],
        ]);

        $contact->update($validated);

        return redirect()
            ->route('contacts.index')
            ->with('success', 'Le contact a été mis à jour avec succès.');
    }

    public function destroy(Contact $contact)
    {
        $contact->delete();

        return redirect()
            ->route('contacts.index')
            ->with('success', 'Le contact a été supprimé avec succès.');
    }
}