<?php

use App\Http\Controllers\DeployController;
use App\Http\Controllers\Web\AdminController;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\CommunicationController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\FollowUpController;
use App\Http\Controllers\Web\ImportExportController;
use App\Http\Controllers\Web\LeadController;
use App\Http\Controllers\Web\LeadDiscoveryController;
use App\Http\Controllers\Web\LeadListController;
use App\Http\Controllers\Web\SocialAccountController;
use Illuminate\Support\Facades\Route;

// Shared Hosting Deployment & Maintenance Routes (Protected by Secret Key)
Route::prefix('deploy')->group(function () {
    Route::get('/', [DeployController::class, 'index'])->name('deploy.index');
    Route::get('/migrate', [DeployController::class, 'migrate'])->name('deploy.migrate');
    Route::get('/migrate-seed', [DeployController::class, 'migrateSeed'])->name('deploy.migrate_seed');
    Route::get('/storage-link', [DeployController::class, 'storageLink'])->name('deploy.storage_link');
    Route::get('/clear-cache', [DeployController::class, 'clearCache'])->name('deploy.clear_cache');
    Route::get('/composer', [DeployController::class, 'composerInstall'])->name('deploy.composer');
});

// Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/', function () {
        return redirect()->route('login');
    });
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/profile', [AuthController::class, 'showProfile'])->name('profile');
    Route::post('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');

    // Dashboard & Analytics
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Lead Discovery
    Route::get('/discovery', [LeadDiscoveryController::class, 'index'])->name('discovery.index');
    Route::post('/discovery/save', [LeadDiscoveryController::class, 'saveToCrm'])->name('discovery.save');

    // Leads CRM
    Route::get('/leads', [LeadController::class, 'index'])->name('leads.index');
    Route::get('/leads/create', [LeadController::class, 'create'])->name('leads.create');
    Route::post('/leads', [LeadController::class, 'store'])->name('leads.store');
    Route::get('/leads/{lead}', [LeadController::class, 'show'])->name('leads.show');
    Route::put('/leads/{lead}', [LeadController::class, 'update'])->name('leads.update');
    Route::delete('/leads/{lead}', [LeadController::class, 'destroy'])->name('leads.destroy');
    Route::post('/leads/{lead}/notes', [LeadController::class, 'addNote'])->name('leads.notes.store');
    Route::post('/leads/{lead}/assign', [LeadController::class, 'assign'])->name('leads.assign');
    Route::post('/leads/{lead}/tags', [LeadController::class, 'updateTags'])->name('leads.tags.update');
    Route::post('/leads/{lead}/lists', [LeadController::class, 'updateLists'])->name('leads.lists.update');
    Route::post('/leads/bulk/action', [LeadController::class, 'bulkAction'])->name('leads.bulk');

    // Communication Center
    Route::get('/communication', [CommunicationController::class, 'index'])->name('communication.index');
    Route::post('/leads/{lead}/communication/log', [CommunicationController::class, 'logCommunication'])->name('communication.log');
    Route::post('/communication/templates', [CommunicationController::class, 'storeTemplate'])->name('communication.templates.store');
    Route::delete('/communication/templates/{template}', [CommunicationController::class, 'deleteTemplate'])->name('communication.templates.destroy');

    // Connected Instagram & Meta Accounts
    Route::get('/social-accounts', [SocialAccountController::class, 'index'])->name('social.index');
    Route::get('/oauth/instagram/connect', [SocialAccountController::class, 'redirectToMeta'])->name('social.connect');
    Route::get('/oauth/instagram/callback', [SocialAccountController::class, 'handleMetaCallback'])->name('social.callback');
    Route::delete('/social-accounts/{account}', [SocialAccountController::class, 'disconnect'])->name('social.disconnect');
    Route::post('/social-accounts/dev-connect', [SocialAccountController::class, 'connectDevelopmentAccount'])->name('social.dev_connect');

    // Custom Lead Lists
    Route::get('/lists', [LeadListController::class, 'index'])->name('lists.index');
    Route::post('/lists', [LeadListController::class, 'store'])->name('lists.store');
    Route::get('/lists/{list}', [LeadListController::class, 'show'])->name('lists.show');
    Route::delete('/lists/{list}/leads/{lead}', [LeadListController::class, 'removeLead'])->name('lists.remove-lead');
    Route::delete('/lists/{list}', [LeadListController::class, 'destroy'])->name('lists.destroy');

    // Follow-ups & Reminders
    Route::get('/followups', [FollowUpController::class, 'index'])->name('followups.index');
    Route::post('/followups', [FollowUpController::class, 'store'])->name('followups.store');
    Route::post('/followups/{followUp}/complete', [FollowUpController::class, 'complete'])->name('followups.complete');
    Route::delete('/followups/{followUp}', [FollowUpController::class, 'destroy'])->name('followups.destroy');

    // Import & Export
    Route::get('/import-export', [ImportExportController::class, 'index'])->name('import-export.index');
    Route::post('/import-export/import', [ImportExportController::class, 'import'])->name('import-export.import');
    Route::get('/import-export/export', [ImportExportController::class, 'export'])->name('import-export.export');
    Route::get('/import-export/sample', [ImportExportController::class, 'downloadSample'])->name('import-export.sample');

    // Admin Panel (Protected by Admin Role check in controller)
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('index');
        Route::post('/team', [AdminController::class, 'storeTeamMember'])->name('team.store');
        Route::put('/team/{member}', [AdminController::class, 'updateTeamMember'])->name('team.update');
        Route::post('/sources/{source}/toggle', [AdminController::class, 'toggleSource'])->name('sources.toggle');
        Route::post('/subscription', [AdminController::class, 'updateSubscription'])->name('subscription.update');
        Route::get('/audit-logs', [AdminController::class, 'auditLogs'])->name('audit-logs');
    });
});
