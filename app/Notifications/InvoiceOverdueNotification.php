<?php

namespace App\Notifications;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvoiceOverdueNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Invoice $invoice
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Alerte: Facture {$this->invoice->invoice_number} en retard")
            ->greeting("Bonjour {$notifiable->name},")
            ->line("La facture **{$this->invoice->invoice_number}** du client **{$this->invoice->contact->nom} {$this->invoice->contact->prenom}** est en retard de paiement.")
            ->line("**Date d'échéance:** {$this->invoice->due_date->format('d/m/Y')}")
            ->line("**Jours de retard:** {$this->invoice->days_overdue}")
            ->line("**Montant dû:** " . number_format($this->invoice->amount_due, 2, ',', ' ') . " DA")
            ->action('Voir la facture', route('invoices.show', $this->invoice))
            ->line('Veuillez contacter le client pour effectuer le recouvrement.')
            ->salutation('L\'équipe CRM');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'invoice_overdue',
            'invoice_id' => $this->invoice->id,
            'invoice_number' => $this->invoice->invoice_number,
            'contact_name' => $this->invoice->contact->nom . ' ' . $this->invoice->contact->prenom,
            'due_date' => $this->invoice->due_date->format('Y-m-d'),
            'days_overdue' => $this->invoice->days_overdue,
            'amount_due' => $this->invoice->amount_due,
            'message' => "La facture {$this->invoice->invoice_number} est en retard de {$this->invoice->days_overdue} jours",
        ];
    }
}
