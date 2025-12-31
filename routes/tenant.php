<?php

declare(strict_types=1);

use Livewire\Volt\Volt;
use Laravel\Fortify\Features;
use Illuminate\Support\Facades\Route;
use App\Livewire\Onboarding\OnboardingWizard;
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

    Route::middleware(['auth'])->group(function () {
        Route::get('/onboarding', OnboardingWizard::class)
            ->name('onboarding');
    });

    // Routes protégées (auth + onboarding)
    Route::middleware(['auth', 'onboarding'])->group(function () {
        // Dashboard
        Route::get('/dashboard', function () {
            return view('dashboard');
        })->name('tenant.dashboard');

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
