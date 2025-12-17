<?php

namespace App\Providers;

use App\Listeners\AuthActivityLogger;
use App\Models\Contact;
use App\Models\Interaction;
use App\Models\Invoice;
use App\Models\Opportunity;
use App\Models\Product;
use App\Models\Subscription;
use App\Models\Task;
use App\Models\User;
use App\Observers\ActivityLogObserver;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Email verification
        VerifyEmail::toMailUsing(function (object $notifiable, string $url) {
            return (new MailMessage)
                ->subject('Vérifiez votre adresse email')
                ->greeting('Bonjour ' . $notifiable->name . ' !')
                ->line('Cliquez sur le bouton ci-dessous pour vérifier votre adresse email.')
                ->action('Vérifier mon email', $url)
                ->line('Si vous n\'avez pas créé de compte, aucune action n\'est requise.')
                ->salutation('Cordialement, L\'équipe ' . config('app.name'));
        });

        // Register Activity Log Observers
        $this->registerActivityLogObservers();

        // Register Auth Event Subscriber
        Event::subscribe(AuthActivityLogger::class);
    }

    /**
     * Register observers for activity logging
     */
    protected function registerActivityLogObservers(): void
    {
        $modelsToTrack = [
            Contact::class,
            Opportunity::class,
            Product::class,
            Interaction::class,
            Task::class,
            User::class,
            Subscription::class,
            Invoice::class,
        ];

        foreach ($modelsToTrack as $model) {
            $model::observe(ActivityLogObserver::class);
        }
    }
}

