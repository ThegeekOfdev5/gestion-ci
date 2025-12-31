<?php
// app/Livewire/Onboarding/OnboardingWizard.php

namespace App\Livewire\Onboarding;

use App\Models\Company;
use App\Models\OnboardingProgress;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

class OnboardingWizard extends Component
{
    use WithFileUploads;

    // Configuration
    public const TOTAL_STEPS = 4;

    // État du wizard
    public int $currentStep = 1;
    public ?OnboardingProgress $progress = null;
    public bool $isLoading = false;

    // Step 1: Informations entreprise de base
    public string $companyName = '';
    public string $companyLegalName = '';
    public string $companyEmail = '';
    public string $companyPhone = '';
    public string $companyMobile = '';

    // Step 2: Identifiants fiscaux CI
    public string $nif = '';
    public string $rccm = '';
    public string $ice = '';
    public string $ifu = '';
    public string $taxRegime = 'reel_simplifie';

    // Step 3: Adresse & Logo
    public string $address = '';
    public string $city = 'Abidjan';
    public string $postalCode = '';
    public string $country = 'Côte d\'Ivoire';
    public $logo;
    public ?string $existingLogo = null;
    public bool $logoChanged = false;

    // Step 4: Préférences facturation
    public string $invoicePrefix = 'FAC';
    public string $quotePrefix = 'DEV';
    public float $defaultTaxRate = 18.00;
    public string $currency = 'XOF';
    public string $timezone = 'Africa/Abidjan';

    // Validation en temps réel
    public bool $showValidationErrors = false;

    /**
     * Initialisation du composant
     */
    public function mount(): void
    {
        $tenant = Auth::user()->tenant;

        // Charger ou créer progression
        $this->progress = OnboardingProgress::firstOrCreate(
            ['tenant_id' => $tenant->id],
            [
                'current_step' => 1,
                'completed' => false,
            ]
        );

        // Si onboarding déjà complété, rediriger vers dashboard
        if ($this->progress->completed) {
            $this->redirect(route('dashboard'), navigate: true);
            return;
        }

        // Charger données entreprise existantes
        if ($company = $tenant->company) {
            $this->loadCompanyData($company);
        }

        // Déterminer l'étape courante
        $this->currentStep = $this->progress->current_step;
    }

    /**
     * Charger les données existantes de l'entreprise
     */
    protected function loadCompanyData(Company $company): void
    {
        // Step 1
        $this->companyName = $company->name ?? '';
        $this->companyLegalName = $company->legal_name ?? '';
        $this->companyEmail = $company->email ?? '';
        $this->companyPhone = $company->phone ?? '';
        $this->companyMobile = $company->mobile ?? '';

        // Step 2
        $this->nif = $company->nif ?? '';
        $this->rccm = $company->rccm ?? '';
        $this->ice = $company->ice ?? '';
        $this->ifu = $company->ifu ?? '';
        $this->taxRegime = $company->tax_regime ?? 'reel_simplifie';

        // Step 3
        $this->address = $company->address ?? '';
        $this->city = $company->city ?? 'Abidjan';
        $this->postalCode = $company->postal_code ?? '';
        $this->country = $company->country ?? 'Côte d\'Ivoire';
        $this->existingLogo = $company->logo;

        // Step 4
        $this->invoicePrefix = $company->invoice_prefix ?? 'FAC';
        $this->quotePrefix = $company->quote_prefix ?? 'DEV';
        $this->defaultTaxRate = $company->default_tax_rate ?? 18.00;
        $this->currency = $company->currency ?? 'XOF';
        $this->timezone = $company->timezone ?? 'Africa/Abidjan';
    }

