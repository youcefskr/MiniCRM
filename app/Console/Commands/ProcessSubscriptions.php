<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use App\Models\Invoice;
use App\Notifications\SubscriptionRenewalReminder;
use App\Notifications\InvoiceOverdueNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ProcessSubscriptions extends Command
{
    protected $signature = 'subscriptions:process 
                            {--billing : Generate invoices for subscriptions due for billing}
                            {--reminders : Send renewal reminders}
                            {--overdue : Mark overdue invoices and send notifications}
                            {--all : Run all processes}';

    protected $description = 'Process subscriptions: generate invoices, send reminders, mark overdue';

    public function handle(): int
    {
        $this->info('Processing subscriptions...');
        
        $runAll = $this->option('all');
        
        if ($runAll || $this->option('billing')) {
            $this->processBilling();
        }
        
        if ($runAll || $this->option('reminders')) {
            $this->sendRenewalReminders();
        }
        
        if ($runAll || $this->option('overdue')) {
            $this->processOverdueInvoices();
        }
        
        $this->info('Done!');
        
        return Command::SUCCESS;
    }

    protected function processBilling(): void
    {
        $this->info('Processing billing...');
        
        $subscriptions = Subscription::needsBilling()->with(['contact', 'user', 'products'])->get();
        
        $this->info("Found {$subscriptions->count()} subscriptions due for billing.");
        
        foreach ($subscriptions as $subscription) {
            try {
                $invoice = $subscription->generateInvoice();
                $this->info("Generated invoice {$invoice->invoice_number} for subscription {$subscription->name}");
                Log::info("Generated invoice {$invoice->invoice_number} for subscription #{$subscription->id}");
            } catch (\Exception $e) {
                $this->error("Error generating invoice for subscription {$subscription->name}: {$e->getMessage()}");
                Log::error("Error generating invoice for subscription #{$subscription->id}: {$e->getMessage()}");
            }
        }
    }

    protected function sendRenewalReminders(): void
    {
        $this->info('Sending renewal reminders...');
        
        // Get subscriptions where renewal is within the reminder period
        $subscriptions = Subscription::active()
            ->whereNotNull('next_renewal_date')
            ->whereRaw('DATEDIFF(next_renewal_date, CURDATE()) <= renewal_reminder_days')
            ->whereRaw('DATEDIFF(next_renewal_date, CURDATE()) > 0')
            ->with(['contact', 'user'])
            ->get();
        
        $this->info("Found {$subscriptions->count()} subscriptions needing renewal reminders.");
        
        foreach ($subscriptions as $subscription) {
            try {
                // Notify the assigned commercial
                $subscription->user->notify(new SubscriptionRenewalReminder($subscription));
                $this->info("Sent reminder for subscription {$subscription->name} to {$subscription->user->name}");
                Log::info("Sent renewal reminder for subscription #{$subscription->id} to user #{$subscription->user_id}");
            } catch (\Exception $e) {
                $this->error("Error sending reminder for subscription {$subscription->name}: {$e->getMessage()}");
                Log::error("Error sending renewal reminder for subscription #{$subscription->id}: {$e->getMessage()}");
            }
        }
    }

    protected function processOverdueInvoices(): void
    {
        $this->info('Processing overdue invoices...');
        
        // Get invoices that are past due date and not yet marked as overdue
        $invoices = Invoice::where('status', 'sent')
            ->where('due_date', '<', now())
            ->with(['contact', 'user'])
            ->get();
        
        $this->info("Found {$invoices->count()} overdue invoices.");
        
        foreach ($invoices as $invoice) {
            try {
                $invoice->markAsOverdue();
                
                // Notify the creator/assigned user
                $invoice->user->notify(new InvoiceOverdueNotification($invoice));
                
                $this->info("Marked invoice {$invoice->invoice_number} as overdue and notified {$invoice->user->name}");
                Log::info("Marked invoice #{$invoice->id} as overdue and sent notification");
            } catch (\Exception $e) {
                $this->error("Error processing overdue invoice {$invoice->invoice_number}: {$e->getMessage()}");
                Log::error("Error processing overdue invoice #{$invoice->id}: {$e->getMessage()}");
            }
        }
    }
}
