<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Notifications\ContactCreated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class ContactController extends Controller
{
    public function index()
    {
        $contacts = Contact::with('user')->get();
        return view('contacts.index', compact('contacts'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'nom' => ['required', 'string', 'max:255'],
                'prenom' => ['nullable', 'string', 'max:255'],
                'email' => ['required', 'email', 'unique:contacts,email'],
                'telephone' => ['required', 'string', 'unique:contacts,telephone'],
                'entreprise' => ['nullable', 'string', 'max:255'],
                'adresse' => ['nullable', 'string'],
            ]);

            // Vérifier si l'utilisateur est connecté
            if (!Auth::check()) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', 'Vous devez être connecté pour créer un contact.');
            }

            // Ajouter l'ID de l'utilisateur connecté
            $validated['user_id'] = Auth::id();
            
            // Créer le contact
            $contact = Contact::create($validated);

            if (!$contact) {
                throw new \Exception('Erreur lors de la création du contact.');
            }

            // Envoyer la notification au nouveau contact
            Notification::route('mail', $contact->email)
                ->notify(new ContactCreated($contact));

            return redirect()
                ->route('contacts.index')
                ->with('success', 'Le contact a été créé avec succès et un email lui a été envoyé.');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Une erreur est survenue : ' . $e->getMessage());
        }
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

    

    public function information(Request $request)
    {
        $search = $request->input('search');
        
        $contacts = Contact::with('user')
            ->when($search, function($query) use ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('nom', 'like', "%{$search}%")
                      ->orWhere('prenom', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('telephone', 'like', "%{$search}%")
                      ->orWhere('entreprise', 'like', "%{$search}%")
                      ->orWhere('adresse', 'like', "%{$search}%");
                });
            })
            ->orderBy('nom')
            ->paginate(12)
            ->withQueryString();
            
        return view('contacts.information', compact('contacts', 'search'));
    }
}