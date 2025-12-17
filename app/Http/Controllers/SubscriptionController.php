<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\Contact;
use App\Models\Opportunity;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    /**
     * Display a listing of the subscriptions.
     */
    public function index(Request $request)
    {
        $query = Subscription::with(['contact', 'user', 'products']);

        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('billing_cycle')) {
            $query->where('billing_cycle', $request->billing_cycle);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhereHas('contact', function ($q) use ($search) {
                        $q->where('nom', 'like', "%{$search}%")
                            ->orWhere('prenom', 'like', "%{$search}%")
                            ->orWhere('entreprise', 'like', "%{$search}%");
                    });
            });
        }

        // Sorting
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        $subscriptions = $query->paginate(15)->withQueryString();

        // Statistics
        $stats = [
            'total' => Subscription::count(),
            'active' => Subscription::active()->count(),
            'pending' => Subscription::pending()->count(),
            'expired' => Subscription::expired()->count(),
            'mrr' => Subscription::active()->get()->sum('monthly_value'), // Monthly Recurring Revenue
            'arr' => Subscription::active()->get()->sum('annual_value'), // Annual Recurring Revenue
            'renewal_soon' => Subscription::renewalSoon()->count(),
        ];

        $users = User::orderBy('name')->get();

        return view('subscriptions.index', compact('subscriptions', 'stats', 'users'));
    }

    /**
     * Show the form for creating a new subscription.
     */
    public function create()
    {
        $contacts = Contact::orderBy('nom')->get();
        $opportunities = Opportunity::orderBy('title')->get();
        $products = Product::where('is_active', true)->orderBy('name')->get();
        $users = User::orderBy('name')->get();

        return view('subscriptions.create', compact('contacts', 'opportunities', 'products', 'users'));
    }

    /**
     * Store a newly created subscription in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'contact_id' => 'required|exists:contacts,id',
            'opportunity_id' => 'nullable|exists:opportunities,id',
            'user_id' => 'required|exists:users,id',
            'billing_cycle' => 'required|in:monthly,quarterly,semi_annual,annual',
            'amount' => 'required|numeric|min:0',
            'tax_rate' => 'required|numeric|min:0|max:100',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'auto_renew' => 'boolean',
            'renewal_reminder_days' => 'integer|min:1|max:90',
            'notes' => 'nullable|string',
            'terms' => 'nullable|string',
            'products' => 'nullable|array',
            'products.*.id' => 'exists:products,id',
            'products.*.quantity' => 'integer|min:1',
            'products.*.unit_price' => 'numeric|min:0',
            'products.*.discount' => 'numeric|min:0|max:100',
        ]);

        // Calculate next billing and renewal dates
        $startDate = \Carbon\Carbon::parse($validated['start_date']);
        $nextBillingDate = $startDate->copy();
        $nextRenewalDate = match($validated['billing_cycle']) {
            'monthly' => $startDate->copy()->addMonth(),
            'quarterly' => $startDate->copy()->addMonths(3),
            'semi_annual' => $startDate->copy()->addMonths(6),
            'annual' => $startDate->copy()->addYear(),
        };

        $subscription = Subscription::create([
            ...$validated,
            'next_billing_date' => $nextBillingDate,
            'next_renewal_date' => $nextRenewalDate,
            'status' => 'active',
            'auto_renew' => $request->boolean('auto_renew', true),
        ]);

        // Attach products
        if ($request->has('products')) {
            foreach ($request->products as $productData) {
                if (isset($productData['id'])) {
                    $subscription->products()->attach($productData['id'], [
                        'quantity' => $productData['quantity'] ?? 1,
                        'unit_price' => $productData['unit_price'] ?? Product::find($productData['id'])->price,
                        'discount' => $productData['discount'] ?? 0,
                    ]);
                }
            }
        }

        return redirect()->route('subscriptions.show', $subscription)
            ->with('success', 'Abonnement créé avec succès.');
    }

    /**
     * Display the specified subscription.
     */
    public function show(Subscription $subscription)
    {
        $subscription->load(['contact', 'user', 'opportunity', 'products', 'invoices' => function ($q) {
            $q->orderBy('issue_date', 'desc');
        }]);

        return view('subscriptions.show', compact('subscription'));
    }

    /**
     * Show the form for editing the specified subscription.
     */
    public function edit(Subscription $subscription)
    {
        $subscription->load('products');
        $contacts = Contact::orderBy('nom')->get();
        $opportunities = Opportunity::orderBy('title')->get();
        $products = Product::where('is_active', true)->orderBy('name')->get();
        $users = User::orderBy('name')->get();

        return view('subscriptions.edit', compact('subscription', 'contacts', 'opportunities', 'products', 'users'));
    }

    /**
     * Update the specified subscription in storage.
     */
    public function update(Request $request, Subscription $subscription)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'contact_id' => 'required|exists:contacts,id',
            'opportunity_id' => 'nullable|exists:opportunities,id',
            'user_id' => 'required|exists:users,id',
            'billing_cycle' => 'required|in:monthly,quarterly,semi_annual,annual',
            'amount' => 'required|numeric|min:0',
            'tax_rate' => 'required|numeric|min:0|max:100',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'status' => 'required|in:active,pending,paused,cancelled,expired',
            'auto_renew' => 'boolean',
            'renewal_reminder_days' => 'integer|min:1|max:90',
            'notes' => 'nullable|string',
            'terms' => 'nullable|string',
            'products' => 'nullable|array',
        ]);

        $subscription->update([
            ...$validated,
            'auto_renew' => $request->boolean('auto_renew', true),
        ]);

        // Sync products
        if ($request->has('products')) {
            $productsData = [];
            foreach ($request->products as $productData) {
                if (isset($productData['id'])) {
                    $productsData[$productData['id']] = [
                        'quantity' => $productData['quantity'] ?? 1,
                        'unit_price' => $productData['unit_price'] ?? Product::find($productData['id'])->price,
                        'discount' => $productData['discount'] ?? 0,
                    ];
                }
            }
            $subscription->products()->sync($productsData);
        } else {
            $subscription->products()->detach();
        }

        return redirect()->route('subscriptions.show', $subscription)
            ->with('success', 'Abonnement mis à jour avec succès.');
    }

    /**
     * Remove the specified subscription from storage.
     */
    public function destroy(Subscription $subscription)
    {
        $subscription->delete();

        return redirect()->route('subscriptions.index')
            ->with('success', 'Abonnement supprimé avec succès.');
    }

    /**
     * Pause a subscription
     */
    public function pause(Subscription $subscription)
    {
        $subscription->pause();

        return back()->with('success', 'Abonnement suspendu.');
    }

    /**
     * Resume a paused subscription
     */
    public function resume(Subscription $subscription)
    {
        $subscription->resume();

        return back()->with('success', 'Abonnement réactivé.');
    }

    /**
     * Cancel a subscription
     */
    public function cancel(Subscription $subscription)
    {
        $subscription->cancel();

        return back()->with('success', 'Abonnement annulé.');
    }

    /**
     * Renew a subscription manually
     */
    public function renew(Subscription $subscription)
    {
        $subscription->renew();

        return back()->with('success', 'Abonnement renouvelé.');
    }

    /**
     * Generate invoice for subscription
     */
    public function generateInvoice(Subscription $subscription)
    {
        $invoice = $subscription->generateInvoice();

        return redirect()->route('invoices.show', $invoice)
            ->with('success', 'Facture générée avec succès.');
    }

    /**
     * Export subscriptions to CSV
     */
    public function export(Request $request)
    {
        $subscriptions = Subscription::with(['contact', 'user'])->get();

        $filename = 'abonnements_' . now()->format('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($subscriptions) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM
            
            fputcsv($file, [
                'ID', 'Nom', 'Client', 'Commercial', 'Cycle', 'Montant', 
                'Statut', 'Date début', 'Prochaine facturation', 'Total facturé'
            ], ';');

            foreach ($subscriptions as $sub) {
                fputcsv($file, [
                    $sub->id,
                    $sub->name,
                    $sub->contact->nom . ' ' . $sub->contact->prenom,
                    $sub->user->name,
                    $sub->billing_cycle_label,
                    number_format($sub->amount, 2, ',', ' ') . ' DA',
                    $sub->status_label,
                    $sub->start_date->format('d/m/Y'),
                    $sub->next_billing_date?->format('d/m/Y'),
                    number_format($sub->total_billed, 2, ',', ' ') . ' DA',
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
