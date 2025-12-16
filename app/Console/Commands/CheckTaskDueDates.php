<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\Task;
use App\Notifications\TaskDueNotification;

class CheckTaskDueDates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tasks:check-due';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Vérifie les tâches arrivant à échéance (aujourd\'hui et demain) et notifie les utilisateurs';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = 0;

        // 1. Tâches pour DEMAIN
        $tasksTomorrow = Task::whereDate('due_date', now()->addDay())
                     ->where('status', '!=', 'terminee')
                     ->whereNotNull('user_id')
                     ->with('user')
                     ->get();

        foreach ($tasksTomorrow as $task) {
            if ($task->user) {
                $task->user->notify(new TaskDueNotification($task, 'tomorrow'));
                $count++;
            }
        }

        // 2. Tâches pour AUJOURD'HUI
        $tasksToday = Task::whereDate('due_date', now())
                     ->where('status', '!=', 'terminee')
                     ->whereNotNull('user_id')
                     ->with('user')
                     ->get();

        foreach ($tasksToday as $task) {
            if ($task->user) {
                $task->user->notify(new TaskDueNotification($task, 'today'));
                $count++;
            }
        }

        $this->info("{$count} notifications de tâches envoyées (Aujourd'hui + Demain).");
    }
}
