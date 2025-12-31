<div>
    {{-- resources/views/livewire/onboarding/onboarding-wizard.blade.php --}}

    <div class="min-h-screen bg-gradient-to-br from-orange-50 to-blue-50 py-8 px-4">
        <div class="max-w-4xl mx-auto">

            {{-- Header --}}
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-800 mb-2">
                    🚀 Configuration de votre entreprise
                </h1>
                <p class="text-gray-600">
                    Complétez ces étapes pour démarrer avec ERP OHADA Cloud
                </p>
            </div>

            {{-- Barre de progression --}}
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center space-x-2">
                        <span class="text-sm font-medium text-gray-700">
                            Étape {{ $currentStep }} sur {{ self::TOTAL_STEPS }}
                        </span>
                    </div>
                    <span class="text-sm font-medium text-orange-600">
                        {{ $this->progressPercentage }}%
                    </span>
                </div>

                {{-- Progress bar --}}
                <div class="w-full bg-gray-200 rounded-full h-2.5 mb-6">
                    <div class="bg-orange-500 h-2.5 rounded-full transition-all duration-300"
                        style="width: {{ $this->progressPercentage }}%" wire:loading.class="animate-pulse">
                    </div>
                </div>

                {{-- Steps indicators --}}
                <div class="grid grid-cols-4 gap-2">
                    @foreach (range(1, self::TOTAL_STEPS) as $step)
                        <button type="button" wire:click="goToStep({{ $step }})" @class([
                            'flex flex-col items-center',
                            'cursor-pointer hover:opacity-80' => $step <= $progress->current_step,
                            'cursor-not-allowed opacity-50' => $step > $progress->current_step,
                        ])
                            @if ($step > $progress->current_step) disabled @endif>

                            <div @class([
                                'w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm mb-1 transition-all',
                                'bg-green-500 text-white' =>
                                    $step < $currentStep &&
                                    $progress->{'step_' .
                                        match ($step) {
                                            1 => 'company_info',
                                            2 => 'company_details',
                                            3 => 'user_profile',
                                            4 => 'subscription',
                                        }},
                                'bg-orange-500 text-white ring-4 ring-orange-200' => $step === $currentStep,
                                'bg-gray-300 text-gray-600' => $step > $currentStep,
                            ])>
                                @if (
                                    $step < $currentStep &&
                                        $progress->{'step_' .
                                            match ($step) {
                                                1 => 'company_info',
                                                2 => 'company_details',
                                                3 => 'user_profile',
                                                4 => 'subscription',
                                            }})
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                            d="M5 13l4 4L19 7"></path>
                                    </svg>
                                @else
                                    {{ $step }}
                                @endif
                            </div>

                            <span class="text-xs text-gray-600 text-center hidden sm:block">
                                {{ match ($step) {
                                    1 => 'Infos',
                                    2 => 'Fiscal',
                                    3 => 'Adresse',
                                    4 => 'Facturation',
                                } }}
                            </span>
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- Flash Messages --}}
            @if (session()->has('success'))
                <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                        </svg>
                        <p class="text-sm text-green-700 font-medium">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            @if (session()->has('info'))
                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6 rounded">
                    <p class="text-sm text-blue-700">ℹ️ {{ session('info') }}</p>
                </div>
            @endif

            @if (session()->has('error'))
                <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded">
                    <p class="text-sm text-red-700">❌ {{ session('error') }}</p>
                </div>
            @endif

            {{-- Formulaire principal --}}
            <div class="bg-white rounded-lg shadow-md p-8">

                {{-- En-tête de l'étape --}}
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-800 mb-2 flex items-center">
                        <span class="mr-3 text-3xl">{{ $this->currentStepIcon }}</span>
                        {{ $this->currentStepTitle }}
                    </h2>
                    <p class="text-gray-600">{{ $this->currentStepDescription }}</p>
                </div>

                {{-- Loading overlay --}}
                <div wire:loading.flex
                    class="absolute inset-0 bg-white bg-opacity-75 flex items-center justify-center rounded-lg z-10">
                    <div class="text-center">
                        <svg class="animate-spin h-10 w-10 text-orange-500 mx-auto mb-3"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        <p class="text-gray-600 font-medium">Sauvegarde en cours...</p>
                    </div>
                </div>

                <form>
                    {{-- STEP 1: Informations de base --}}
                    @if ($currentStep === 1)
                        <div class="space-y-5 animate-fade-in">
                            {{-- Nom entreprise --}}
                            <div>
                                <label for="companyName" class="block text-sm font-medium text-gray-700 mb-2">
                                    Nom de l'entreprise <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="companyName" wire:model.blur="companyName"
                                    class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('companyName') border-red-500 @enderror"
                                    placeholder="Ex: SARL ABC Technologie" required>
                                @error('companyName')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Raison sociale --}}
                            <div>
                                <label for="companyLegalName" class="block text-sm font-medium text-gray-700 mb-2">
                                    Raison sociale (optionnel)
                                </label>
                                <input type="text" id="companyLegalName" wire:model.blur="companyLegalName"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                                    placeholder="Nom juridique complet si différent">
                            </div>

                            {{-- Email & Téléphone --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="companyEmail" class="block text-sm font-medium text-gray-700 mb-2">
                                        Email entreprise <span class="text-red-500">*</span>
                                    </label>
                                    <input type="email" id="companyEmail" wire:model.blur="companyEmail"
                                        class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('companyEmail') border-red-500 @enderror"
                                        placeholder="contact@entreprise.ci" required>
                                    @error('companyEmail')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="companyPhone" class="block text-sm font-medium text-gray-700 mb-2">
                                        Téléphone <span class="text-red-500">*</span>
                                    </label>
                                    <input type="tel" id="companyPhone" wire:model.blur="companyPhone"
                                        class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('companyPhone') border-red-500 @enderror"
                                        placeholder="+225 07 XX XX XX XX" required>
                                    @error('companyPhone')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            {{-- Mobile (optionnel) --}}
                            <div>
                                <label for="companyMobile" class="block text-sm font-medium text-gray-700 mb-2">
                                    Téléphone mobile (optionnel)
                                </label>
                                <input type="tel" id="companyMobile" wire:model.blur="companyMobile"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                                    placeholder="+225 05 XX XX XX XX">
                            </div>
                        </div>
                    @endif

                    {{-- Navigation buttons --}}
                    <div class="flex justify-between items-center mt-8 pt-6 border-t">

                        {{-- Bouton Précédent --}}
                        @if ($this->canGoBack)
                            <button type="button" wire:click="previousStep"
                                class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition font-medium inline-flex items-center"
                                wire:loading.attr="disabled">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 19l-7-7 7-7"></path>
                                </svg>
                                Précédent
                            </button>
                        @else
                            <div></div>
                        @endif

                        {{-- Actions droite --}}
                        <div class="flex items-center space-x-3">

                            {{-- Bouton Passer (sauf étapes 1 et 4) --}}
                            @if ($currentStep !== 1 && $currentStep !== self::TOTAL_STEPS)
                                <button type="button" wire:click="skipStep"
                                    class="px-6 py-3 text-gray-600 hover:text-gray-800 transition font-medium"
                                    wire:loading.attr="disabled">
                                    Passer cette étape
                                </button>
                            @endif

                            {{-- Bouton Suivant / Terminer --}}
                            <button type="button" wire:click="submitStep{{ $currentStep }}"
                                class="px-6 py-3 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition font-medium inline-flex items-center disabled:opacity-50 disabled:cursor-not-allowed"
                                wire:loading.attr="disabled">

                                <span wire:loading.remove wire:target="submitStep{{ $currentStep }}">
                                    @if ($currentStep === self::TOTAL_STEPS)
                                        Terminer
                                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    @else
                                        Suivant
                                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    @endif
                                </span>

                                <span wire:loading wire:target="submitStep{{ $currentStep }}"
                                    class="inline-flex items-center">
                                    <svg class="animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                    Traitement...
                                </span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Footer --}}
            <div class="text-center mt-8 text-sm text-gray-600">
                <p>
                    Besoin d'aide ?
                    <a href="https://wa.me/2250700000000" target="_blank"
                        class="text-orange-600 hover:underline font-medium">
                        Contactez notre support WhatsApp
                    </a>
                </p>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            @keyframes fade-in {
                from {
                    opacity: 0;
                    transform: translateY(10px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .animate-fade-in {
                animation: fade-in 0.3s ease-out;
            }
        </style>
    @endpush

    {{-- STEP 2: Identifiants fiscaux --}}
    @if ($currentStep === 2)
        <div class="space-y-5 animate-fade-in">

            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6">
                <p class="text-sm text-blue-700">
                    ℹ️ Ces informations sont optionnelles mais fortement recommandées pour la conformité avec la DGI de
                    Côte d'Ivoire
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- NIF --}}
                <div>
                    <label for="nif" class="block text-sm font-medium text-gray-700 mb-2">
                        NIF (Numéro Identification Fiscale)
                    </label>
                    <input type="text" id="nif" wire:model.blur="nif"
                        class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('nif') border-red-500 @enderror"
                        placeholder="Ex: 1234567890">
                    @error('nif')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- RCCM --}}
                <div>
                    <label for="rccm" class="block text-sm font-medium text-gray-700 mb-2">
                        RCCM (Registre Commerce)
                    </label>
                    <input type="text" id="rccm" wire:model.blur="rccm"
                        class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('rccm') border-red-500 @enderror"
                        placeholder="Ex: CI-ABJ-2024-B-XXXXX">
                    @error('rccm')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- ICE --}}
                <div>
                    <label for="ice" class="block text-sm font-medium text-gray-700 mb-2">
                        ICE (Identifiant Commun Entreprise)
                    </label>
                    <input type="text" id="ice" wire:model.blur="ice"
                        class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('ice') border-red-500 @enderror"
                        placeholder="Ex: 000XXXXXXXX">
                    @error('ice')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- IFU --}}
                <div>
                    <label for="ifu" class="block text-sm font-medium text-gray-700 mb-2">
                        IFU (Identifiant Fiscal Unique)
                    </label>
                    <input type="text" id="ifu" wire:model.blur="ifu"
                        class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('ifu') border-red-500 @enderror"
                        placeholder="Ex: 9XXXXXXXXX">
                    @error('ifu')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Régime fiscal --}}
            <div>
                <label for="taxRegime" class="block text-sm font-medium text-gray-700 mb-2">
                    Régime fiscal <span class="text-red-500">*</span>
                </label>
                <select id="taxRegime" wire:model="taxRegime"
                    class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('taxRegime') border-red-500 @enderror"
                    required>
                    <option value="reel_simplifie">Réel Simplifié (CA &lt; 100M XOF)</option>
                    <option value="reel_normal">Réel Normal (CA &gt; 100M XOF)</option>
                </select>
                @error('taxRegime')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
    @endif

    {{-- STEP 3: Adresse & Logo --}}
    @if ($currentStep === 3)
        <div class="space-y-5 animate-fade-in">

            {{-- Adresse --}}
            <div>
                <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                    Adresse complète <span class="text-red-500">*</span>
                </label>
                <textarea id="address" wire:model.blur="address" rows="3"
                    class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('address') border-red-500 @enderror"
                    placeholder="Ex: Cocody Riviera, Rue des Jardins, Immeuble ABC, 2ème étage" required></textarea>
                @error('address')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Ville & Code postal --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="city" class="block text-sm font-medium text-gray-700 mb-2">
                        Ville <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="city" wire:model.blur="city"
                        class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('city') border-red-500 @enderror"
                        required>
                    @error('city')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="postalCode" class="block text-sm font-medium text-gray-700 mb-2">
                        Code postal
                    </label>
                    <input type="text" id="postalCode" wire:model.blur="postalCode"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                        placeholder="Ex: 01 BP 1234">
                </div>
            </div>

            {{-- Logo --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Logo de l'entreprise (optionnel)
                </label>

                @if ($existingLogo && !$logoChanged)
                    {{-- Afficher logo existant --}}
                    <div class="flex items-center space-x-4 mb-4">
                        <img src="{{ asset('storage/' . $existingLogo) }}" alt="Logo actuel"
                            class="w-32 h-32 object-contain border rounded-lg p-2">
                        <button type="button" wire:click="removeLogo"
                            class="text-red-600 hover:text-red-800 text-sm font-medium">
                            Supprimer le logo
                        </button>
                    </div>
                @endif

                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-orange-500 transition cursor-pointer"
                    x-data="{ uploading: false, progress: 0 }" x-on:livewire-upload-start="uploading = true"
                    x-on:livewire-upload-finish="uploading = false; $wire.dispatch('logo-changed')"
                    x-on:livewire-upload-error="uploading = false"
                    x-on:livewire-upload-progress="progress = $event.detail.progress">

                    <input type="file" id="logo" wire:model="logo" accept="image/*" class="hidden">

                    <label for="logo" class="cursor-pointer block">
                        @if ($logo && $logoChanged)
                            <img src="{{ $logo->temporaryUrl() }}" class="mx-auto h-32 mb-3 rounded">
                        @else
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none"
                                viewBox="0 0 48 48">
                                <path
                                    d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        @endif

                        <p class="mt-2 text-sm text-gray-600 font-medium">
                            Cliquez pour télécharger ou glissez une image
                        </p>
                        <p class="text-xs text-gray-500 mt-1">PNG, JPG, WEBP jusqu'à 2MB</p>
                    </label>

                    {{-- Progress bar --}}
                    <div x-show="uploading" class="mt-4">
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-orange-500 h-2 rounded-full transition-all" :style="`width: ${progress}%`">
                            </div>
                        </div>
                        <p class="text-xs text-gray-600 mt-1">Téléchargement en cours...</p>
                    </div>
                </div>

                @error('logo')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
    @endif

    {{-- STEP 4: Préférences facturation --}}
    @if ($currentStep === 4)
        <div class="space-y-5 animate-fade-in">

            {{-- Préfixes --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="invoicePrefix" class="block text-sm font-medium text-gray-700 mb-2">
                        Préfixe factures <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="invoicePrefix" wire:model.blur="invoicePrefix"
                        class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('invoicePrefix') border-red-500 @enderror uppercase"
                        placeholder="FAC" maxlength="10" required>
                    <p class="text-xs text-gray-500 mt-1">Ex: FAC-2024-00001</p>
                    @error('invoicePrefix')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="quotePrefix" class="block text-sm font-medium text-gray-700 mb-2">
                        Préfixe devis <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="quotePrefix" wire:model.blur="quotePrefix"
                        class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('quotePrefix') border-red-500 @enderror uppercase"
                        placeholder="DEV" maxlength="10" required>
                    <p class="text-xs text-gray-500 mt-1">Ex: DEV-2024-00001</p>
                    @error('quotePrefix')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Taux TVA --}}
            <div>
                <label for="defaultTaxRate" class="block text-sm font-medium text-gray-700 mb-2">
                    Taux de TVA par défaut (%) <span class="text-red-500">*</span>
                </label>
                <select id="defaultTaxRate" wire:model="defaultTaxRate"
                    class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('defaultTaxRate') border-red-500 @enderror"
                    required>
                    <option value="18">18% (Taux standard CI)</option>
                    <option value="10">10% (Taux réduit)</option>
                    <option value="0">0% (Exonéré)</option>
                </select>
                @error('defaultTaxRate')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Devise & Timezone --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="currency" class="block text-sm font-medium text-gray-700 mb-2">
                        Devise <span class="text-red-500">*</span>
                    </label>
                    <select id="currency" wire:model="currency"
                        class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                        required>
                        <option value="XOF">XOF (Franc CFA)</option>
                        <option value="EUR">EUR (Euro)</option>
                        <option value="USD">USD (Dollar)</option>
                    </select>
                </div>

                <div>
                    <label for="timezone" class="block text-sm font-medium text-gray-700 mb-2">
                        Fuseau horaire <span class="text-red-500">*</span>
                    </label>
                    <select id="timezone" wire:model="timezone"
                        class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                        required>
                        <option value="Africa/Abidjan">Africa/Abidjan (GMT+0)</option>
                        <option value="Africa/Accra">Africa/Accra (GMT+0)</option>
                        <option value="Europe/Paris">Europe/Paris (GMT+1)</option>
                    </select>
                </div>
            </div>

            {{-- Message final --}}
            <div class="bg-green-50 border-l-4 border-green-500 p-4 mt-6">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd" />
                    </svg>
                    <p class="text-sm text-green-700 font-medium">
                        ✅ Vous êtes prêt à démarrer ! Cliquez sur "Terminer" pour accéder à votre tableau de bord.
                    </p>
                </div>
            </div>
        </div>
</div>
