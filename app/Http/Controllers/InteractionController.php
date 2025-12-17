<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Interaction;
use App\Models\TypeInteraction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class InteractionController extends Controller
{
    /**
     * Liste des interactions pour un contact spécifique.
     */
    public function index(Request $request, Contact $contact)
    {
        $interactions = $contact->interactions()
            ->with(['type', 'notes.user', 'user'])
            ->filter($request->all())
            ->orderBy('date_interaction', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        $types = TypeInteraction::all();
        $users = User::all();

        // Statistiques pour ce contact
        $stats = [
            'total' => $contact->interactions()->count(),
            'par_type' => $contact->interactions()
                ->join('types_interactions', 'interactions.type_id', '=', 'types_interactions.id')
                ->selectRaw('types_interactions.nom, COUNT(*) as count')
                ->groupBy('types_interactions.nom')
                ->get(),
            'par_statut' => $contact->interactions()
                ->selectRaw('statut, COUNT(*) as count')
                ->groupBy('statut')
                ->get(),
        ];
        
        return view('interactions.index', compact('contact', 'interactions', 'types', 'users', 'stats'));
    }

    /**
     * Liste globale de toutes les interactions (Vue moderne unifiée).
     */
    public function globalIndex(Request $request)
    {
        try {
            // Statistiques globales
            $stats = [
                'total' => Interaction::count(),
                'today' => Interaction::whereDate('date_interaction', today())->count(),
                'week' => Interaction::whereDate('date_interaction', '>=', now()->startOfWeek())->count(),
                'byType' => TypeInteraction::withCount('interactions')->get(),
                'recentContacts' => Contact::whereHas('interactions', function($query) {
                    $query->whereDate('created_at', '>=', now()->subDays(7));
                })->take(5)->get()
            ];

            // Récupération des interactions filtrées
            $interactions = Interaction::with([
                    'contact:id,nom,prenom,email',
                    'type:id,nom,couleur',
                    'user:id,name',
                    'notes' => function($query) {
                        $query->latest()->with('user:id,name');
                    }
                ])
                ->filter($request->all())
                ->orderBy('date_interaction', 'desc')
                ->latest()
                ->paginate(15)
                ->withQueryString();

            $types = TypeInteraction::all();

            // On utilise la vue 'modern' comme vue principale pour l'instant
            return view('interactions.modern', compact('interactions', 'stats', 'types'));

        } catch (\Exception $e) {
            Log::error('Error retrieving interactions:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Une erreur est survenue lors de la récupération des données.');
        }
    }

    public function store(Request $request, Contact $contact)
    {
        $request->validate([
            'type_id' => 'required|exists:types_interactions,id',
            'note' => 'required|string',
            'date_interaction' => 'nullable|date',
            'heure_interaction' => 'nullable',
            'statut' => 'required|in:planifié,réalisé,annulé',
            'user_id' => 'nullable|exists:users,id',
        ]);

        $dateInteraction = null;
        if ($request->filled('date_interaction')) {
            $heure = $request->heure_interaction ?? '00:00';
            $dateInteraction = $request->date_interaction . ' ' . $heure;
        }

        $interaction = $contact->interactions()->create([
            'type_id' => $request->type_id,
            'user_id' => $request->user_id ?? Auth::id(),
            'date_interaction' => $dateInteraction ?? now(),
            'statut' => $request->statut,
        ]);

        $interaction->notes()->create([
            'contenu' => $request->note,
            'user_id' => Auth::id(),
        ]);

        return redirect()
            ->route('contacts.interactions.index', $contact)
            ->with('success', 'L\'interaction a été enregistrée avec succès.');
    }

    public function update(Request $request, Contact $contact, Interaction $interaction)
    {
        $request->validate([
            'type_id' => 'required|exists:types_interactions,id',
            'note' => 'nullable|string',
            'date_interaction' => 'nullable|date',
            'heure_interaction' => 'nullable',
            'statut' => 'required|in:planifié,réalisé,annulé',
            'user_id' => 'nullable|exists:users,id',
        ]);

        $dateInteraction = null;
        if ($request->filled('date_interaction')) {
            $heure = $request->heure_interaction ?? '00:00';
            $dateInteraction = $request->date_interaction . ' ' . $heure;
        }

        $interaction->update([
            'type_id' => $request->type_id,
            'user_id' => $request->user_id ?? $interaction->user_id,
            'date_interaction' => $dateInteraction ?? $interaction->date_interaction,
            'statut' => $request->statut,
        ]);

        if ($request->filled('note')) {
            $interaction->notes()->create([
                'contenu' => $request->note,
                'user_id' => Auth::id(),
            ]);
        }

        return redirect()
            ->route('contacts.interactions.index', $contact)
            ->with('success', 'L\'interaction a été mise à jour avec succès.');
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

        // Redirection intelligente
        if ($request->header('referer') && str_contains($request->header('referer'), 'interactions')) {
             return back()->with('success', 'La note a été ajoutée avec succès.');
        }

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
}