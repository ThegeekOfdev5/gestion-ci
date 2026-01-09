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
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

#[Layout('components.layouts.guest')]
class OnboardingWizard extends Component
{
    use WithFileUploads;

    // Configuration des étapes
    public const STEPS = [
        1 => ['key' => 'welcome', 'title' => 'Bienvenue', 'icon' => '👋'],
        2 => ['key' => 'company_profile', 'title' => 'Profil Entreprise', 'icon' => '🏢'],
        3 => ['key' => 'fiscal_identity', 'title' => 'Identité Fiscale', 'icon' => '📊'],
        4 => ['key' => 'financial_setup', 'title' => 'Configuration Financière', 'icon' => '💰'],
        5 => ['key' => 'modules', 'title' => 'Modules ERP', 'icon' => '⚙️'],
    ];

    public int $currentStep = 1;
    public int $totalSteps = 5;
    public ?OnboardingProgress $progress = null;

    // Step 1: Welcome
    public bool $acceptedTerms = false;

    // Step 2: Company Profile
    public string $companyName = '';
    public string $companyLegalName = '';
    public string $companyEmail = '';
    public string $companyPhone = '';
    public string $companyMobile = '';
    public string $businessSector = '';
    public $logo;
    public ?string $existingLogo = null;
    public bool $logoChanged = false;
    public string $address = '';
    public string $city = 'Abidjan';
    public string $postalCode = '';
    public string $country = 'Côte d\'Ivoire';

    // Step 3: Fiscal Identity
    public string $nif = '';
    public string $rccm = '';
    public string $ice = '';
    public string $ifu = '';
    public string $taxRegime = 'reel_simplifie';
    public ?string $taxCardNumber = null;
    public ?string $taxOffice = null;

    // Step 4: Financial Setup
    public string $invoicePrefix = 'FAC';
    public string $quotePrefix = 'DEV';
    public string $defaultTaxRate = '18';
    public ?string $customTaxRate = null;
    public string $currency = 'XOF';
    public string $timezone = 'Africa/Abidjan';
    public string $paymentTerms = '30j';
    public string $fiscalYearStart = '01-01';
    public bool $vatEnabled = true;

    // Step 5: Modules & Plan
    public array $selectedModules = ['facturation', 'comptabilité'];
    public string $selectedPlan = 'professionnel';
    public int $userCount = 2;
    public bool $newsletterSubscription = true;
    public bool $demoRequested = false;

    // UI State
    public bool $showValidationErrors = false;
    public bool $isLoading = false;

    /**
     * Mount the component
     */
    public function mount(): void
    {
        $tenant = Auth::user()->tenant;

        // Load or create onboarding progress
        $this->progress = OnboardingProgress::firstOrCreate(
            ['tenant_id' => $tenant->id],
            [
                'current_step' => 1,
                'completed' => false,
            ]
        );

        // If onboarding already completed, redirect to dashboard
        if ($this->progress->completed) {
            $this->redirect(route('tenant.dashboard'), navigate: true);
            return;
        }

        // Load existing company data
        if ($company = $tenant->company) {
            $this->loadCompanyData($company);
        }

        // Set current step from progress
        $this->currentStep = $this->progress->current_step;
    }

