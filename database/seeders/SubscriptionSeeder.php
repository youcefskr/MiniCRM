<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Product;
use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class SubscriptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $contacts = Contact::all();
        $products = Product::all();

        if ($users->isEmpty() || $contacts->isEmpty()) {
            $this->command->warn('Veuillez d\'abord créer des utilisateurs et des contacts.');
            return;
        }

        // Créer des abonnements
        $subscriptions = [
            [
                'name' => 'Maintenance informatique annuelle',
                'description' => 'Contrat de maintenance informatique incluant support téléphonique et interventions sur site',
                'billing_cycle' => 'annual',
                'amount' => 120000,
                'tax_rate' => 19,
                'status' => 'active',
            ],
            [
                'name' => 'Hébergement web Premium',
                'description' => 'Hébergement haute disponibilité avec certificat SSL et backup quotidien',
                'billing_cycle' => 'monthly',
                'amount' => 5000,
                'tax_rate' => 19,
                'status' => 'active',
            ],
            [
                'name' => 'Support technique mensuel',
                'description' => 'Support technique illimité par email et téléphone',
                'billing_cycle' => 'monthly',
                'amount' => 15000,
                'tax_rate' => 19,
                'status' => 'active',
            ],
            [
                'name' => 'Licence logiciel ERP',
                'description' => 'Licence annuelle pour le logiciel de gestion ERP',
                'billing_cycle' => 'annual',
                'amount' => 250000,
                'tax_rate' => 19,
                'status' => 'pending',
            ],
            [
                'name' => 'Formation continue trimestrielle',
                'description' => 'Sessions de formation trimestrielles pour les équipes',
                'billing_cycle' => 'quarterly',
                'amount' => 45000,
                'tax_rate' => 19,
                'status' => 'active',
            ],
            [
                'name' => 'Service cloud semestriel',
                'description' => 'Stockage cloud 500 Go avec synchronisation multi-appareils',
                'billing_cycle' => 'semi_annual',
                'amount' => 30000,
                'tax_rate' => 19,
                'status' => 'paused',
            ],
        ];

        foreach ($subscriptions as $index => $subData) {
            $contact = $contacts->random();
            $user = $users->random();
            $startDate = Carbon::now()->subMonths(rand(1, 12));
            
            $nextBilling = match($subData['billing_cycle']) {
                'monthly' => $startDate->copy()->addMonth(),
                'quarterly' => $startDate->copy()->addMonths(3),
                'semi_annual' => $startDate->copy()->addMonths(6),
                'annual' => $startDate->copy()->addYear(),
            };

            // Adjust next billing to be in the future
            while ($nextBilling->isPast()) {
                $nextBilling = match($subData['billing_cycle']) {
                    'monthly' => $nextBilling->addMonth(),
                    'quarterly' => $nextBilling->addMonths(3),
                    'semi_annual' => $nextBilling->addMonths(6),
                    'annual' => $nextBilling->addYear(),
                };
            }

            $subscription = Subscription::create([
                'name' => $subData['name'],
                'description' => $subData['description'],
                'contact_id' => $contact->id,
                'user_id' => $user->id,
                'billing_cycle' => $subData['billing_cycle'],
                'amount' => $subData['amount'],
                'tax_rate' => $subData['tax_rate'],
                'start_date' => $startDate,
                'next_billing_date' => $nextBilling,
                'next_renewal_date' => $nextBilling->copy()->addDays(7),
                'status' => $subData['status'],
                'auto_renew' => rand(0, 1) === 1,
                'renewal_reminder_days' => rand(3, 14),
                'billing_count' => rand(0, 12),
                'total_billed' => $subData['amount'] * rand(1, 5),
            ]);

            // Attacher des produits si disponibles
            if ($products->isNotEmpty() && rand(0, 1) === 1) {
                $selectedProducts = $products->random(rand(1, min(3, $products->count())));
                foreach ($selectedProducts as $product) {
                    $subscription->products()->attach($product->id, [
                        'quantity' => rand(1, 5),
                        'unit_price' => $product->price,
                        'discount' => rand(0, 20),
                    ]);
                }
            }

            // Créer des factures pour les abonnements actifs
            if ($subData['status'] === 'active') {
                $this->createInvoicesForSubscription($subscription, $user, rand(1, 4));
            }

            $this->command->info("Abonnement créé: {$subscription->name}");
        }

        // Créer quelques factures standalone
        $this->createStandaloneInvoices($users, $contacts, $products);

        $this->command->info('');
        $this->command->info('✅ Abonnements et factures créés avec succès !');
        $this->command->info('   - ' . Subscription::count() . ' abonnements');
        $this->command->info('   - ' . Invoice::count() . ' factures');
    }

    protected function createInvoicesForSubscription(Subscription $subscription, User $user, int $count): void
    {
        for ($i = 0; $i < $count; $i++) {
            $issueDate = Carbon::now()->subMonths($i + 1);
            $dueDate = $issueDate->copy()->addDays(30);
            
            $status = match(true) {
                $i === 0 && rand(0, 1) === 1 => 'sent',
                $dueDate->isPast() && rand(0, 1) === 1 => 'overdue',
                default => 'paid',
            };

            $invoice = Invoice::create([
                'invoice_number' => Invoice::generateInvoiceNumber(),
                'contact_id' => $subscription->contact_id,
                'subscription_id' => $subscription->id,
                'user_id' => $user->id,
                'subtotal' => $subscription->amount,
                'tax_amount' => $subscription->amount * ($subscription->tax_rate / 100),
                'discount_amount' => 0,
                'total' => $subscription->amount * (1 + $subscription->tax_rate / 100),
                'issue_date' => $issueDate,
                'due_date' => $dueDate,
                'status' => $status,
                'amount_paid' => $status === 'paid' ? $subscription->amount * (1 + $subscription->tax_rate / 100) : 0,
                'paid_date' => $status === 'paid' ? $dueDate->copy()->subDays(rand(1, 15)) : null,
                'payment_method' => $status === 'paid' ? ['cash', 'transfer', 'check', 'card'][rand(0, 3)] : null,
                'is_recurring' => true,
                'recurring_period' => $subscription->billing_cycle,
            ]);

            // Créer les lignes de facture
            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'description' => $subscription->name,
                'quantity' => 1,
                'unit_price' => $subscription->amount,
                'tax_rate' => $subscription->tax_rate,
                'discount' => 0,
                'total' => $subscription->amount,
            ]);
        }
    }

    protected function createStandaloneInvoices($users, $contacts, $products): void
    {
        $invoiceDescriptions = [
            'Prestation de développement web',
            'Consultation stratégique',
            'Formation sur site',
            'Installation matériel',
            'Audit de sécurité',
            'Refonte graphique',
        ];

        for ($i = 0; $i < 5; $i++) {
            $contact = $contacts->random();
            $user = $users->random();
            $issueDate = Carbon::now()->subDays(rand(1, 60));
            $dueDate = $issueDate->copy()->addDays(30);

            $statuses = ['draft', 'sent', 'paid', 'overdue'];
            $status = $statuses[array_rand($statuses)];

            $subtotal = rand(10000, 100000);
            $taxRate = 19;
            $taxAmount = $subtotal * ($taxRate / 100);
            $total = $subtotal + $taxAmount;

            $invoice = Invoice::create([
                'invoice_number' => Invoice::generateInvoiceNumber(),
                'contact_id' => $contact->id,
                'user_id' => $user->id,
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'discount_amount' => 0,
                'total' => $total,
                'issue_date' => $issueDate,
                'due_date' => $dueDate,
                'status' => $status,
                'amount_paid' => $status === 'paid' ? $total : ($status === 'partial' ? $total * 0.5 : 0),
                'paid_date' => $status === 'paid' ? $dueDate->copy()->subDays(rand(1, 10)) : null,
                'payment_method' => $status === 'paid' ? ['cash', 'transfer', 'check'][rand(0, 2)] : null,
                'is_recurring' => false,
                'notes' => 'Facture pour prestations diverses',
            ]);

            // Ajouter des lignes
            $numItems = rand(1, 3);
            for ($j = 0; $j < $numItems; $j++) {
                $itemPrice = rand(5000, 50000);
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'product_id' => $products->isNotEmpty() ? $products->random()->id : null,
                    'description' => $invoiceDescriptions[array_rand($invoiceDescriptions)],
                    'quantity' => rand(1, 5),
                    'unit_price' => $itemPrice,
                    'tax_rate' => $taxRate,
                    'discount' => rand(0, 15),
                    'total' => $itemPrice * rand(1, 5),
                ]);
            }
        }
    }
}
