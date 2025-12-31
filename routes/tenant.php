<?php

declare(strict_types=1);

use Livewire\Volt\Volt;
use Laravel\Fortify\Features;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\InitializeTenancyBySubdomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomainOrSubdomain;

/*
|--------------------------------------------------------------------------
| ROUTES TENANT (sous-domaines uniquement)
|--------------------------------------------------------------------------
| Ces routes sont accessibles sur *.gestion-ci.test
| Ex: test.gestion-ci.test, demo.gestion-ci.test
*/

Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {

    // Routes protégées par authentification
    Route::middleware(['auth'])->group(function () {

        // Dashboard
        Route::get('/dashboard', function () {
            return view('dashboard');
        })->name('tenant.dashboard');

        // Clients
        Route::prefix('clients')->name('tenant.clients.')->group(function () {
            Route::get('/', function () {
                return view('clients.index');
            })->name('index');
        });

        // Produits
        Route::prefix('produits')->name('tenant.products.')->group(function () {
            Route::get('/', function () {
                return view('products.index');
            })->name('index');
        });

        // Factures
        Route::prefix('factures')->name('tenant.invoices.')->group(function () {
            Route::get('/', function () {
                return view('invoices.index');
            })->name('index');
        });

        // Devis
        Route::prefix('devis')->name('tenant.quotes.')->group(function () {
            Route::get('/', function () {
                return view('quotes.index');
            })->name('index');
        });

        // Paramètres
        // Route::prefix('parametres')->name('tenant.settings.')->group(function () {
        //     Route::get('/', function () {
        //         return view('settings.index');
        //     })->name('index');
        // });

        Route::middleware(['auth'])->group(function () {
            Route::redirect('settings', 'settings/profile');

            Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
            Volt::route('settings/password', 'settings.password')->name('user-password.edit');
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
        });
    });
});
