<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Livewire\Volt\Volt;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\InteractionController;
use App\Http\Controllers\TypeInteractionController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\InformationController;
use App\Http\Controllers\TaskController;
Route::get('/chatbot', function () {
    return view('chatbot');
});

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/roles', [RolePermissionController::class, 'index'])->name('roles.index');

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
    Route::resource('users', UserController::class)->except(['show']);

    // routes supplémentaires pour export et suppression en masse
    Route::get('users/export', [UserController::class, 'export'])->name('users.export');
    Route::post('users/bulk-destroy', [UserController::class, 'bulkDestroy'])->name('users.bulkDestroy');
    
    // Add these new routes for contacts in admin section
    Route::get('contacts/export', [ContactController::class, 'export'])->name('contacts.export');
    Route::post('contacts/bulk-destroy', [ContactController::class, 'bulkDestroy'])->name('contacts.bulkDestroy');

    //Products
    Route::resource('categories', CategoryController::class);
    Route::resource('products', ProductController::class);

    //facts
    Route::apiResource('informations', InformationController::class);

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
    
   
    Route::get('/contacts/{contact}/interactions', [InteractionController::class, 'index'])
        ->name('contacts.interactions.index');
    Route::post('/contacts/{contact}/interactions', [InteractionController::class, 'store'])
        ->name('contacts.interactions.store');
    Route::put('/contacts/{contact}/interactions/{interaction}', [InteractionController::class, 'update'])
        ->name('contacts.interactions.update');
    Route::post('/contacts/{contact}/interactions/{interaction}/notes', [InteractionController::class, 'addNote'])
        ->name('contacts.interactions.addNote');
    Route::delete('/contacts/{contact}/interactions/{interaction}', [InteractionController::class, 'destroy'])
        ->name('contacts.interactions.destroy');
        

    
    Route::get('/interactions', [InteractionController::class, 'all'])
        ->name('interactions.all');
    
    Route::get('/interactions/modern', [InteractionController::class, 'modern'])
        ->name('interactions.modern');
    
    Route::get('/interactions/dashboard', [InteractionController::class, 'dashboard'])
        ->name('interactions.dashboard');

    Route::resource('types-interactions', TypeInteractionController::class);

    // Routes pour la messagerie
    Route::prefix('messages')->name('messages.')->group(function () {
        Route::get('/', [\App\Http\Controllers\MessageController::class, 'index'])->name('index');
        Route::get('/new', [\App\Http\Controllers\MessageController::class, 'create'])->name('create');
        Route::get('/search', [\App\Http\Controllers\MessageController::class, 'search'])->name('search');
        Route::post('/private', [\App\Http\Controllers\MessageController::class, 'createPrivate'])->name('create.private');
        Route::post('/group', [\App\Http\Controllers\MessageController::class, 'createGroup'])->name('create.group');
        Route::get('/{conversation}', [\App\Http\Controllers\MessageController::class, 'show'])->name('show');
        Route::post('/{conversation}/send', [\App\Http\Controllers\MessageController::class, 'store'])->name('store');
        Route::post('/{conversation}/read', [\App\Http\Controllers\MessageController::class, 'markAsRead'])->name('read');
        Route::get('/{conversation}/messages', [\App\Http\Controllers\MessageController::class, 'getMessages'])->name('get-messages');
    });
    Route::resource('tasks', TaskController::class);
    Route::get('/messenger', function () {
        return view('messenger.index');
    })->name('messenger');


    
});

require __DIR__.'/auth.php';