    /**
     * STEP 1: Informations de base
     */
    public function submitStep1(): void
    {
        $this->showValidationErrors = true;

        $validated = $this->validate([
            'companyName' => ['required', 'string', 'min:2', 'max:255'],
            'companyLegalName' => ['nullable', 'string', 'max:255'],
            'companyEmail' => [
                'required',
                'email',
                'max:255',
                Rule::unique('companies', 'email')
                    ->ignore(Auth::user()->tenant->company?->id)
            ],
            'companyPhone' => ['required', 'string', 'regex:/^[+]?[0-9\s\-\(\)]+$/', 'min:8', 'max:20'],
            'companyMobile' => ['nullable', 'string', 'regex:/^[+]?[0-9\s\-\(\)]+$/', 'max:20'],
        ], [
            'companyName.required' => 'Le nom de l\'entreprise est obligatoire',
            'companyName.min' => 'Le nom doit contenir au moins 2 caractères',
            'companyEmail.required' => 'L\'email est obligatoire',
            'companyEmail.email' => 'L\'email doit être valide',
            'companyEmail.unique' => 'Cet email est déjà utilisé',
            'companyPhone.required' => 'Le téléphone est obligatoire',
            'companyPhone.regex' => 'Le numéro de téléphone n\'est pas valide',
            'companyMobile.regex' => 'Le numéro mobile n\'est pas valide',
        ]);

        $this->saveCompanyData();
        $this->markStepCompleted(1);
        $this->nextStep();

        $this->dispatch('step-completed', step: 1);
        $this->showValidationErrors = false;
    }

    /**
     * STEP 2: Identifiants fiscaux CI
     */
    public function submitStep2(): void
    {
        $this->showValidationErrors = true;

        $validated = $this->validate([
            'nif' => ['nullable', 'string', 'max:50', 'regex:/^[A-Z0-9]+$/i'],
            'rccm' => ['nullable', 'string', 'max:50'],
            'ice' => ['nullable', 'string', 'max:50', 'regex:/^[0-9]+$/'],
            'ifu' => ['nullable', 'string', 'max:50', 'regex:/^[0-9A-Z]+$/i'],
            'taxRegime' => ['required', 'in:reel_simplifie,reel_normal'],
        ], [
            'nif.regex' => 'Le NIF ne doit contenir que des lettres et chiffres',
            'ice.regex' => 'L\'ICE ne doit contenir que des chiffres',
            'ifu.regex' => 'L\'IFU ne doit contenir que des lettres et chiffres',
            'taxRegime.required' => 'Le régime fiscal est obligatoire',
            'taxRegime.in' => 'Régime fiscal invalide',
        ]);

        $this->saveCompanyData();
        $this->markStepCompleted(2);
        $this->nextStep();

        $this->dispatch('step-completed', step: 2);
        $this->showValidationErrors = false;
    }

    /**
     * STEP 3: Adresse & Logo
     */
    public function submitStep3(): void
    {
        $this->showValidationErrors = true;

        $rules = [
            'address' => ['required', 'string', 'min:10', 'max:500'],
            'city' => ['required', 'string', 'max:100'],
            'postalCode' => ['nullable', 'string', 'max:20'],
            'country' => ['required', 'string', 'max:100'],
        ];

        // Validation du logo uniquement si changé
        if ($this->logoChanged && $this->logo) {
            $rules['logo'] = ['nullable', 'image', 'max:2048', 'mimes:jpeg,jpg,png,webp'];
        }

        $validated = $this->validate($rules, [
            'address.required' => 'L\'adresse est obligatoire',
            'address.min' => 'L\'adresse doit contenir au moins 10 caractères',
            'city.required' => 'La ville est obligatoire',
            'country.required' => 'Le pays est obligatoire',
            'logo.image' => 'Le fichier doit être une image',
            'logo.max' => 'L\'image ne peut dépasser 2 MB',
            'logo.mimes' => 'Formats acceptés : JPEG, JPG, PNG, WEBP',
        ]);

        // Upload logo si changé
        if ($this->logoChanged && $this->logo instanceof TemporaryUploadedFile) {
            $this->uploadLogo();
        }

        $this->saveCompanyData();
        $this->markStepCompleted(3);
        $this->nextStep();

        $this->dispatch('step-completed', step: 3);
        $this->showValidationErrors = false;
    }

