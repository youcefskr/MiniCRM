<?php

namespace App\Listeners;

use App\Models\ActivityLog;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Auth\Events\PasswordReset;

class AuthActivityLogger
{
    /**
     * Handle user login event.
     */
    public function handleLogin(Login $event): void
    {
        ActivityLog::logLogin($event->user);
    }

    /**
     * Handle user logout event.
     */
    public function handleLogout(Logout $event): void
    {
        if ($event->user) {
            ActivityLog::logLogout($event->user);
        }
    }

    /**
     * Handle failed login attempt.
     */
    public function handleFailed(Failed $event): void
    {
        $email = $event->credentials['email'] ?? 'unknown';
        ActivityLog::logLoginFailed($email);
    }

    /**
     * Handle account lockout.
     */
    public function handleLockout(Lockout $event): void
    {
        $email = $event->request->input('email', 'unknown');
        
        ActivityLog::log(
            action: 'lockout',
            module: 'auth',
            description: "Compte verrouillé pour tentatives répétées: {$email}",
            isSensitive: true,
            severity: 'danger'
        );
    }

    /**
     * Handle password reset.
     */
    public function handlePasswordReset(PasswordReset $event): void
    {
        ActivityLog::log(
            action: 'password_change',
            module: 'auth',
            description: "Mot de passe réinitialisé pour l'utilisateur {$event->user->name}",
            model: $event->user,
            isSensitive: true,
            severity: 'warning'
        );
    }

    /**
     * Register the listeners for the subscriber.
     */
    public function subscribe($events): array
    {
        return [
            Login::class => 'handleLogin',
            Logout::class => 'handleLogout',
            Failed::class => 'handleFailed',
            Lockout::class => 'handleLockout',
            PasswordReset::class => 'handlePasswordReset',
        ];
    }
}
