<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facture {{ $invoice->invoice_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #333;
            background: #fff;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 40px;
        }
        
        /* Header */
        .header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 40px;
            border-bottom: 3px solid #22c55e;
            padding-bottom: 20px;
        }
        
        .logo-section h1 {
            font-size: 28px;
            font-weight: bold;
            color: #22c55e;
            margin-bottom: 5px;
        }
        
        .logo-section p {
            color: #666;
            font-size: 11px;
        }
        
        .invoice-info {
            text-align: right;
        }
        
        .invoice-info h2 {
            font-size: 24px;
            color: #333;
            margin-bottom: 10px;
        }
        
        .invoice-info .invoice-number {
            font-size: 14px;
            font-weight: bold;
            color: #22c55e;
        }
        
        .invoice-info .dates {
            margin-top: 10px;
            font-size: 11px;
            color: #666;
        }
        
        /* Addresses */
        .addresses {
            display: table;
            width: 100%;
            margin-bottom: 40px;
        }
        
        .address-block {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        
        .address-block h3 {
            font-size: 10px;
            text-transform: uppercase;
            color: #999;
            margin-bottom: 8px;
            letter-spacing: 1px;
        }
        
        .address-block .name {
            font-size: 14px;
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }
        
        .address-block p {
            color: #666;
            font-size: 11px;
            line-height: 1.6;
        }
        
        /* Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        
        .items-table thead tr {
            background: #f8f9fa;
        }
        
        .items-table th {
            padding: 12px 10px;
            text-align: left;
            font-size: 10px;
            text-transform: uppercase;
            color: #666;
            border-bottom: 2px solid #e5e7eb;
            letter-spacing: 0.5px;
        }
        
        .items-table th.text-center {
            text-align: center;
        }
        
        .items-table th.text-right {
            text-align: right;
        }
        
        .items-table td {
            padding: 12px 10px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 11px;
        }
        
        .items-table td.text-center {
            text-align: center;
        }
        
        .items-table td.text-right {
            text-align: right;
        }
        
        .items-table .description {
            font-weight: 500;
        }
        
        .items-table .discount {
            color: #22c55e;
            font-size: 10px;
        }
        
        /* Totals */
        .totals-section {
            display: table;
            width: 100%;
            margin-bottom: 40px;
        }
        
        .totals-left {
            display: table-cell;
            width: 60%;
            vertical-align: top;
        }
        
        .totals-right {
            display: table-cell;
            width: 40%;
            vertical-align: top;
        }
        
        .totals-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .totals-table tr td {
            padding: 8px 10px;
            font-size: 11px;
        }
        
        .totals-table tr td:first-child {
            color: #666;
        }
        
        .totals-table tr td:last-child {
            text-align: right;
            font-weight: 500;
        }
        
        .totals-table tr.total-row {
            background: #22c55e;
            color: #fff;
        }
        
        .totals-table tr.total-row td {
            font-size: 14px;
            font-weight: bold;
            padding: 12px 10px;
        }
        
        .totals-table tr.total-row td:first-child {
            color: #fff;
        }
        
        /* Notes */
        .notes-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        
        .notes-section h4 {
            font-size: 11px;
            text-transform: uppercase;
            color: #666;
            margin-bottom: 8px;
            letter-spacing: 0.5px;
        }
        
        .notes-section p {
            color: #333;
            font-size: 11px;
            line-height: 1.6;
        }
        
        /* Footer */
        .footer {
            text-align: center;
            padding-top: 30px;
            border-top: 1px solid #e5e7eb;
            color: #999;
            font-size: 10px;
        }
        
        /* Status Badge */
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 10px;
        }
        
        .status-paid {
            background: #dcfce7;
            color: #166534;
        }
        
        .status-sent {
            background: #dbeafe;
            color: #1e40af;
        }
        
        .status-overdue {
            background: #fee2e2;
            color: #991b1b;
        }
        
        .status-draft {
            background: #f3f4f6;
            color: #374151;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="logo-section">
                <h1>MiniCRM</h1>
                <p>Solution de gestion client</p>
            </div>
            <div class="invoice-info">
                <h2>FACTURE</h2>
                <div class="invoice-number">{{ $invoice->invoice_number }}</div>
                <div class="dates">
                    <div>Émise le: {{ $invoice->issue_date->format('d/m/Y') }}</div>
                    <div>Échéance: {{ $invoice->due_date->format('d/m/Y') }}</div>
                </div>
                @if($invoice->status === 'paid')
                    <div class="status-badge status-paid">PAYÉE</div>
                @elseif($invoice->status === 'overdue')
                    <div class="status-badge status-overdue">EN RETARD</div>
                @elseif($invoice->status === 'sent')
                    <div class="status-badge status-sent">ENVOYÉE</div>
                @else
                    <div class="status-badge status-draft">BROUILLON</div>
                @endif
            </div>
        </div>
        
        <!-- Addresses -->
        <div class="addresses">
            <div class="address-block">
                <h3>De</h3>
                <div class="name">Votre Entreprise</div>
                <p>
                    Adresse de l'entreprise<br>
                    Ville, Code Postal<br>
                    Algérie<br>
                    Tél: +213 XX XX XX XX<br>
                    Email: contact@entreprise.dz
                </p>
            </div>
            <div class="address-block">
                <h3>Facturé à</h3>
                <div class="name">{{ $invoice->contact->nom }} {{ $invoice->contact->prenom }}</div>
                <p>
                    @if($invoice->contact->entreprise){{ $invoice->contact->entreprise }}<br>@endif
                    @if($invoice->contact->adresse){{ $invoice->contact->adresse }}<br>@endif
                    @if($invoice->contact->email)Email: {{ $invoice->contact->email }}<br>@endif
                    @if($invoice->contact->telephone)Tél: {{ $invoice->contact->telephone }}@endif
                </p>
            </div>
        </div>
        
        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 45%;">Description</th>
                    <th class="text-center" style="width: 10%;">Qté</th>
                    <th class="text-right" style="width: 15%;">Prix unit.</th>
                    <th class="text-center" style="width: 10%;">TVA</th>
                    <th class="text-right" style="width: 20%;">Total HT</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $item)
                    <tr>
                        <td>
                            <div class="description">{{ $item->description }}</div>
                            @if($item->discount > 0)
                                <div class="discount">Remise: -{{ number_format($item->discount, 0) }}%</div>
                            @endif
                        </td>
                        <td class="text-center">{{ $item->quantity }}</td>
                        <td class="text-right">{{ number_format($item->unit_price, 2, ',', ' ') }} DA</td>
                        <td class="text-center">{{ number_format($item->tax_rate, 0) }}%</td>
                        <td class="text-right">{{ number_format($item->total, 2, ',', ' ') }} DA</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        
        <!-- Totals -->
        <div class="totals-section">
            <div class="totals-left">
                @if($invoice->notes || $invoice->terms)
                    <div class="notes-section">
                        @if($invoice->notes)
                            <h4>Notes</h4>
                            <p>{{ $invoice->notes }}</p>
                        @endif
                        @if($invoice->terms)
                            <h4 style="margin-top: 15px;">Conditions</h4>
                            <p>{{ $invoice->terms }}</p>
                        @endif
                    </div>
                @endif
            </div>
            <div class="totals-right">
                <table class="totals-table">
                    <tr>
                        <td>Sous-total HT</td>
                        <td>{{ number_format($invoice->subtotal, 2, ',', ' ') }} DA</td>
                    </tr>
                    <tr>
                        <td>TVA</td>
                        <td>{{ number_format($invoice->tax_amount, 2, ',', ' ') }} DA</td>
                    </tr>
                    @if($invoice->discount_amount > 0)
                        <tr>
                            <td>Remise</td>
                            <td>-{{ number_format($invoice->discount_amount, 2, ',', ' ') }} DA</td>
                        </tr>
                    @endif
                    <tr class="total-row">
                        <td>TOTAL TTC</td>
                        <td>{{ number_format($invoice->total, 2, ',', ' ') }} DA</td>
                    </tr>
                    @if($invoice->amount_paid > 0 && $invoice->status !== 'paid')
                        <tr>
                            <td>Déjà payé</td>
                            <td>-{{ number_format($invoice->amount_paid, 2, ',', ' ') }} DA</td>
                        </tr>
                        <tr style="background: #fef3c7;">
                            <td style="font-weight: bold;">Reste à payer</td>
                            <td style="font-weight: bold;">{{ number_format($invoice->amount_due, 2, ',', ' ') }} DA</td>
                        </tr>
                    @endif
                </table>
            </div>
        </div>
        
        <!-- Payment Info -->
        @if($invoice->status === 'paid')
            <div class="notes-section" style="background: #dcfce7; border: 1px solid #22c55e;">
                <h4 style="color: #166534;">Paiement reçu</h4>
                <p style="color: #166534;">
                    Date: {{ $invoice->paid_date->format('d/m/Y') }}<br>
                    @if($invoice->payment_method)Mode: {{ $invoice->payment_method_label }}<br>@endif
                    @if($invoice->payment_reference)Référence: {{ $invoice->payment_reference }}@endif
                </p>
            </div>
        @endif
        
        <!-- Footer -->
        <div class="footer">
            <p>Merci pour votre confiance.</p>
            <p style="margin-top: 5px;">Facture générée par MiniCRM - {{ now()->format('d/m/Y H:i') }}</p>
        </div>
    </div>
</body>
</html>