    /**
     * STEP 4: Préférences facturation (Final)
     */
    public function submitStep4(): void
    {
        $this->showValidationErrors = true;

        $validated = $this->validate([
            'invoicePrefix' => ['required', 'string', 'alpha_num', 'max:10'],
            'quotePrefix' => ['required', 'string', 'alpha_num', 'max:10'],
            'defaultTaxRate' => ['required', 'numeric', 'min:0', 'max:100'],
            'currency' => ['required', 'string', 'in:XOF,EUR,USD'],
            'timezone' => ['required', 'string'],
        ], [
            'invoicePrefix.required' => 'Le préfixe de facture est obligatoire',
            'invoicePrefix.alpha_num' => 'Le préfixe ne doit contenir que des lettres et chiffres',
            'quotePrefix.required' => 'Le préfixe de devis est obligatoire',
            'quotePrefix.alpha_num' => 'Le préfixe ne doit contenir que des lettres et chiffres',
            'defaultTaxRate.required' => 'Le taux de TVA est obligatoire',
            'defaultTaxRate.numeric' => 'Le taux doit être un nombre',
            'defaultTaxRate.min' => 'Le taux ne peut être négatif',
            'defaultTaxRate.max' => 'Le taux ne peut dépasser 100%',
            'currency.required' => 'La devise est obligatoire',
            'currency.in' => 'Devise non supportée',
        ]);

        $this->saveCompanyData();
        $this->completeOnboarding();

        $this->dispatch('onboarding-completed');
        $this->showValidationErrors = false;
    }

    /**
     * Sauvegarder les données entreprise
     */
    protected function saveCompanyData(): void
    {
        $tenant = Auth::user()->tenant;

        DB::transaction(function () use ($tenant) {
            Company::updateOrCreate(
                ['tenant_id' => $tenant->id],
                [
                    // Step 1
                    'name' => $this->companyName,
                    'legal_name' => $this->companyLegalName ?: $this->companyName,
                    'email' => $this->companyEmail,
                    'phone' => $this->companyPhone,
                    'mobile' => $this->companyMobile,

                    // Step 2
                    'nif' => $this->nif ?: null,
                    'rccm' => $this->rccm ?: null,
                    'ice' => $this->ice ?: null,
                    'ifu' => $this->ifu ?: null,
                    'tax_regime' => $this->taxRegime,

                    // Step 3
                    'address' => $this->address,
                    'city' => $this->city,
                    'postal_code' => $this->postalCode ?: null,
                    'country' => $this->country,
                    'logo' => $this->existingLogo,

                    // Step 4
                    'invoice_prefix' => strtoupper($this->invoicePrefix),
                    'quote_prefix' => strtoupper($this->quotePrefix),
                    'default_tax_rate' => $this->defaultTaxRate,
                    'currency' => $this->currency,
                    'timezone' => $this->timezone,
                ]
            );
        });
    }

    /**
     * Upload du logo avec optimisation
     */
    protected function uploadLogo(): void
    {
        try {
            // Générer nom unique
            $filename = 'logos/' . uniqid('logo_', true) . '.' . $this->logo->extension();

            // Stocker le fichier
            $path = $this->logo->storeAs('public', $filename);

            // Supprimer ancien logo si existe
            if ($this->existingLogo && Storage::disk('public')->exists($this->existingLogo)) {
                Storage::disk('public')->delete($this->existingLogo);
            }

            // Mettre à jour le chemin
            $this->existingLogo = $filename;

            // TODO: Optimiser l'image avec Intervention Image
            // $img = Image::make(storage_path('app/public/' . $filename));
            // $img->fit(500, 500)->save();

        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors du téléchargement du logo : ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Marquer une étape comme complétée
     */
    protected function markStepCompleted(int $step): void
    {
        $fields = [
            1 => 'step_company_info',
            2 => 'step_company_details',
            3 => 'step_user_profile',
            4 => 'step_subscription',
        ];

        if (isset($fields[$step])) {
            $this->progress->update([
                $fields[$step] => true,
                'current_step' => min($step + 1, self::TOTAL_STEPS),
            ]);
        }
    }

    /**
     * Marquer onboarding comme terminé
     */
    protected function completeOnboarding(): void
    {
        $this->progress->update([
            'completed' => true,
            'completed_at' => now(),
            'current_step' => self::TOTAL_STEPS,
        ]);

        session()->flash('success', '🎉 Félicitations ! Votre entreprise est maintenant configurée.');

        // Rediriger vers dashboard
        $this->redirect(route('dashboard'), navigate: true);
    }

    /**
     * Navigation : Étape suivante
     */
    public function nextStep(): void
    {
        if ($this->currentStep < self::TOTAL_STEPS) {
            $this->currentStep++;
            $this->showValidationErrors = false;
        }
    }

    /**
     * Navigation : Étape précédente
     */
    public function previousStep(): void
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
            $this->showValidationErrors = false;
        }
    }

