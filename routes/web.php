<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Livewire\Volt\Volt;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\InteractionController;
use App\Http\Controllers\TypeInteractionController;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/roles', [RolePermissionController::class, 'index'])->name('roles.index');

    // 👇 Routes pour les formulaires
   // Gestion des rôles
    Route::post('/roles', [RolePermissionController::class, 'storeRole'])
        ->name('roles.store');
    
    Route::put('/roles/{role}', [RolePermissionController::class, 'updateRole'])
        ->name('roles.update');
    
    Route::delete('roles/{role}', [RolePermissionController::class, 'destroyRole'])
        ->name('roles.destroy'); // Le nom réel devient 'admin.roles.destroy'
    
    // Gestion des permissions
    Route::post('/permissions', [RolePermissionController::class, 'storePermission'])
        ->name('permissions.store');
    
    Route::put('/permissions/{permission}', [RolePermissionController::class, 'updatePermission'])
        ->name('admin.permissions.update');
    
    Route::delete('/permissions/{permission}', [RolePermissionController::class, 'destroyPermission'])
        ->name('permissions.destroy');

    // User Management Routes
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
});

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
    Volt::route('settings/password', 'settings.password')->name('password.edit');
    Volt::route('settings/appearance', 'settings.appearance')->name('appearance.edit');

    Volt::route('settings/two-factor', 'settings.two-factor')
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                    && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');

    // Routes pour les contacts
    Route::get('/contacts/information', [ContactController::class, 'information'])
        ->name('contacts.information');
    Route::resource('contacts', ContactController::class);
    
    // Routes pour les interactions
    Route::get('/contacts/{contact}/interactions', [InteractionController::class, 'index'])
        ->name('contacts.interactions.index');
    Route::post('/contacts/{contact}/interactions', [InteractionController::class, 'store'])
        ->name('contacts.interactions.store');
    Route::post('/contacts/{contact}/interactions/{interaction}/notes', [InteractionController::class, 'addNote'])
        ->name('contacts.interactions.addNote');
    Route::delete('/contacts/{contact}/interactions/{interaction}', [InteractionController::class, 'destroy'])
        ->name('contacts.interactions.destroy');

    
    Route::get('/interactions', [InteractionController::class, 'all'])
        ->name('interactions.all');

    // Routes pour les types d'interactions
    Route::resource('types-interactions', TypeInteractionController::class);
});


require __DIR__.'/auth.php';
