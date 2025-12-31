<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Contracts\LoginResponse;
use Laravel\Fortify\Contracts\RegisterResponse;
use Laravel\Fortify\Contracts\LogoutResponse;

class FortifyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // 🔥 REDIRECTION APRÈS LOGIN
        $this->app->instance(LoginResponse::class, new class implements LoginResponse {
            public function toResponse($request)
            {
                $user = auth()->user();

                if (!$user || !$user->tenant) {
                    return redirect()->route('home')->withErrors([
                        'error' => 'Impossible de déterminer votre entreprise.'
                    ]);
                }

                // Récupérer le premier domaine du tenant
                $domain = $user->tenant->domains()->first();

                if (!$domain) {
                    return redirect()->route('home')->withErrors([
                        'error' => 'Votre entreprise n\'a pas de domaine configuré.'
                    ]);
                }

                // Construire l'URL du tenant
                $tenantUrl = $this->buildTenantUrl($domain->domain);

                // Redirection vers le dashboard du tenant
                return redirect()->away($tenantUrl . '/dashboard');
            }

            private function buildTenantUrl(string $domain): string
            {
                $protocol = request()->secure() ? 'https' : 'http';
                return "{$protocol}://{$domain}";
            }
        });

        // 🔥 REDIRECTION APRÈS INSCRIPTION
        $this->app->instance(RegisterResponse::class, new class implements RegisterResponse {
            public function toResponse($request)
            {
                $user = auth()->user();

                if (!$user || !$user->tenant) {
                    return redirect()->route('home')->withErrors([
                        'error' => 'Erreur lors de la création de votre compte.'
                    ]);
                }

                // Récupérer le domaine du tenant nouvellement créé
                $domain = $user->tenant->domains()->first();

                if (!$domain) {
                    return redirect()->route('home')->withErrors([
                        'error' => 'Erreur lors de la configuration de votre entreprise.'
                    ]);
                }

                // Construire l'URL du tenant
                $tenantUrl = $this->buildTenantUrl($domain->domain);

                // Redirection vers le dashboard avec message de bienvenue
                return redirect()->away($tenantUrl . '/dashboard?welcome=1');
            }

            private function buildTenantUrl(string $domain): string
            {
                $protocol = request()->secure() ? 'https' : 'http';
                return "{$protocol}://{$domain}";
            }
        });

        // 🔥 REDIRECTION APRÈS LOGOUT
        $this->app->instance(LogoutResponse::class, new class implements LogoutResponse {
            public function toResponse($request)
            {
                // Redirection vers le domaine central
                $centralDomain = config('app.central_domain', 'gestion-ci.test');
                $protocol = request()->secure() ? 'https' : 'http';
                $centralUrl = "{$protocol}://{$centralDomain}";

                return redirect()->away($centralUrl)
                    ->with('status', 'Vous êtes déconnecté.');
            }
        });
    }

    public function boot(): void
    {
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        // Rate limiting
        RateLimiter::for('login', function (Request $request) {
            $email = (string) $request->email;
            return Limit::perMinute(5)->by($email . $request->ip());
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });

        // Vues Fortify (sur domaine central uniquement)
        Fortify::loginView(fn() => view('livewire.auth.login'));
        Fortify::registerView(fn() => view('livewire.auth.register'));
        Fortify::requestPasswordResetLinkView(fn() => view('livewire.auth.forgot-password'));
        Fortify::resetPasswordView(fn($request) => view('livewire.auth.reset-password', ['request' => $request]));
    }
}
