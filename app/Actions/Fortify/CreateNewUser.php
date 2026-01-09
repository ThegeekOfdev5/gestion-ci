<?php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Models\Domain;
use App\Models\Tenant;
use App\Models\Company;
use Illuminate\Support\Str;
use App\Models\Subscription;
use Illuminate\Validation\Rule;
use App\Models\OnboardingProgress;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;

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

            // 1️⃣ Créer le tenant
            $tenant = Tenant::create([
                'name' => $input['company_name'],
            ]);

            // 2️⃣ Créer le domaine du tenant
            $subdomain = $this->generateSubdomain($input['company_name']);
            $centralDomain = config('app.central_domain', 'gestion-ci.test');
            $fullDomain = "{$subdomain}.{$centralDomain}";

            $tenant->domains()->create([
                'domain' => $fullDomain,
            ]);

            // 3️⃣ Créer l'utilisateur
            $user = User::create([
                'tenant_id' => $tenant->id,
                'name' => $input['first_name'] . ' ' . $input['last_name'],
                'first_name' => $input['first_name'],
                'last_name' => $input['last_name'],
                'email' => $input['email'],
                'phone' => $input['phone'] ?? null,
                'password' => Hash::make($input['password']),
                'is_active' => true,
            ]);

            // 4️⃣ Assigner rôle admin au premier utilisateur
            $user->assignRole('admin');

            // 5️⃣ Créer la Company (vide pour l'instant)
            Company::create([
                'tenant_id' => $tenant->id,
                'name' => $input['company_name'],
                'email' => $input['email'], // Email par défaut
            ]);

            // 6️⃣ Créer la progression onboarding
            OnboardingProgress::create([
                'tenant_id' => $tenant->id,
                'current_step' => 1,
                'completed' => false,
            ]);

            // 7️⃣ Logger la création
            logger()->info('Nouveau tenant créé', [
                'tenant_id' => $tenant->id,
                'user_id' => $user->id,
                'company_name' => $input['company_name'],
                'domain' => $fullDomain,
            ]);

            return $user;
        });

        // return DB::transaction(function () use ($input) {
        //     // 1. Créer le tenant
        //     $tenant = Tenant::create([
        //         'name' => $input['company_name'],
        //     ]);

        //     // 2. Générer et créer le domaine complet
        //     $subdomain = $this->generateUniqueSubdomain($input['company_name']);
        //     $fullDomain = $this->buildFullDomain($subdomain);

        //     Domain::create([
        //         'domain' => $fullDomain,
        //         'tenant_id' => $tenant->id,
        //     ]);

        //     // 3. Exécuter les actions dans le contexte du tenant
        //     $tenant->run(function () use ($input) {
        //         // Créer la société
        //         Company::create([
        //             'name' => $input['company_name'],
        //         ]);
        //     });

        //     // 4. Créer l'utilisateur
        //     $user = User::create([
        //         'tenant_id' => $tenant->id,
        //         'name' => $input['first_name'] . ' ' . $input['last_name'],
        //         'first_name' => $input['first_name'],
        //         'last_name' => $input['last_name'],
        //         'email' => $input['email'],
        //         'phone' => $input['phone'] ?? null,
        //         'password' => Hash::make($input['password']),
        //         'is_active' => true,
        //     ]);

        //     // 5. Assigner le rôle
        //     $user->assignRole('owner');

        //     return $user;
        // });
    }

    /**
     * Générer un sous-domaine unique à partir du nom de l'entreprise
     */
    protected function generateSubdomain(string $companyName): string
    {
        // Nettoyer le nom : minuscules, sans accents, sans espaces
        $subdomain = strtolower($companyName);

        // Remplacer caractères spéciaux par tirets
        $subdomain = preg_replace('/[^a-z0-9]+/', '-', $subdomain);

        // Supprimer tirets en début/fin
        $subdomain = trim($subdomain, '-');

        // Limiter à 20 caractères
        $subdomain = substr($subdomain, 0, 20);

        // Vérifier unicité
        $originalSubdomain = $subdomain;
        $counter = 1;

        while ($this->subdomainExists($subdomain)) {
            $subdomain = $originalSubdomain . '-' . $counter;
            $counter++;

            // Sécurité : limite à 999 tentatives
            if ($counter > 999) {
                $subdomain = $originalSubdomain . '-' . uniqid();
                break;
            }
        }

        return $subdomain;
    }

    /**
     * Vérifier si le sous-domaine existe déjà
     */
    protected function subdomainExists(string $subdomain): bool
    {
        $centralDomain = config('app.central_domain', 'gestion-ci.test');
        $fullDomain = "{$subdomain}.{$centralDomain}";

        return DB::table('domains')
            ->where('domain', $fullDomain)
            ->exists();
    }
}
