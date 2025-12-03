<?php

namespace App\Http\Controllers;

use App\Models\Opportunity;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OpportunityController extends Controller
{
    public function index()
    {
        $opportunities = Opportunity::with(['contact', 'user'])->get();
        
        // Group by stage for Kanban
        $stages = [
            'new' => 'Prospection',
            'qualification' => 'Qualification',
            'negotiation' => 'Négociation',
            'proposition' => 'Proposition',
            'won' => 'Gagnée',
            'lost' => 'Perdue'
        ];

        $groupedOpportunities = $opportunities->groupBy('stage');
        $contacts = Contact::all();

        $stats = [
            'total' => $opportunities->count(),
            'total_value' => $opportunities->sum('value'),
            'par_stage' => $opportunities->groupBy('stage')->map(function ($group, $stage) {
                return (object) [
                    'stage' => $stage,
                    'count' => $group->count(),
                    'value' => $group->sum('value')
                ];
            })
        ];

        return view('opportunities.index', compact('opportunities', 'groupedOpportunities', 'stages', 'contacts', 'stats'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'contact_id' => 'required|exists:contacts,id',
            'value' => 'required|numeric|min:0',
            'stage' => 'required|string',
            'probability' => 'required|integer|min:0|max:100',
            'expected_close_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $validated['user_id'] = Auth::id(); // Assign to current user by default, or add field to select user

        Opportunity::create($validated);

        return redirect()->route('opportunities.index')->with('success', 'Opportunité créée avec succès.');
    }

    public function edit(Opportunity $opportunity)
    {
        $contacts = Contact::all();
        $stages = [
            'new' => 'Prospection',
            'qualification' => 'Qualification',
            'negotiation' => 'Négociation',
            'proposition' => 'Proposition',
            'won' => 'Gagnée',
            'lost' => 'Perdue'
        ];

        return view('opportunities.edit', compact('opportunity', 'contacts', 'stages'));
    }

    public function update(Request $request, Opportunity $opportunity)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'contact_id' => 'required|exists:contacts,id',
            'value' => 'required|numeric|min:0',
            'stage' => 'required|string',
            'probability' => 'required|integer|min:0|max:100',
            'expected_close_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $opportunity->update($validated);

        return redirect()->route('opportunities.index')->with('success', 'Opportunité mise à jour.');
    }

    public function destroy(Opportunity $opportunity)
    {
        $opportunity->delete();
        return redirect()->route('opportunities.index')->with('success', 'Opportunité supprimée.');
    }

    // API method for Drag & Drop
    public function updateStage(Request $request, Opportunity $opportunity)
    {
        $validated = $request->validate([
            'stage' => 'required|string',
        ]);

        $opportunity->update(['stage' => $validated['stage']]);

        return response()->json(['success' => true, 'message' => 'Étape mise à jour.']);
    }
}
