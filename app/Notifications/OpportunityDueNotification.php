<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

use App\Models\Opportunity;

class OpportunityDueNotification extends Notification
{
    use Queueable;

    public $opportunity;
    public $timing; // 'today' ou 'tomorrow'

    /**
     * Create a new notification instance.
     */
    public function __construct(Opportunity $opportunity, string $timing)
    {
        $this->opportunity = $opportunity;
        $this->timing = $timing;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $message = match($this->timing) {
            'today' => "L'opportunité '{$this->opportunity->title}' doit être clôturée AUJOURD'HUI !",
            'tomorrow' => "L'opportunité '{$this->opportunity->title}' doit être clôturée demain.",
            default => "Rappel pour l'opportunité '{$this->opportunity->title}'"
        };

        return [
            'title' => $this->timing === 'today' ? '💰 Opportunité à clôturer' : '📅 Opportunité pour demain',
            'message' => $message,
            'action_url' => route('opportunities.index'),
            'type' => 'opportunity_due',
            'icon' => 'currency-dollar'
        ];
    }
}
