<?php

namespace App\Http\Controllers;

use App\Models\Opportunity;
use App\Models\Contact;
use App\Models\User;
use App\Models\Product;
use App\Notifications\OpportunityDueNotification;
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
        $products = Product::active()->with('category')->get();

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

        return view('opportunities.index', compact('opportunities', 'groupedOpportunities', 'stages', 'contacts', 'products', 'stats'));
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

        $validated['user_id'] = Auth::id();

        $opportunity = Opportunity::create($validated);

        // Associer les produits si fournis
        if ($request->has('products') && is_array($request->products)) {
            foreach ($request->products as $productData) {
                if (!empty($productData['product_id']) && !empty($productData['quantity'])) {
                    $product = Product::find($productData['product_id']);
                    if ($product) {
                        $unitPrice = $productData['unit_price'] ?? $product->price;
                        $quantity = $productData['quantity'];
                        $discount = $productData['discount'] ?? 0;
                        $totalPrice = ($unitPrice * $quantity) * (1 - ($discount / 100));

                        $opportunity->products()->attach($product->id, [
                            'quantity' => $quantity,
                            'unit_price' => $unitPrice,
                            'discount' => $discount,
                            'total_price' => $totalPrice,
                        ]);
                    }
                }
            }
        }

        // Notification immédiate si due aujourd'hui ou demain
        if ($opportunity->expected_close_date && $opportunity->user) {
            if ($opportunity->expected_close_date->isToday()) {
                $opportunity->user->notify(new OpportunityDueNotification($opportunity, 'today'));
            } elseif ($opportunity->expected_close_date->isTomorrow()) {
                $opportunity->user->notify(new OpportunityDueNotification($opportunity, 'tomorrow'));
            }
        }

        return redirect()->route('opportunities.index')->with('success', 'Opportunité créée avec succès.');
    }

    public function edit(Opportunity $opportunity)
    {
        $opportunity->load('products.category');
        $contacts = Contact::all();
        $products = Product::active()->with('category')->get();
        $stages = [
            'new' => 'Prospection',
            'qualification' => 'Qualification',
            'negotiation' => 'Négociation',
            'proposition' => 'Proposition',
            'won' => 'Gagnée',
            'lost' => 'Perdue'
        ];

        return view('opportunities.edit', compact('opportunity', 'contacts', 'products', 'stages'));
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

        // Synchroniser les produits
        $productsToSync = [];
        if ($request->has('products') && is_array($request->products)) {
            foreach ($request->products as $productData) {
                if (!empty($productData['product_id']) && !empty($productData['quantity'])) {
                    $product = Product::find($productData['product_id']);
                    if ($product) {
                        $unitPrice = $productData['unit_price'] ?? $product->price;
                        $quantity = $productData['quantity'];
                        $discount = $productData['discount'] ?? 0;
                        $totalPrice = ($unitPrice * $quantity) * (1 - ($discount / 100));

                        $productsToSync[$product->id] = [
                            'quantity' => $quantity,
                            'unit_price' => $unitPrice,
                            'discount' => $discount,
                            'total_price' => $totalPrice,
                        ];
                    }
                }
            }
        }
        $opportunity->products()->sync($productsToSync);

        // Notification immédiate si due aujourd'hui ou demain
        if ($opportunity->expected_close_date && $opportunity->user) {
            if ($opportunity->expected_close_date->isToday()) {
                $opportunity->user->notify(new OpportunityDueNotification($opportunity, 'today'));
            } elseif ($opportunity->expected_close_date->isTomorrow()) {
                $opportunity->user->notify(new OpportunityDueNotification($opportunity, 'tomorrow'));
            }
        }

        return redirect()->route('opportunities.index')->with('success', 'Opportunité mise à jour.');
    }


    public function show(Opportunity $opportunity)
    {
        $opportunity->load(['contact', 'user', 'products.category']);
        
        $stats = [
            'products_count' => $opportunity->products->count(),
            'products_total' => $opportunity->products->sum('pivot.total_price'),
        ];
        
        return view('opportunities.show', compact('opportunity', 'stats'));
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
