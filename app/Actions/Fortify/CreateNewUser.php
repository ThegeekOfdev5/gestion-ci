<?php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Models\Tenant;
use App\Models\Domain;
use App\Models\Company;
use App\Models\Subscription;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    public function create(array $input): User
    {
        Validator::make($input, [
            'company_name' => ['required', 'string', 'max:255'],
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class),
            ],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => $this->passwordRules(),
            'terms' => ['required', 'accepted'],
        ], [
            'company_name.required' => 'Le nom de l\'entreprise est obligatoire.',
            'first_name.required' => 'Le prénom est obligatoire.',
            'last_name.required' => 'Le nom est obligatoire.',
            'email.required' => 'L\'email est obligatoire.',
            'email.unique' => 'Cet email est déjà utilisé.',
            'password.required' => 'Le mot de passe est obligatoire.',
            'terms.accepted' => 'Vous devez accepter les conditions d\'utilisation.',
        ])->validate();

        return DB::transaction(function () use ($input) {
            // 1. Créer le tenant
            $tenant = Tenant::create([
                'name' => $input['company_name'],
                'subscription_plan' => 'essentiel',
                'subscription_status' => 'trial',
                'trial_ends_at' => now()->addDays(30),
                'billing_email' => $input['email'],
            ]);

            // 2. Générer et créer le domaine complet
            $subdomain = $this->generateUniqueSubdomain($input['company_name']);
            $fullDomain = $this->buildFullDomain($subdomain);

            Domain::create([
                'domain' => $fullDomain,
                'tenant_id' => $tenant->id,
            ]);

            // 3. Exécuter les actions dans le contexte du tenant
            $tenant->run(function () use ($input) {
                // Créer la société
                Company::create([
                    'legal_name' => $input['company_name'],
                    'email' => $input['email'],
                    'phone' => $input['phone'] ?? null,
                    'currency' => 'XOF',
                    'country' => 'CI',
                    'invoice_prefix' => 'FAC',
                    'quote_prefix' => 'DEV',
                    'next_invoice_number' => 1,
                    'next_quote_number' => 1,
                    'payment_terms_days' => 30,
                ]);

                // Créer l'abonnement
                Subscription::create([
                    'plan' => 'essentiel',
                    'billing_cycle' => 'monthly',
                    'amount' => 12000,
                    'currency' => 'XOF',
                    'status' => 'trialing',
                    'trial_ends_at' => now()->addDays(30),
                    'current_period_start' => now(),
                    'current_period_end' => now()->addDays(30),
                    'auto_renew' => true,
                ]);
            });

            // 4. Créer l'utilisateur
            $user = User::create([
                'tenant_id' => $tenant->id,
                'first_name' => $input['first_name'],
                'last_name' => $input['last_name'],
                'email' => $input['email'],
                'phone' => $input['phone'] ?? null,
                'password' => Hash::make($input['password']),
                'role' => 'owner',
                'is_active' => true,
                'email_verified_at' => now(),
            ]);

            // 5. Assigner le rôle
            $user->assignRole('owner');

            return $user;
        });
    }

    /**
     * Générer un sous-domaine unique à partir du nom de l'entreprise
     */
    private function generateUniqueSubdomain(string $companyName): string
    {
        $baseSlug = Str::slug($companyName);
        $slug = $baseSlug;
        $counter = 1;

        // Vérifier l'unicité du domaine complet
        while ($this->fullDomainExists($slug)) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Construire le domaine complet (sous-domaine + domaine central)
     */
    private function buildFullDomain(string $subdomain): string
    {
        $centralDomain = config('app.central_domain', 'gestion-ci.test');
        return "{$subdomain}.{$centralDomain}";
    }

    /**
     * Vérifier si le domaine complet existe déjà
     */
    private function fullDomainExists(string $subdomain): bool
    {
        $fullDomain = $this->buildFullDomain($subdomain);
        return Domain::where('domain', $fullDomain)->exists();
    }
}
