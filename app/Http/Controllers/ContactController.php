<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Notifications\ContactCreated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class ContactController extends Controller
{
    public function index(Request $request)
    {
        $query = Contact::with('user');

        // Filtrage par recherche
        if ($search = $request->input('q')) {
            $query->where(function($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('prenom', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('telephone', 'like', "%{$search}%")
                  ->orWhere('entreprise', 'like', "%{$search}%");
            });
        }

        // Filtrage par entreprise
        if ($entreprise = $request->input('entreprise')) {
            $query->where('entreprise', $entreprise);
        }

        $contacts = $query->orderBy('nom')
                         ->paginate(15)
                         ->withQueryString();

        $entreprises = Contact::distinct('entreprise')
                             ->whereNotNull('entreprise')
                             ->pluck('entreprise');

        return view('contacts.index', compact('contacts', 'entreprises'));
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

    /**
     * Display the specified contact.
     *
     * @param  \App\Models\Contact  $contact
     * @return \Illuminate\View\View
     */
    public function show(Contact $contact)
    {
        return view('contacts.show', compact('contact'));
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

    /**
     * Export contacts to CSV
     */
    public function export(Request $request)
    {
        $query = Contact::with('user');

        // Appliquer les mêmes filtres que l'index
        if ($search = $request->input('q')) {
            $query->where(function($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('prenom', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('telephone', 'like', "%{$search}%")
                  ->orWhere('entreprise', 'like', "%{$search}%");
            });
        }

        if ($entreprise = $request->input('entreprise')) {
            $query->where('entreprise', $entreprise);
        }

        $contacts = $query->orderBy('nom')->get();

        $filename = 'contacts_export_' . now()->format('Ymd_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($contacts) {
            $handle = fopen('php://output', 'w');
            // BOM UTF-8 pour Excel
            echo "\xEF\xBB\xBF";
            
            // En-têtes
            fputcsv($handle, [
                'Nom',
                'Prénom',
                'Email',
                'Téléphone',
                'Entreprise',
                'Créé le',
                'Modifié le'
            ]);

            // Données
            foreach ($contacts as $contact) {
                fputcsv($handle, [
                    $contact->nom,
                    $contact->prenom,
                    $contact->email,
                    $contact->telephone,
                    $contact->entreprise,
                    $contact->created_at->format('d/m/Y H:i'),
                    $contact->updated_at->format('d/m/Y H:i')
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}