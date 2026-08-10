<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\ChooseRoleController;
use App\Http\Controllers\BrowseController;
use App\Http\Controllers\ClientProfileController;
use App\Http\Controllers\ClientWelcomeController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\CreatorProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\HelpController;
use App\Http\Controllers\LegalController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\MeetingRequestController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProposalController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SavedProjectController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\WelcomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [WelcomeController::class, 'index'])->name('welcome');
Route::get('/terms', [LegalController::class, 'terms'])->name('legal.terms');
Route::get('/privacy', [LegalController::class, 'privacy'])->name('legal.privacy');
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::post('/locale', [LocaleController::class, 'update'])->name('locale.update');
Route::post('/meeting-request', [MeetingRequestController::class, 'store'])
    ->middleware('throttle:5,1')->name('meeting-request.store');

Route::get('/contact', [ContactController::class, 'create'])->name('contact.create');
Route::post('/contact', [ContactController::class, 'store'])
    ->middleware('throttle:5,1')->name('contact.store');

// Public so verified creator profiles can be indexed by search engines.
Route::get('/creators', [CreatorProfileController::class, 'index'])->name('creators.index');
Route::get('/creators/{creatorProfile}', [CreatorProfileController::class, 'show'])->name('creators.show');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified', 'onboarded'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/onboarding', [OnboardingController::class, 'index'])->name('onboarding');

    Route::get('/help', [HelpController::class, 'index'])->name('help');

    Route::get('/choose-role', [ChooseRoleController::class, 'create'])->name('choose-role');
    Route::post('/choose-role', [ChooseRoleController::class, 'store'])->name('choose-role.store');

    Route::get('/welcome', [ClientWelcomeController::class, 'index'])->name('client-welcome');
    Route::post('/welcome', [ClientWelcomeController::class, 'store'])->name('client-welcome.store');

    Route::get('/browse', [BrowseController::class, 'index'])->name('projects.browse');
    Route::get('/saved-projects', [SavedProjectController::class, 'index'])->name('saved-projects.index');

    Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
    Route::get('/projects/create', [ProjectController::class, 'create'])->name('projects.create');
    Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store');
    Route::post('/projects/improve-description', [ProjectController::class, 'improveDescription'])
        ->middleware('throttle:10,1')->name('projects.improve-description');
    Route::get('/projects/{project}/edit', [ProjectController::class, 'edit'])->name('projects.edit');
    Route::patch('/projects/{project}', [ProjectController::class, 'update'])->name('projects.update');
    Route::post('/projects/{project}/cancel', [ProjectController::class, 'cancel'])->name('projects.cancel');
    Route::get('/projects/{project}', [ProjectController::class, 'show'])->name('projects.show');

    Route::get('/proposals', [ProposalController::class, 'index'])->name('proposals.index');
    Route::post('/projects/{project}/proposals', [ProposalController::class, 'store'])->name('proposals.store');
    Route::post('/projects/{project}/generate-cover-letter', [ProposalController::class, 'generateCoverLetter'])
        ->middleware('throttle:10,1')->name('proposals.generate-cover-letter');
    Route::post('/proposals/{proposal}/accept', [ProposalController::class, 'accept'])->name('proposals.accept');
    Route::post('/proposals/{proposal}/reject', [ProposalController::class, 'reject'])->name('proposals.reject');

    Route::get('/clients/{user}', [ClientProfileController::class, 'show'])->name('clients.show');
    Route::post('/clients/{client}/message', [MessageController::class, 'startWithClient'])->name('messages.startWithClient');

    Route::get('/creators/{creatorProfile}/edit', [CreatorProfileController::class, 'edit'])->name('creators.edit');
    Route::patch('/creators/{creatorProfile}', [CreatorProfileController::class, 'update'])->name('creators.update');
    Route::post('/creators/{creatorProfile}/message', [MessageController::class, 'startWithCreator'])->name('messages.start');
    Route::post('/creators/{creatorProfile}/favorite', [FavoriteController::class, 'toggle'])->name('favorites.toggle');

    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/{conversation}', [MessageController::class, 'show'])->name('messages.show');
    Route::post('/messages/{conversation}', [MessageController::class, 'store'])->name('messages.store');

    Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index');

    Route::post('/contracts/{contract}/complete', [ContractController::class, 'complete'])->name('contracts.complete');
    Route::post('/contracts/{contract}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
});

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/verifications', [AdminController::class, 'verifications'])->name('admin.verifications');
    Route::post('/admin/verifications/{creatorProfile}', [AdminController::class, 'verify'])->name('admin.verifications.verify');
    Route::get('/admin/users', [AdminController::class, 'users'])->name('admin.users');
    Route::delete('/admin/users/{user}', [AdminController::class, 'destroyUser'])->name('admin.users.destroy');
    Route::post('/admin/creators/remind-onboarding', [AdminController::class, 'remindIncompleteOnboarding'])->name('admin.creators.remind-onboarding');
    Route::get('/admin/creators/{user}/edit', [AdminController::class, 'editCreatorProfile'])->name('admin.creators.edit');
    Route::patch('/admin/creators/{user}', [AdminController::class, 'updateCreatorProfile'])->name('admin.creators.update');
    Route::get('/admin/contact-messages', [AdminController::class, 'contactMessages'])->name('admin.contact-messages');
    Route::patch('/admin/contact-messages/{contactMessage}', [AdminController::class, 'updateContactMessageStatus'])->name('admin.contact-messages.update');
});

require __DIR__.'/auth.php';
