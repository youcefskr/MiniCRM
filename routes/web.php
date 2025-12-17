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

use App\Http\Controllers\DashboardController;

Route::get('/dashboard', [DashboardController::class, 'index'])
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


    // Products & Categories
    Route::get('products/export', [ProductController::class, 'export'])->name('products.export');
    Route::post('products/{product}/update-stock', [ProductController::class, 'updateStock'])->name('products.updateStock');
    Route::resource('categories', CategoryController::class);
    Route::resource('products', ProductController::class);

    //facts
    Route::apiResource('informations', InformationController::class);

    // Activity Logs
    Route::get('activity-logs', [\App\Http\Controllers\ActivityLogController::class, 'index'])->name('activity-logs.index');
    Route::get('activity-logs/export', [\App\Http\Controllers\ActivityLogController::class, 'export'])->name('activity-logs.export');
    Route::get('activity-logs/security', [\App\Http\Controllers\ActivityLogController::class, 'securityDashboard'])->name('activity-logs.security');
    Route::get('activity-logs/user/{user}', [\App\Http\Controllers\ActivityLogController::class, 'userHistory'])->name('activity-logs.user-history');
    Route::get('activity-logs/model', [\App\Http\Controllers\ActivityLogController::class, 'modelHistory'])->name('activity-logs.model-history');
    Route::get('activity-logs/{activityLog}', [\App\Http\Controllers\ActivityLogController::class, 'show'])->name('activity-logs.show');

});

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
    Volt::route('settings/password', 'settings.password')->name('password.edit');
    Volt::route('settings/appearance', 'settings.appearance')->name('appearance.edit');
    Volt::route('settings/language', 'settings.language')->name('language.edit');

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
        

    
    Route::get('/interactions', [InteractionController::class, 'globalIndex'])
        ->name('interactions.index');

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
    Route::post('/tasks/{task}/update-status', [\App\Http\Controllers\TaskController::class, 'updateStatus'])->name('tasks.update-status');
    
    // Opportunities
    Route::resource('opportunities', \App\Http\Controllers\OpportunityController::class);
    Route::post('/opportunities/{opportunity}/update-stage', [\App\Http\Controllers\OpportunityController::class, 'updateStage'])->name('opportunities.update-stage');

    Route::get('/messenger', function () {
        return view('messenger.index');
    })->name('messenger');

    // Subscriptions
    Route::resource('subscriptions', \App\Http\Controllers\SubscriptionController::class);
    Route::post('/subscriptions/{subscription}/pause', [\App\Http\Controllers\SubscriptionController::class, 'pause'])->name('subscriptions.pause');
    Route::post('/subscriptions/{subscription}/resume', [\App\Http\Controllers\SubscriptionController::class, 'resume'])->name('subscriptions.resume');
    Route::post('/subscriptions/{subscription}/cancel', [\App\Http\Controllers\SubscriptionController::class, 'cancel'])->name('subscriptions.cancel');
    Route::post('/subscriptions/{subscription}/renew', [\App\Http\Controllers\SubscriptionController::class, 'renew'])->name('subscriptions.renew');
    Route::get('/subscriptions/{subscription}/generate-invoice', [\App\Http\Controllers\SubscriptionController::class, 'generateInvoice'])->name('subscriptions.generate-invoice');
    Route::get('/subscriptions-export', [\App\Http\Controllers\SubscriptionController::class, 'export'])->name('subscriptions.export');

    // Invoices
    Route::resource('invoices', \App\Http\Controllers\InvoiceController::class);
    Route::post('/invoices/{invoice}/send', [\App\Http\Controllers\InvoiceController::class, 'send'])->name('invoices.send');
    Route::post('/invoices/{invoice}/mark-as-paid', [\App\Http\Controllers\InvoiceController::class, 'markAsPaid'])->name('invoices.mark-as-paid');
    Route::post('/invoices/{invoice}/add-payment', [\App\Http\Controllers\InvoiceController::class, 'addPayment'])->name('invoices.add-payment');
    Route::post('/invoices/{invoice}/cancel', [\App\Http\Controllers\InvoiceController::class, 'cancel'])->name('invoices.cancel');
    Route::get('/invoices/{invoice}/download', [\App\Http\Controllers\InvoiceController::class, 'download'])->name('invoices.download');
    Route::get('/invoices/{invoice}/duplicate', [\App\Http\Controllers\InvoiceController::class, 'duplicate'])->name('invoices.duplicate');
    Route::get('/invoices-export', [\App\Http\Controllers\InvoiceController::class, 'export'])->name('invoices.export');
    
});

require __DIR__.'/auth.php';
