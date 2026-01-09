<?php

use Livewire\Volt\Volt;
use Laravel\Fortify\Features;
use Illuminate\Support\Facades\Route;
use App\Livewire\Onboarding\OnboardingWizard;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

/*
|--------------------------------------------------------------------------
| ROUTES CENTRALES (domaine principal uniquement)
|--------------------------------------------------------------------------
| Ces routes sont accessibles sur gestion-ci.test
| C'est ici que se font les inscriptions et connexions
*/

Route::domain(config('app.central_domain'))->group(function () {

    // Page d'accueil
    Route::get('/', function () {
        return view('welcome');
    })->name('home');

    // Fortify gère automatiquement :
    // - GET/POST /login
    // - GET/POST /register
    // - GET/POST /forgot-password
    // - GET/POST /reset-password
    // - POST /logout
});


/*
|--------------------------------------------------------------------------
| ROUTE ABONNEMENT EXPIRÉ (accessible partout)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->get('/subscription-expired', function () {
    return view('subscription.expired');
})->name('subscription.expired');