    /**
     * Load existing company data
     */
    protected function loadCompanyData(Company $company): void
    {
        // Company Profile
        $this->companyName = $company->name ?? '';
        $this->companyLegalName = $company->legal_name ?? '';
        $this->companyEmail = $company->email ?? '';
        $this->companyPhone = $company->phone ?? '';
        $this->companyMobile = $company->mobile ?? '';
        $this->businessSector = $company->business_sector ?? '';
        $this->existingLogo = $company->logo;
        $this->address = $company->address ?? '';
        $this->city = $company->city ?? 'Abidjan';
        $this->postalCode = $company->postal_code ?? '';
        $this->country = $company->country ?? 'Côte d\'Ivoire';

        // Fiscal Identity
        $this->nif = $company->nif ?? '';
        $this->rccm = $company->rccm ?? '';
        $this->ice = $company->ice ?? '';
        $this->ifu = $company->ifu ?? '';
        $this->taxRegime = $company->tax_regime ?? 'reel_simplifie';
        $this->taxCardNumber = $company->tax_card_number ?? null;
        $this->taxOffice = $company->tax_office ?? null;

        // Financial Setup
        $this->invoicePrefix = $company->invoice_prefix ?? 'FAC';
        $this->quotePrefix = $company->quote_prefix ?? 'DEV';
        $this->defaultTaxRate = (string) ($company->default_tax_rate ?? '18');
        $this->currency = $company->currency ?? 'XOF';
        $this->timezone = $company->timezone ?? 'Africa/Abidjan';
        $this->paymentTerms = $company->payment_terms ?? '30j';
        $this->fiscalYearStart = $company->fiscal_year_start ?? '01-01';
        $this->vatEnabled = $company->vat_enabled ?? true;

        // Modules (we'll load from config later)
    }

    /**
     * STEP 1: Welcome
     */
    public function submitStep1(): void
    {
        $this->validate([
            'acceptedTerms' => ['required', 'accepted'],
        ], [
            'acceptedTerms.required' => 'Vous devez accepter les conditions d\'utilisation',
            'acceptedTerms.accepted' => 'Vous devez accepter les conditions d\'utilisation',
        ]);

        $this->saveProgress();
        $this->markStepCompleted(1);
        $this->nextStep();

        $this->dispatch('step-completed', step: 1);
    }

