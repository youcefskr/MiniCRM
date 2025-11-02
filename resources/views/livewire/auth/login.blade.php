<?php

use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Features;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth')] class extends Component {
    #[Validate('required|string|email')]
    public string $email = '';

    #[Validate('required|string')]
    public string $password = '';

    public bool $remember = false;

    /**
     * Handle an incoming authentication request.
     *
     * @return \Illuminate\Http\RedirectResponse|void
     */
    public function login()
    {
        $this->validate();

        $this->ensureIsNotRateLimited();

        $user = $this->validateCredentials();

        if (Features::canManageTwoFactorAuthentication() && $user->hasEnabledTwoFactorAuthentication()) {
            Session::put([
                'login.id' => $user->getKey(),
                'login.remember' => $this->remember,
            ]);

            return redirect()->route('two-factor.login');
        }

        Auth::login($user, $this->remember);

        RateLimiter::clear($this->throttleKey());
        Session::regenerate();

        return redirect()->intended(route('dashboard'));
    }

    /**
     * Validate the user's credentials.
     */
    protected function validateCredentials(): User
    {
        $user = Auth::getProvider()->retrieveByCredentials(['email' => $this->email, 'password' => $this->password]);

        if (! $user || ! Auth::getProvider()->validateCredentials($user, ['password' => $this->password])) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        return $user;
    }

    /**
     * Ensure the authentication request is not rate limited.
     */
    protected function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => __('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the authentication rate limiting throttle key.
     */
    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->email).'|'.request()->ip());
    }
}; ?>

<div class="min-h-screen flex flex-col items-center justify-center bg-gray-50 dark:bg-gray-900">
    <div class="w-full max-w-md">
        <!-- CRM Title -->
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-primary-600 dark:text-primary-400">CRM System</h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">Welcome back!</p>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow-xl rounded-lg p-8">
            <x-auth-header 
                :title="__('Sign in to your account')" 
                :description="__('Manage your business relationships')" 
            />

            <!-- Session Status -->
            <x-auth-session-status class="mb-4 text-center" :status="session('status')" />

            <form method="POST" wire:submit="login" class="space-y-6">
                <!-- Email Address -->
                <flux:input
                    wire:model="email"
                    :label="__('Email address')"
                    type="email"
                    required
                    autofocus
                    autocomplete="email"
                    placeholder="email@example.com"
                    class="block w-full"
                />

                <!-- Password -->
                <div class="relative">
                    <flux:input
                        wire:model="password"
                        :label="__('Password')"
                        type="password"
                        required
                        autocomplete="current-password"
                        :placeholder="__('Password')"
                        viewable
                        class="block w-full"
                    />

                    @if (Route::has('password.request'))
                        <flux:link class="absolute top-0 text-sm end-0 text-primary-600 hover:text-primary-500 dark:text-primary-400" :href="route('password.request')" wire:navigate>
                            {{ __('Forgot password?') }}
                        </flux:link>
                    @endif
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between">
                    <flux:checkbox wire:model="remember" :label="__('Keep me signed in')" />
                </div>

                <flux:button variant="primary" type="submit" class="w-full py-3" data-test="login-button">
                    {{ __('Sign in to account') }}
                </flux:button>
            </form>

            @if (Route::has('register'))
                <div class="mt-6 text-center">
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        {{ __('New to our CRM?') }}
                        <flux:link :href="route('register')" wire:navigate class="font-medium text-primary-600 hover:text-primary-500 dark:text-primary-400">
                            {{ __('Create an account') }}
                        </flux:link>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
