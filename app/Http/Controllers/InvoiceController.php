<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Contact;
use App\Models\Subscription;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the invoices.
     */
    public function index(Request $request)
    {
        $query = Invoice::with(['contact', 'subscription', 'user']);

        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('contact_id')) {
            $query->where('contact_id', $request->contact_id);
        }

        if ($request->filled('is_recurring')) {
            $query->where('is_recurring', $request->boolean('is_recurring'));
        }

        if ($request->filled('date_from')) {
            $query->where('issue_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('issue_date', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                    ->orWhereHas('contact', function ($q) use ($search) {
                        $q->where('nom', 'like', "%{$search}%")
                            ->orWhere('prenom', 'like', "%{$search}%")
                            ->orWhere('entreprise', 'like', "%{$search}%");
                    });
            });
        }

        // Sorting
        $sortField = $request->get('sort', 'issue_date');
        $sortDirection = $request->get('direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        $invoices = $query->paginate(15)->withQueryString();

        // Statistics
        $stats = [
            'total' => Invoice::count(),
            'draft' => Invoice::draft()->count(),
            'sent' => Invoice::sent()->count(),
            'paid' => Invoice::paid()->count(),
            'overdue' => Invoice::overdue()->count(),
            'total_this_month' => Invoice::thisMonth()->sum('total'),
            'total_paid_this_month' => Invoice::thisMonth()->paid()->sum('total'),
            'total_unpaid' => Invoice::unpaid()->sum('total'),
        ];

        $contacts = Contact::orderBy('nom')->get();

        return view('invoices.index', compact('invoices', 'stats', 'contacts'));
    }

    /**
     * Show the form for creating a new invoice.
     */
    public function create(Request $request)
    {
        $contacts = Contact::orderBy('nom')->get();
        $subscriptions = Subscription::active()->orderBy('name')->get();
        $products = Product::where('is_active', true)->orderBy('name')->get();

        // Pre-fill from contact or subscription if provided
        $selectedContact = null;
        $selectedSubscription = null;

        if ($request->has('contact_id')) {
            $selectedContact = Contact::find($request->contact_id);
        }

        if ($request->has('subscription_id')) {
            $selectedSubscription = Subscription::with('products')->find($request->subscription_id);
            if ($selectedSubscription) {
                $selectedContact = $selectedSubscription->contact;
            }
        }

        return view('invoices.create', compact(
            'contacts', 'subscriptions', 'products', 
            'selectedContact', 'selectedSubscription'
        ));
    }

    /**
     * Store a newly created invoice in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'contact_id' => 'required|exists:contacts,id',
            'subscription_id' => 'nullable|exists:subscriptions,id',
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:issue_date',
            'notes' => 'nullable|string',
            'terms' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.tax_rate' => 'required|numeric|min:0|max:100',
            'items.*.discount' => 'nullable|numeric|min:0|max:100',
            'items.*.product_id' => 'nullable|exists:products,id',
        ]);

        // Calculate totals
        $subtotal = 0;
        $taxAmount = 0;

        foreach ($request->items as $item) {
            $discountedPrice = $item['unit_price'] * (1 - ($item['discount'] ?? 0) / 100);
            $itemTotal = $discountedPrice * $item['quantity'];
            $subtotal += $itemTotal;
            $taxAmount += $itemTotal * ($item['tax_rate'] / 100);
        }

        $invoice = Invoice::create([
            'invoice_number' => Invoice::generateInvoiceNumber(),
            'contact_id' => $validated['contact_id'],
            'subscription_id' => $validated['subscription_id'],
            'user_id' => Auth::id(),
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'discount_amount' => 0,
            'total' => $subtotal + $taxAmount,
            'issue_date' => $validated['issue_date'],
            'due_date' => $validated['due_date'],
            'status' => 'draft',
            'notes' => $validated['notes'],
            'terms' => $validated['terms'],
            'is_recurring' => $request->has('subscription_id'),
        ]);

        // Create items
        foreach ($request->items as $itemData) {
            $discountedPrice = $itemData['unit_price'] * (1 - ($itemData['discount'] ?? 0) / 100);
            
            $invoice->items()->create([
                'product_id' => $itemData['product_id'] ?? null,
                'description' => $itemData['description'],
                'quantity' => $itemData['quantity'],
                'unit_price' => $itemData['unit_price'],
                'tax_rate' => $itemData['tax_rate'],
                'discount' => $itemData['discount'] ?? 0,
                'total' => $discountedPrice * $itemData['quantity'],
            ]);
        }

        return redirect()->route('invoices.show', $invoice)
            ->with('success', 'Facture créée avec succès.');
    }

    /**
     * Display the specified invoice.
     */
    public function show(Invoice $invoice)
    {
        $invoice->load(['contact', 'subscription', 'user', 'items.product']);

        return view('invoices.show', compact('invoice'));
    }

    /**
     * Show the form for editing the specified invoice.
     */
    public function edit(Invoice $invoice)
    {
        if ($invoice->status === 'paid') {
            return back()->with('error', 'Impossible de modifier une facture payée.');
        }

        $invoice->load('items');
        $contacts = Contact::orderBy('nom')->get();
        $subscriptions = Subscription::active()->orderBy('name')->get();
        $products = Product::where('is_active', true)->orderBy('name')->get();

        return view('invoices.edit', compact('invoice', 'contacts', 'subscriptions', 'products'));
    }

    /**
     * Update the specified invoice in storage.
     */
    public function update(Request $request, Invoice $invoice)
    {
        if ($invoice->status === 'paid') {
            return back()->with('error', 'Impossible de modifier une facture payée.');
        }

        $validated = $request->validate([
            'contact_id' => 'required|exists:contacts,id',
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:issue_date',
            'notes' => 'nullable|string',
            'terms' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.tax_rate' => 'required|numeric|min:0|max:100',
            'items.*.discount' => 'nullable|numeric|min:0|max:100',
        ]);

        $invoice->update([
            'contact_id' => $validated['contact_id'],
            'issue_date' => $validated['issue_date'],
            'due_date' => $validated['due_date'],
            'notes' => $validated['notes'],
            'terms' => $validated['terms'],
        ]);

        // Update items
        $invoice->items()->delete();
        
        foreach ($request->items as $itemData) {
            $discountedPrice = $itemData['unit_price'] * (1 - ($itemData['discount'] ?? 0) / 100);
            
            $invoice->items()->create([
                'product_id' => $itemData['product_id'] ?? null,
                'description' => $itemData['description'],
                'quantity' => $itemData['quantity'],
                'unit_price' => $itemData['unit_price'],
                'tax_rate' => $itemData['tax_rate'],
                'discount' => $itemData['discount'] ?? 0,
                'total' => $discountedPrice * $itemData['quantity'],
            ]);
        }

        $invoice->recalculateTotals();

        return redirect()->route('invoices.show', $invoice)
            ->with('success', 'Facture mise à jour avec succès.');
    }

    /**
     * Remove the specified invoice from storage.
     */
    public function destroy(Invoice $invoice)
    {
        if ($invoice->status === 'paid') {
            return back()->with('error', 'Impossible de supprimer une facture payée.');
        }

        $invoice->delete();

        return redirect()->route('invoices.index')
            ->with('success', 'Facture supprimée avec succès.');
    }

    /**
     * Send the invoice
     */
    public function send(Invoice $invoice)
    {
        $invoice->send();

        // TODO: Send email notification to contact

        return back()->with('success', 'Facture envoyée.');
    }

    /**
     * Mark invoice as paid
     */
    public function markAsPaid(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'payment_method' => 'required|in:cash,transfer,check,card,ccp',
            'payment_reference' => 'nullable|string|max:255',
        ]);

        $invoice->markAsPaid($validated['payment_method'], $validated['payment_reference']);

        return back()->with('success', 'Facture marquée comme payée.');
    }

    /**
     * Add partial payment
     */
    public function addPayment(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01|max:' . $invoice->amount_due,
            'payment_method' => 'required|in:cash,transfer,check,card,ccp',
            'payment_reference' => 'nullable|string|max:255',
        ]);

        $invoice->addPayment(
            $validated['amount'],
            $validated['payment_method'],
            $validated['payment_reference']
        );

        return back()->with('success', 'Paiement enregistré.');
    }

    /**
     * Cancel the invoice
     */
    public function cancel(Invoice $invoice)
    {
        if ($invoice->status === 'paid') {
            return back()->with('error', 'Impossible d\'annuler une facture payée.');
        }

        $invoice->cancel();

        return back()->with('success', 'Facture annulée.');
    }

    /**
     * Download invoice as PDF
     */
    public function download(Invoice $invoice)
    {
        $invoice->load(['contact', 'items.product', 'user']);

        $pdf = Pdf::loadView('invoices.pdf', compact('invoice'));
        
        return $pdf->download('facture_' . $invoice->invoice_number . '.pdf');
    }

    /**
     * Duplicate an invoice
     */
    public function duplicate(Invoice $invoice)
    {
        $newInvoice = $invoice->replicate();
        $newInvoice->invoice_number = Invoice::generateInvoiceNumber();
        $newInvoice->issue_date = now();
        $newInvoice->due_date = now()->addDays(30);
        $newInvoice->status = 'draft';
        $newInvoice->amount_paid = 0;
        $newInvoice->paid_date = null;
        $newInvoice->payment_method = null;
        $newInvoice->payment_reference = null;
        $newInvoice->save();

        foreach ($invoice->items as $item) {
            $newItem = $item->replicate();
            $newItem->invoice_id = $newInvoice->id;
            $newItem->save();
        }

        return redirect()->route('invoices.edit', $newInvoice)
            ->with('success', 'Facture dupliquée.');
    }

    /**
     * Export invoices to CSV
     */
    public function export(Request $request)
    {
        $query = Invoice::with(['contact', 'user']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->where('issue_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('issue_date', '<=', $request->date_to);
        }

        $invoices = $query->orderBy('issue_date', 'desc')->get();

        $filename = 'factures_' . now()->format('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($invoices) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            fputcsv($file, [
                'N° Facture', 'Client', 'Date émission', 'Date échéance', 
                'Sous-total', 'TVA', 'Total', 'Statut', 'Payé', 'Reste à payer'
            ], ';');

            foreach ($invoices as $inv) {
                fputcsv($file, [
                    $inv->invoice_number,
                    $inv->contact->nom . ' ' . $inv->contact->prenom,
                    $inv->issue_date->format('d/m/Y'),
                    $inv->due_date->format('d/m/Y'),
                    number_format($inv->subtotal, 2, ',', ' '),
                    number_format($inv->tax_amount, 2, ',', ' '),
                    number_format($inv->total, 2, ',', ' '),
                    $inv->status_label,
                    number_format($inv->amount_paid, 2, ',', ' '),
                    number_format($inv->amount_due, 2, ',', ' '),
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