    /**
     * STEP 2: Company Profile
     */
    public function submitStep2(): void
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
            'businessSector' => ['required', 'string', 'in:services,commerce,industrie,bâtiment,informatique,santé,transport,autre'],
            'address' => ['required', 'string', 'min:10', 'max:500'],
            'city' => ['required', 'string', 'max:100'],
            'postalCode' => ['nullable', 'string', 'max:20'],
            'country' => ['required', 'string', 'max:100'],
        ], [
            'companyName.required' => 'Le nom de l\'entreprise est obligatoire',
            'companyEmail.required' => 'L\'email est obligatoire',
            'companyEmail.unique' => 'Cet email est déjà utilisé',
            'companyPhone.required' => 'Le téléphone est obligatoire',
            'businessSector.required' => 'Le secteur d\'activité est obligatoire',
            'address.required' => 'L\'adresse est obligatoire',
            'city.required' => 'La ville est obligatoire',
            'country.required' => 'Le pays est obligatoire',
        ]);

        // Upload logo if changed
        if ($this->logoChanged && $this->logo instanceof TemporaryUploadedFile) {
            $this->uploadLogo();
        }

        $this->saveCompanyData();
        $this->markStepCompleted(2);
        $this->nextStep();

        $this->dispatch('step-completed', step: 2);
        $this->showValidationErrors = false;
    }

    /**
     * STEP 3: Fiscal Identity
     */
    public function submitStep3(): void
    {
        $this->showValidationErrors = true;

        $validated = $this->validate([
            'nif' => ['nullable', 'string', 'max:50', 'regex:/^[A-Z0-9]+$/i'],
            'rccm' => ['nullable', 'string', 'max:50'],
            'ice' => ['nullable', 'string', 'max:50', 'regex:/^[0-9]+$/'],
            'ifu' => ['nullable', 'string', 'max:50', 'regex:/^[0-9A-Z]+$/i'],
            'taxRegime' => ['required', 'in:reel_simplifie,reel_normal'],
            'taxCardNumber' => ['nullable', 'string', 'max:50'],
            'taxOffice' => ['nullable', 'string', 'max:100'],
        ], [
            'nif.regex' => 'Le NIF ne doit contenir que des lettres et chiffres',
            'ice.regex' => 'L\'ICE ne doit contenir que des chiffres',
            'ifu.regex' => 'L\'IFU ne doit contenir que des lettres et chiffres',
            'taxRegime.required' => 'Le régime fiscal est obligatoire',
            'taxRegime.in' => 'Régime fiscal invalide',
        ]);

        $this->saveCompanyData();
        $this->markStepCompleted(3);
        $this->nextStep();

        $this->dispatch('step-completed', step: 3);
        $this->showValidationErrors = false;
    }

    /**
     * STEP 4: Financial Setup
     */
    public function submitStep4(): void
    {
        $this->showValidationErrors = true;

        $rules = [
            'invoicePrefix' => ['required', 'string', 'alpha_num', 'max:10'],
            'quotePrefix' => ['required', 'string', 'alpha_num', 'max:10'],
            'currency' => ['required', 'string', 'in:XOF,EUR,USD'],
            'timezone' => ['required', 'string'],
            'paymentTerms' => ['required', 'string', 'in:immédiat,7j,30j,60j'],
            'fiscalYearStart' => ['required', 'string', 'regex:/^\d{2}-\d{2}$/'],
            'vatEnabled' => ['required', 'boolean'],
        ];

        // Validate tax rate based on selection
        if ($this->defaultTaxRate === 'custom') {
            $rules['customTaxRate'] = ['required', 'numeric', 'min:0', 'max:100'];
        } else {
            $rules['defaultTaxRate'] = ['required', 'string', 'in:0,10,18'];
        }

        $validated = $this->validate($rules, [
            'invoicePrefix.required' => 'Le préfixe de facture est obligatoire',
            'quotePrefix.required' => 'Le préfixe de devis est obligatoire',
            'currency.required' => 'La devise est obligatoire',
            'paymentTerms.required' => 'Les conditions de paiement sont obligatoires',
            'fiscalYearStart.required' => 'Le début de l\'année fiscale est obligatoire',
            'fiscalYearStart.regex' => 'Format invalide (JJ-MM requis)',
            'customTaxRate.required' => 'Le taux personnalisé est requis',
        ]);

        $this->saveCompanyData();
        $this->markStepCompleted(4);
        $this->nextStep();

        $this->dispatch('step-completed', step: 4);
        $this->showValidationErrors = false;
    }

    /**
     * STEP 5: Modules & Plan (Final)
     */
    public function submitStep5(): void
    {
        $this->showValidationErrors = true;

        $validated = $this->validate([
            'selectedModules' => ['required', 'array', 'min:2'],
            'selectedPlan' => ['required', 'string', 'in:essentiel,professionnel,entreprise'],
            'userCount' => ['required', 'integer', 'min:1', 'max:100'],
            'newsletterSubscription' => ['boolean'],
            'demoRequested' => ['boolean'],
        ], [
            'selectedModules.required' => 'Veuillez sélectionner au moins les modules obligatoires',
            'selectedModules.min' => 'Les modules de base sont obligatoires',
            'selectedPlan.required' => 'Veuillez sélectionner un plan',
            'userCount.required' => 'Le nombre d\'utilisateurs est requis',
        ]);

        // Save company data with modules
        $this->saveCompanyData();

        // Save module selection to user preferences or company config
        $this->saveModuleSelection();

        // Complete onboarding
        $this->completeOnboarding();

        $this->dispatch('onboarding-completed');
        $this->showValidationErrors = false;
    }

    /**
     * Save company data
     */
    protected function saveCompanyData(): void
    {
        $tenant = Auth::user()->tenant;

        DB::transaction(function () use ($tenant) {
            Company::updateOrCreate(
                ['tenant_id' => $tenant->id],
                [
                    // Company Profile
                    'name' => $this->companyName,
                    'legal_name' => $this->companyLegalName ?: $this->companyName,
                    'email' => $this->companyEmail,
                    'phone' => $this->companyPhone,
                    'mobile' => $this->companyMobile,
                    'business_sector' => $this->businessSector,
                    'logo' => $this->existingLogo,
                    'address' => $this->address,
                    'city' => $this->city,
                    'postal_code' => $this->postalCode ?: null,
                    'country' => $this->country,

                    // Fiscal Identity
                    'nif' => $this->nif ?: null,
                    'rccm' => $this->rccm ?: null,
                    'ice' => $this->ice ?: null,
                    'ifu' => $this->ifu ?: null,
                    'tax_regime' => $this->taxRegime,
                    'tax_card_number' => $this->taxCardNumber ?: null,
                    'tax_office' => $this->taxOffice ?: null,

                    // Financial Setup
                    'invoice_prefix' => strtoupper($this->invoicePrefix),
                    'quote_prefix' => strtoupper($this->quotePrefix),
                    'default_tax_rate' => $this->effectiveTaxRate,
                    'currency' => $this->currency,
                    'timezone' => $this->timezone,
                    'payment_terms' => $this->paymentTerms,
                    'fiscal_year_start' => $this->fiscalYearStart,
                    'vat_enabled' => $this->vatEnabled,
                ]
            );
        });
    }

    /**
     * Upload logo
     */
    protected function uploadLogo(): void
    {
        try {
            // Generate unique filename
            $filename = 'logos/' . uniqid('logo_', true) . '.' . $this->logo->extension();

            // Store file
            $path = $this->logo->storeAs('logos', $filename, 'public');

            // Delete old logo if exists
            if ($this->existingLogo && Storage::disk('public')->exists($this->existingLogo)) {
                Storage::disk('public')->delete($this->existingLogo);
            }

            // Update path
            $this->existingLogo = $filename;

        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors du téléchargement du logo : ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Save module selection
     */
    protected function saveModuleSelection(): void
    {
        $tenant = Auth::user()->tenant;

        // Save to company config or user preferences
        $company = $tenant->company;
        if ($company) {
            $company->update([
                'enabled_modules' => $this->selectedModules,
                'selected_plan' => $this->selectedPlan,
                'user_count' => $this->userCount,
            ]);
        }

        // Optionally create subscription record
        // Subscription::create([...]);
    }

    /**
     * Mark step as completed
     */
    protected function markStepCompleted(int $step): void
    {
        $fieldMap = [
            1 => 'step_welcome',
            2 => 'step_company_profile',
            3 => 'step_fiscal_identity',
            4 => 'step_financial_setup',
            5 => 'step_modules',
        ];

        if (isset($fieldMap[$step])) {
            $this->progress->update([
                $fieldMap[$step] => true,
                'current_step' => min($step + 1, $this->totalSteps),
            ]);
        }
    }

    /**
     * Complete onboarding
     */
    protected function completeOnboarding(): void
    {
        // Mark step 5 as completed
        $this->markStepCompleted(5);

        // Mark onboarding as completed
        $this->progress->update([
            'completed' => true,
            'completed_at' => now(),
        ]);

        // Send welcome email
        // Mail::to(Auth::user())->send(new WelcomeEmail());

        // Create initial subscription
        // $this->createInitialSubscription();

        session()->flash('success', '🎉 Félicitations ! Votre espace ERP OHADA Cloud est maintenant prêt.');

        // Redirect to dashboard
        $this->redirect(route('tenant.dashboard'), navigate: true);
    }

    /**
     * Save progress (auto-save)
     */
    protected function saveProgress(): void
    {
        try {
            $this->progress->touch();
        } catch (\Exception $e) {
            // Silently fail for auto-save
        }
    }

    /**
     * Navigation: Next step
     */
    public function nextStep(): void
    {
        if ($this->currentStep < $this->totalSteps) {
            $this->currentStep++;
            $this->showValidationErrors = false;
        }
    }

    /**
     * Navigation: Previous step
     */
    public function previousStep(): void
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
            $this->showValidationErrors = false;
        }
    }

    /**
     * Navigation: Go to specific step
     */
    public function goToStep(int $step): void
    {
        // Can only go to completed steps or next step
        if ($step >= 1 && $step <= $this->progress->current_step && $step <= $this->totalSteps) {
            $this->currentStep = $step;
            $this->showValidationErrors = false;
        }
    }

    /**
     * Skip step (except steps 1 and 5)
     */
    public function skipStep(): void
    {
        if ($this->currentStep === 1 || $this->currentStep === $this->totalSteps) {
            return;
        }

        $this->saveCompanyData();
        $this->markStepCompleted($this->currentStep);
        $this->nextStep();

        session()->flash('info', 'Étape passée. Vous pourrez la compléter plus tard dans les paramètres.');
    }

    /**
     * Toggle module selection
     */
    public function toggleModule(string $module): void
    {
        if (in_array($module, $this->selectedModules)) {
            $this->selectedModules = array_filter($this->selectedModules, fn($m) => $m !== $module);
        } else {
            $this->selectedModules[] = $module;
        }
    }

    /**
     * Remove logo
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
     * Logo changed listener
     */
    #[On('logo-changed')]
    public function onLogoChanged(): void
    {
        $this->logoChanged = true;
    }

    /**
     * Computed: Effective tax rate
     */
    #[Computed]
    public function effectiveTaxRate(): float
    {
        if ($this->defaultTaxRate === 'custom' && $this->customTaxRate) {
            return (float) $this->customTaxRate;
        }
        return (float) $this->defaultTaxRate;
    }

    /**
     * Computed: Monthly estimate
     */
    #[Computed]
    public function monthlyEstimate(): int
    {
        $basePrice = match($this->selectedPlan) {
            'essentiel' => 50000,
            'professionnel' => 75000,
            'entreprise' => 120000,
            default => 50000,
        };

        // Add extra for additional users
        $extraUsers = max(0, $this->userCount - match($this->selectedPlan) {
            'essentiel' => 2,
            'professionnel' => 5,
            'entreprise' => 10,
            default => 2,
        });

        $userCost = $extraUsers * 5000; // 5.000 XOF per extra user

        // Add cost for extra modules (excluding included ones)
        $includedModules = match($this->selectedPlan) {
            'essentiel' => ['facturation', 'comptabilité'],
            'professionnel' => ['facturation', 'comptabilité', 'crm', 'inventaire'],
            'entreprise' => ['facturation', 'comptabilité', 'crm', 'inventaire', 'paie', 'projets'],
            default => ['facturation', 'comptabilité'],
        };

        $extraModules = array_filter($this->selectedModules,
            fn($module) => !in_array($module, $includedModules));

        $moduleCost = count($extraModules) * 10000; // 10.000 XOF per extra module

        return $basePrice + $userCost + $moduleCost;
    }

    /**
     * Computed: Progress percentage
     */
    #[Computed]
    public function progressPercentage(): int
    {
        return (int) (($this->currentStep / $this->totalSteps) * 100);
    }

    /**
     * Computed: Completed steps count
     */
    #[Computed]
    public function completedSteps(): int
    {
        if (!$this->progress) {
            return 0;
        }

        return collect([
            $this->progress->step_welcome ?? false,
            $this->progress->step_company_profile ?? false,
            $this->progress->step_fiscal_identity ?? false,
            $this->progress->step_financial_setup ?? false,
            $this->progress->step_modules ?? false,
        ])->filter()->count();
    }

    /**
     * Computed: Can go back
     */
    #[Computed]
    public function canGoBack(): bool
    {
        return $this->currentStep > 1;
    }

    /**
     * Computed: Current step title
     */
    #[Computed]
    public function currentStepTitle(): string
    {
        return match ($this->currentStep) {
            1 => 'Bienvenue sur ERP OHADA Cloud',
            2 => 'Profil de votre entreprise',
            3 => 'Identité fiscale (Conformité DGI)',
            4 => 'Configuration financière',
            5 => 'Modules & Plan tarifaire',
            default => 'Configuration',
        };
    }

    /**
     * Computed: Current step description
     */
    #[Computed]
    public function currentStepDescription(): string
    {
        return match ($this->currentStep) {
            1 => 'Configurez votre espace professionnel conforme aux normes OHADA en 5 étapes simples.',
            2 => 'Décrivez votre entreprise pour personnaliser votre expérience ERP.',
            3 => 'Renseignez vos identifiants fiscaux pour la conformité avec l\'administration.',
            4 => 'Configurez vos paramètres financiers et vos préférences de facturation.',
            5 => 'Choisissez les modules adaptés à vos besoins et votre plan tarifaire.',
            default => '',
        };
    }

    /**
     * Computed: Current step icon
     */
    #[Computed]
    public function currentStepIcon(): string
    {
        return match ($this->currentStep) {
            1 => '👋',
            2 => '🏢',
            3 => '📊',
            4 => '💰',
            5 => '⚙️',
            default => '⚙️',
        };
    }

    /**
     * Computed: Step help text
     */
    #[Computed]
    public function stepHelp(): string
    {
        return match($this->currentStep) {
            1 => 'Prenez 7 minutes pour configurer votre espace professionnel conforme OHADA.',
            2 => 'Ces informations apparaîtront sur vos factures et documents officiels.',
            3 => 'Les identifiants fiscaux sont obligatoires pour la conformité avec la DGI.',
            4 => 'Configurez vos paramètres financiers selon votre activité.',
            5 => 'Choisissez les modules adaptés à vos besoins. Vous pourrez en ajouter plus tard.',
            default => '',
        };
    }

    /**
     * Computed: Sector recommendations
     */
    #[Computed]
    public function sectorRecommendations(): array
    {
        return match($this->businessSector) {
            'services' => [
                'Le module CRM est recommandé pour suivre vos clients',
                'TVA à 18% pour les prestations de services',
                'Conditions de paiement à 30 jours standards',
            ],
            'commerce' => [
                'Le module Inventaire est essentiel pour gérer vos stocks',
                'TVA à 18% pour les ventes de marchandises',
                'Préfixe FAC recommandé pour les factures',
            ],
            'industrie' => [
                'Module Projets pour le suivi de production',
                'TVA à 18% pour les produits manufacturés',
                'Gestion des stocks avancée recommandée',
            ],
            'bâtiment' => [
                'Module Projets pour le suivi des chantiers',
                'TVA à 10% pour certaines prestations BTP',
                'Conditions de paiement échelonnées',
            ],
            'informatique' => [
                'Module CRM pour la gestion des clients',
                'TVA à 18% pour les services IT',
                'Support prioritaire recommandé',
            ],
            'santé' => [
                'Module Inventaire pour la gestion des médicaments',
                'TVA à 10% pour certains produits pharmaceutiques',
                'Gestion des fournisseurs essentielle',
            ],
            'transport' => [
                'Module Projets pour le suivi des livraisons',
                'TVA à 18% pour les services de transport',
                'Gestion des véhicules recommandée',
            ],
            default => [],
        };
    }

    /**
     * Computed: Module details
     */
    #[Computed]
    public function moduleDetails(): array
    {
        return [
            'facturation' => [
                'icon' => '🧾',
                'title' => 'Facturation',
                'description' => 'Factures, devis, relances, avoirs',
                'required' => true,
                'included' => true,
            ],
            'comptabilité' => [
                'icon' => '📒',
                'title' => 'Comptabilité OHADA',
                'description' => 'Plan comptable, journaux, bilan, déclarations',
                'required' => true,
                'included' => true,
            ],
            'crm' => [
                'icon' => '👥',
                'title' => 'CRM Clients',
                'description' => 'Gestion de la relation client, suivi commercial',
                'required' => false,
                'included' => in_array($this->selectedPlan, ['professionnel', 'entreprise']),
            ],
            'inventaire' => [
                'icon' => '📦',
                'title' => 'Inventaire',
                'description' => 'Gestion des stocks, approvisionnements, inventaire',
                'required' => false,
                'included' => in_array($this->selectedPlan, ['professionnel', 'entreprise']),
            ],
            'paie' => [
                'icon' => '💰',
                'title' => 'Paie & RH',
                'description' => 'Bulletins de paie, congés, contrats, déclarations sociales',
                'required' => false,
                'included' => $this->selectedPlan === 'entreprise',
            ],
            'projets' => [
                'icon' => '📋',
                'title' => 'Gestion de projets',
                'description' => 'Planning, tâches, équipes, budgets',
                'required' => false,
                'included' => $this->selectedPlan === 'entreprise',
            ],
        ];
    }

    /**
     * Render
     */
    public function render()
    {
        return view('livewire.onboarding.onboarding-wizard');
    }
}