    /**
     * Navigation : Aller à une étape spécifique
     */
    public function goToStep(int $step): void
    {
        // Ne peut aller qu'aux étapes déjà complétées ou suivante
        if ($step >= 1 && $step <= $this->progress->current_step && $step <= self::TOTAL_STEPS) {
            $this->currentStep = $step;
            $this->showValidationErrors = false;
        }
    }

    /**
     * Passer une étape (sauf étape 1 et 4)
     */
    public function skipStep(): void
    {
        // Ne peut pas passer l'étape 1 (obligatoire) ni l'étape 4 (finale)
        if ($this->currentStep === 1 || $this->currentStep === 4) {
            return;
        }

        $this->saveCompanyData();
        $this->markStepCompleted($this->currentStep);
        $this->nextStep();

        session()->flash('info', 'Étape passée. Vous pourrez la compléter plus tard dans les paramètres.');
    }

    /**
     * Listener : Logo changé
     */
    #[On('logo-changed')]
    public function onLogoChanged(): void
    {
        $this->logoChanged = true;
    }

    /**
     * Listener : Logo supprimé
     */
    public function removeLogo(): void
    {
        if ($this->existingLogo && Storage::disk('public')->exists($this->existingLogo)) {
            Storage::disk('public')->delete($this->existingLogo);
        }

        $this->existingLogo = null;
        $this->logo = null;
        $this->logoChanged = true;

        session()->flash('info', 'Logo supprimé avec succès.');
    }

    /**
     * Computed : Pourcentage de progression
     */
    #[Computed]
    public function progressPercentage(): int
    {
        return (int) (($this->currentStep / self::TOTAL_STEPS) * 100);
    }

    /**
     * Computed : Nombre d'étapes complétées
     */
    #[Computed]
    public function completedSteps(): int
    {
        return collect([
            $this->progress->step_company_info,
            $this->progress->step_company_details,
            $this->progress->step_user_profile,
            $this->progress->step_subscription,
        ])->filter()->count();
    }

    /**
     * Computed : Peut avancer à l'étape suivante
     */
    #[Computed]
    public function canProceed(): bool
    {
        return $this->currentStep < self::TOTAL_STEPS;
    }

    /**
     * Computed : Peut revenir en arrière
     */
    #[Computed]
    public function canGoBack(): bool
    {
        return $this->currentStep > 1;
    }

    /**
     * Computed : Titre de l'étape courante
     */
    #[Computed]
    public function currentStepTitle(): string
    {
        return match ($this->currentStep) {
            1 => 'Informations de l\'entreprise',
            2 => 'Identifiants fiscaux (Côte d\'Ivoire)',
            3 => 'Localisation et identité visuelle',
            4 => 'Paramètres de facturation',
            default => 'Configuration',
        };
    }

    /**
     * Computed : Description de l'étape courante
     */
    #[Computed]
    public function currentStepDescription(): string
    {
        return match ($this->currentStep) {
            1 => 'Commencez par renseigner les informations de base de votre entreprise.',
            2 => 'Ces informations sont optionnelles mais recommandées pour la conformité DGI.',
            3 => 'Indiquez l\'adresse de votre entreprise et personnalisez avec votre logo.',
            4 => 'Configurez vos préférences pour la facturation et vous êtes prêt !',
            default => '',
        };
    }

    /**
     * Computed : Icône de l'étape courante
     */
    #[Computed]
    public function currentStepIcon(): string
    {
        return match ($this->currentStep) {
            1 => '📋',
            2 => '🏛️',
            3 => '📍',
            4 => '🧾',
            default => '⚙️',
        };
    }

    /**
     * Render
     */
    public function render()
    {
        return view('livewire.onboarding.onboarding-wizard')
            ->layout('layouts.auth');
    }
}
