<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Interaction;
use App\Models\TypeInteraction;
use App\Models\NoteInteraction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class InteractionController extends Controller
{
    public function index(Request $request, Contact $contact)
    {
        $query = $contact->interactions()
            ->with(['type', 'notes.user', 'user']);

        // Filtres
        if ($request->filled('type')) {
            $query->where('type_id', $request->type);
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('user')) {
            $query->where('user_id', $request->user);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('date_interaction', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('date_interaction', '<=', $request->date_to);
        }

        $interactions = $query->orderBy('date_interaction', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        $types = TypeInteraction::all();
        $users = User::all();

        // Statistiques
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

        // Si une note est fournie, créer une nouvelle note
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

        // Redirect to modern view if coming from there, otherwise to index
        if ($request->header('referer') && str_contains($request->header('referer'), 'interactions/modern')) {
            return redirect()
                ->route('interactions.modern')
                ->with('success', 'La note a été ajoutée avec succès.');
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

    public function all()
    {
        try {
            // Get basic statistics
            $stats = [
                'total' => Interaction::count(),
                'today' => Interaction::whereDate('created_at', today())->count(),
                'byType' => TypeInteraction::withCount('interactions')->get(),
                'recentContacts' => Contact::whereHas('interactions', function($query) {
                    $query->whereDate('created_at', '>=', now()->subDays(7));
                })->take(5)->get()
            ];

            // Get interactions with relationships
            $interactions = Interaction::query()
                ->with([
                    'contact:id,nom,prenom,email',
                    'type:id,nom,couleur',
                    'user:id,name',
                    'notes' => function($query) {
                        $query->latest()->with('user:id,name');
                    }
                ])
                ->latest()
                ->paginate(10);

            // Debug output
            Log::info('Interactions retrieved:', [
                'count' => $interactions->count(),
                'total' => $stats['total'],
                'sample' => $interactions->first()
            ]);

            return view('interactions.all', compact('interactions', 'stats'));
        } catch (\Exception $e) {
            Log::error('Error retrieving interactions:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Une erreur est survenue lors de la récupération des données.');
        }
    }

    public function modern(Request $request)
    {
        try {
            // Get basic statistics
            $stats = [
                'total' => Interaction::count(),
                'today' => Interaction::whereDate('created_at', today())->count(),
                'byType' => TypeInteraction::withCount('interactions')->get(),
                'recentContacts' => Contact::whereHas('interactions', function($query) {
                    $query->whereDate('created_at', '>=', now()->subDays(7));
                })->take(5)->get()
            ];

            // Build query with filters
            $query = Interaction::query()
                ->with([
                    'contact:id,nom,prenom,email',
                    'type:id,nom,couleur',
                    'user:id,name',
                    'notes' => function($query) {
                        $query->latest()->with('user:id,name');
                    }
                ]);

            // Filter by type
            if ($request->filled('type')) {
                $query->where('type_id', $request->type);
            }

            // Filter by date
            if ($request->filled('date')) {
                switch ($request->date) {
                    case 'today':
                        $query->whereDate('created_at', today());
                        break;
                    case 'week':
                        $query->whereDate('created_at', '>=', now()->startOfWeek());
                        break;
                    case 'month':
                        $query->whereMonth('created_at', now()->month)
                              ->whereYear('created_at', now()->year);
                        break;
                    case 'year':
                        $query->whereYear('created_at', now()->year);
                        break;
                }
            }

            // Search filter
            if ($request->filled('search')) {
                $search = $request->search;
                $query->whereHas('contact', function($q) use ($search) {
                    $q->where('nom', 'like', "%{$search}%")
                      ->orWhere('prenom', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                })->orWhereHas('notes', function($q) use ($search) {
                    $q->where('contenu', 'like', "%{$search}%");
                });
            }

            $interactions = $query->latest()->paginate(15)->withQueryString();
            $types = TypeInteraction::all();

            return view('interactions.modern', compact('interactions', 'stats', 'types'));
        } catch (\Exception $e) {
            Log::error('Error retrieving interactions:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Une erreur est survenue lors de la récupération des données.');
        }
    }

    public function dashboard(Request $request)
    {
        try {
            // Build query with filters
            $query = Interaction::query()
                ->with([
                    'contact:id,nom,prenom,email',
                    'type:id,nom,couleur',
                    'user:id,name',
                    'notes' => function($query) {
                        $query->latest()->with('user:id,name');
                    }
                ]);

            // Filter by type
            if ($request->filled('type')) {
                $query->where('type_id', $request->type);
            }

            // Filter by statut
            if ($request->filled('statut')) {
                $query->where('statut', $request->statut);
            }

            // Filter by date
            if ($request->filled('date_from')) {
                $query->whereDate('date_interaction', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $query->whereDate('date_interaction', '<=', $request->date_to);
            }

            // Search filter
            if ($request->filled('search')) {
                $search = $request->search;
                $query->whereHas('contact', function($q) use ($search) {
                    $q->where('nom', 'like', "%{$search}%")
                      ->orWhere('prenom', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                })->orWhereHas('notes', function($q) use ($search) {
                    $q->where('contenu', 'like', "%{$search}%");
                });
            }

            $interactions = $query->orderBy('date_interaction', 'desc')
                ->orderBy('created_at', 'desc')
                ->paginate(12)->withQueryString();

            // Get statistics
            $stats = [
                'total' => Interaction::count(),
                'today' => Interaction::whereDate('created_at', today())->count(),
                'planifiees' => Interaction::where('statut', 'planifié')->count(),
                'week' => Interaction::whereDate('created_at', '>=', now()->startOfWeek())->count(),
                'byType' => TypeInteraction::withCount('interactions')->get(),
            ];

            $types = TypeInteraction::all();

            return view('interactions.dashboard', compact('interactions', 'stats', 'types'));
        } catch (\Exception $e) {
            Log::error('Error retrieving interactions dashboard:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Une erreur est survenue lors de la récupération des données.');
        }
    }
}