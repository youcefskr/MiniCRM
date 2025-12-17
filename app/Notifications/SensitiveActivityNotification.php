<?php

namespace App\Notifications;

use App\Models\ActivityLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SensitiveActivityNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public ActivityLog $log
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $severityLabel = match($this->log->severity) {
            'danger' => '🔴 CRITIQUE',
            'warning' => '🟠 ATTENTION',
            default => '🔵 INFO',
        };

        return (new MailMessage)
            ->subject("{$severityLabel} - Alerte d'activité sensible dans le CRM")
            ->greeting("Bonjour {$notifiable->name},")
            ->line("Une action sensible a été détectée dans le système CRM.")
            ->line("")
            ->line("**Détails de l'action:**")
            ->line("- **Action:** {$this->log->action_label}")
            ->line("- **Module:** {$this->log->module_label}")
            ->line("- **Description:** {$this->log->description}")
            ->line("- **Utilisateur:** {$this->log->user_name}")
            ->line("- **Date/Heure:** {$this->log->created_at->format('d/m/Y H:i:s')}")
            ->line("- **Adresse IP:** {$this->log->ip_address}")
            ->action('Voir le journal d\'activité', route('admin.activity-logs.index'))
            ->line("Veuillez vérifier cette action et prendre les mesures nécessaires si requis.")
            ->salutation('L\'équipe de sécurité CRM');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'sensitive_activity',
            'log_id' => $this->log->id,
            'action' => $this->log->action,
            'module' => $this->log->module,
            'description' => $this->log->description,
            'user_name' => $this->log->user_name,
            'severity' => $this->log->severity,
            'message' => "Alerte: {$this->log->description}",
        ];
    }
}
