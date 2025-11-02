<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth')] class extends Component {
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        event(new Registered(($user = User::create($validated))));

        Auth::login($user);

        Session::regenerate();

        $this->redirectIntended(route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div class="min-h-screen flex flex-col items-center justify-center bg-gray-50 dark:bg-gray-900">
    <div class="w-full max-w-md">
        <!-- CRM Title -->
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-primary-600 dark:text-primary-400">CRM System</h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">Create your account</p>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow-xl rounded-lg p-8">
            <x-auth-header 
                :title="__('Register new account')" 
                :description="__('Join our CRM platform')" 
            />

            <!-- Session Status -->
            <x-auth-session-status class="mb-4 text-center" :status="session('status')" />

            <form method="POST" wire:submit="register" class="space-y-6">
                <!-- Name -->
                <flux:input
                    wire:model="name"
                    :label="__('Full name')"
                    type="text"
                    required
                    autofocus
                    autocomplete="name"
                    :placeholder="__('Enter your full name')"
                    class="block w-full"
                />

                <!-- Email Address -->
                <flux:input
                    wire:model="email"
                    :label="__('Email address')"
                    type="email"
                    required
                    autocomplete="email"
                    placeholder="email@example.com"
                    class="block w-full"
                />

                <!-- Password -->
                <flux:input
                    wire:model="password"
                    :label="__('Password')"
                    type="password"
                    required
                    autocomplete="new-password"
                    :placeholder="__('Create a password')"
                    viewable
                    class="block w-full"
                />

                <!-- Confirm Password -->
                <flux:input
                    wire:model="password_confirmation"
                    :label="__('Confirm password')"
                    type="password"
                    required
                    autocomplete="new-password"
                    :placeholder="__('Repeat your password')"
                    viewable
                    class="block w-full"
                />

                <flux:button variant="primary" type="submit" class="w-full py-3" data-test="register-user-button">
                    {{ __('Create account') }}
                </flux:button>
            </form>

            <div class="mt-6 text-center">
                <div class="text-sm text-gray-600 dark:text-gray-400">
                    {{ __('Already have an account?') }}
                    <flux:link :href="route('login')" wire:navigate class="font-medium text-primary-600 hover:text-primary-500 dark:text-primary-400">
                        {{ __('Sign in instead') }}
                    </flux:link>
                </div>
            </div>
        </div>
    </div>
</div>
