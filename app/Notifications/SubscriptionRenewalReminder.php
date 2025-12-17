<?php

namespace App\Notifications;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionRenewalReminder extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Subscription $subscription
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $daysUntil = $this->subscription->days_until_renewal;
        
        return (new MailMessage)
            ->subject("Rappel: Renouvellement d'abonnement dans {$daysUntil} jours")
            ->greeting("Bonjour {$notifiable->name},")
            ->line("L'abonnement **{$this->subscription->name}** du client **{$this->subscription->contact->nom} {$this->subscription->contact->prenom}** arrive à échéance.")
            ->line("**Date de renouvellement:** {$this->subscription->next_renewal_date->format('d/m/Y')}")
            ->line("**Montant:** " . number_format($this->subscription->total_with_tax, 2, ',', ' ') . " DA")
            ->action('Voir l\'abonnement', route('subscriptions.show', $this->subscription))
            ->line('Pensez à contacter le client pour confirmer le renouvellement.')
            ->salutation('L\'équipe CRM');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'subscription_renewal_reminder',
            'subscription_id' => $this->subscription->id,
            'subscription_name' => $this->subscription->name,
            'contact_name' => $this->subscription->contact->nom . ' ' . $this->subscription->contact->prenom,
            'renewal_date' => $this->subscription->next_renewal_date->format('Y-m-d'),
            'days_until' => $this->subscription->days_until_renewal,
            'amount' => $this->subscription->total_with_tax,
            'message' => "L'abonnement {$this->subscription->name} arrive à échéance dans {$this->subscription->days_until_renewal} jours",
        ];
    }
}
