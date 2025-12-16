<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

use App\Models\Task;

class TaskDueNotification extends Notification
{
    use Queueable;

    public $task;
    public $timing; // 'today' ou 'tomorrow'

    /**
     * Create a new notification instance.
     */
    public function __construct(Task $task, string $timing)
    {
        $this->task = $task;
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
            'today' => "La tâche '{$this->task->title}' arrive à échéance AUJOURD'HUI !",
            'tomorrow' => "La tâche '{$this->task->title}' est prévue pour demain.",
            default => "Rappel pour la tâche '{$this->task->title}'"
        };

        return [
            'title' => $this->timing === 'today' ? '⚠️ Tâche pour aujourd\'hui' : '📅 Tâche pour demain',
            'message' => $message,
            'action_url' => route('tasks.index'),
            'type' => 'task_due',
            'icon' => $this->timing === 'today' ? 'exclamation-circle' : 'clock'
        ];
    }
}
