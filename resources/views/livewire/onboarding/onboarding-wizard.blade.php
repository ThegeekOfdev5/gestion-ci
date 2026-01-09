{{-- resources/views/livewire/onboarding/enhanced-onboarding-wizard.blade.php --}}

<div>
    <div class="min-h-screen bg-gradient-to-br from-orange-50 via-white to-blue-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

            {{-- Header --}}
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-r from-orange-500 to-orange-600 rounded-full mb-4">
                    <span class="text-3xl">🏢</span>
                </div>
                <h1 class="text-4xl font-bold text-gray-900 mb-2">
                   {{ config('app.name') }}
                </h1>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    Configuration de votre espace professionnel conforme aux normes OHADA
                </p>
            </div>

            <div class="flex flex-col lg:flex-row gap-8">
                {{-- Sidebar: Progress & Overview --}}
                <div class="lg:w-1/4">
                    <div class="sticky top-8 space-y-6">
                        {{-- Progress Card --}}
                        <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-200">
                            <div class="flex items-center justify-between mb-6">
                                <h3 class="font-bold text-gray-900">Progression</h3>
                                <span class="text-2xl font-bold text-orange-600">{{ $this->progressPercentage }}%</span>
                            </div>

                            {{-- Progress Bar --}}
                            <div class="w-full bg-gray-200 rounded-full h-2.5 mb-8">
                                <div class="bg-gradient-to-r from-orange-500 to-orange-600 h-2.5 rounded-full transition-all duration-500"
                                    style="width: {{ $this->progressPercentage }}%">
                                </div>
                            </div>

                            {{-- Steps --}}
                            <div class="space-y-4">
                                @foreach(self::STEPS as $step => $data)
                                    <button type="button"
                                        wire:click="goToStep({{ $step }})"
                                        @class([
                                            'flex items-center w-full text-left p-3 rounded-xl transition-all',
                                            'bg-orange-50 border border-orange-200 ring-2 ring-orange-200' => $step === $currentStep,
                                            'hover:bg-gray-50' => $step !== $currentStep && $step <= $progress->current_step,
                                            'opacity-50 cursor-not-allowed' => $step > $progress->current_step,
                                        ])
                                        @if($step > $progress->current_step) disabled @endif>

                                        <div @class([
                                            'w-10 h-10 rounded-full flex items-center justify-center mr-3 flex-shrink-0',
                                            'bg-orange-500 text-white' => $step === $currentStep,
                                            'bg-green-500 text-white' => $step < $currentStep,
                                            'bg-gray-200 text-gray-600' => $step > $currentStep,
                                        ])>
                                            @if($step < $currentStep)
                                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                </svg>
                                            @else
                                                {{ $data['icon'] }}
                                            @endif
                                        </div>

                                        <div>
                                            <div class="font-medium text-gray-900">{{ $data['title'] }}</div>
                                            <div class="text-sm text-gray-500">Étape {{ $step }}</div>
                                        </div>
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        {{-- Quick Overview --}}
                        @if($companyName || $businessSector)
                        <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-2xl p-6 border border-orange-200">
                            <h4 class="font-bold text-gray-900 mb-4">Votre configuration</h4>
                            <div class="space-y-3">
                                @if($companyName)
                                    <div class="flex items-center">
                                        <span class="text-gray-600 text-sm w-20">Entreprise</span>
                                        <span class="font-medium text-gray-900 text-sm">{{ Str::limit($companyName, 20) }}</span>
                                    </div>
                                @endif

                                @if($businessSector)
                                    <div class="flex items-center">
                                        <span class="text-gray-600 text-sm w-20">Secteur</span>
                                        <span class="font-medium text-gray-900 text-sm">
                                            @switch($businessSector)
                                                @case('services')
                                                    Services
                                                    @break
                                                @case('commerce')
                                                    Commerce
                                                    @break
                                                @case('industrie')
                                                    Industrie
                                                    @break
                                                @case('bâtiment')
                                                    BTP
                                                    @break
                                                @case('informatique')
                                                    IT
                                                    @break
                                                @case('santé')
                                                    Santé
                                                    @break
                                                @case('transport')
                                                    Transport
                                                    @break
                                                @default
                                                    Autre
                                            @endswitch
                                        </span>
                                    </div>
                                @endif

                                <div class="flex items-center">
                                    <span class="text-gray-600 text-sm w-20">Modules</span>
                                    <span class="font-medium text-gray-900 text-sm">{{ count($selectedModules) }}</span>
                                </div>

                                @if($currentStep >= 5)
                                    <div class="flex items-center">
                                        <span class="text-gray-600 text-sm w-20">Coût mensuel</span>
                                        <span class="font-bold text-orange-600">
                                            {{ number_format($this->monthlyEstimate, 0, ',', ' ') }} XOF
                                        </span>
                                    </div>
                                @endif
                            </div>

                            {{-- Recommendations --}}
                            @if(count($this->sectorRecommendations) > 0)
                                <div class="mt-6 pt-6 border-t border-orange-200">
                                    <h5 class="font-medium text-gray-900 mb-2">Recommandations</h5>
                                    <ul class="space-y-2">
                                        @foreach($this->sectorRecommendations as $recommendation)
                                            <li class="flex items-start text-sm text-gray-700">
                                                <svg class="w-4 h-4 text-green-500 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                </svg>
                                                {{ $recommendation }}
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>
                        @endif

                        {{-- Quick Support --}}
                        <div class="text-center">
                            <div class="text-sm text-gray-600 mb-2">Besoin d'aide ?</div>
                            <a href="https://wa.me/2250700000000" target="_blank"
                               class="inline-flex items-center text-sm text-orange-600 hover:text-orange-700 font-medium">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                                </svg>
                                Support WhatsApp
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Main Content --}}
                <div class="lg:w-3/4">
                    {{-- Flash Messages --}}
                    @if (session()->has('success'))
                        <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded-lg animate-fade-in">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                <p class="text-sm text-green-700 font-medium">{{ session('success') }}</p>
                            </div>
                        </div>
                    @endif

                    @if (session()->has('error'))
                        <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-lg animate-fade-in">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-red-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                                <p class="text-sm text-red-700 font-medium">{{ session('error') }}</p>
                            </div>
                        </div>
                    @endif

                    @if (session()->has('info'))
                        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6 rounded-lg animate-fade-in">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-blue-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                                <p class="text-sm text-blue-700">{{ session('info') }}</p>
                            </div>
                        </div>
                    @endif

                    {{-- Main Form Container --}}
                    <div class="bg-white rounded-2xl shadow-lg p-8 border border-gray-200">

                        {{-- Step Header --}}
                        <div class="mb-8">
                            <div class="flex items-center mb-4">
                                <span class="text-4xl mr-4">{{ $this->currentStepIcon }}</span>
                                <div>
                                    <h2 class="text-2xl font-bold text-gray-900">{{ $this->currentStepTitle }}</h2>
                                    <p class="text-gray-600">{{ $this->currentStepDescription }}</p>
                                </div>
                            </div>

                            @if($this->stepHelp)
                                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-lg">
                                    <p class="text-sm text-blue-700">{{ $this->stepHelp }}</p>
                                </div>
                            @endif
                        </div>

                        {{-- Loading Overlay --}}
                        <div wire:loading.flex class="absolute inset-0 bg-white/80 flex items-center justify-center rounded-2xl z-10">
                            <div class="text-center">
                                <svg class="animate-spin h-12 w-12 text-orange-500 mx-auto mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <p class="text-gray-600 font-medium">Traitement en cours...</p>
                            </div>
                        </div>

                        <form wire:submit.prevent>
                            {{-- STEP 1: Welcome --}}
                            @if ($currentStep === 1)
                                <div class="space-y-8 animate-fade-in">
                                    <div class="text-center py-8">
                                        <div class="w-24 h-24 bg-gradient-to-r from-orange-500 to-orange-600 rounded-full mx-auto mb-6 flex items-center justify-center">
                                            <span class="text-4xl text-white">🏢</span>
                                        </div>

                                        <h3 class="text-2xl font-bold text-gray-900 mb-4">
                                            Bienvenue sur votre futur ERP
                                        </h3>

                                        <p class="text-lg text-gray-600 mb-8 max-w-2xl mx-auto">
                                            Nous allons configurer ensemble votre espace professionnel conforme
                                            aux normes OHADA et à la réglementation ivoirienne en 5 étapes simples.
                                        </p>

                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
                                            <div class="bg-white p-6 rounded-xl shadow-sm border text-center">
                                                <div class="text-orange-500 text-2xl mb-3">⏱️</div>
                                                <h4 class="font-semibold mb-2">7 minutes</h4>
                                                <p class="text-sm text-gray-500">Configuration rapide</p>
                                            </div>

                                            <div class="bg-white p-6 rounded-xl shadow-sm border text-center">
                                                <div class="text-orange-500 text-2xl mb-3">✅</div>
                                                <h4 class="font-semibold mb-2">Conforme DGI</h4>
                                                <p class="text-sm text-gray-500">Respect des normes fiscales</p>
                                            </div>

                                            <div class="bg-white p-6 rounded-xl shadow-sm border text-center">
                                                <div class="text-orange-500 text-2xl mb-3">🆓</div>
                                                <h4 class="font-semibold mb-2">30 jours gratuits</h4>
                                                <p class="text-sm text-gray-500">Sans engagement</p>
                                            </div>
                                        </div>

                                        {{-- Terms & Conditions --}}
                                        <div class="max-w-2xl mx-auto p-6 bg-gray-50 rounded-xl border">
                                            <div class="flex items-start mb-4">
                                                <input type="checkbox" id="acceptedTerms" wire:model.live="acceptedTerms"
                                                    class="w-5 h-5 text-orange-600 border-gray-300 rounded focus:ring-orange-500 mt-1 mr-3">
                                                <label for="acceptedTerms" class="text-gray-700">
                                                    <span class="font-medium">J'accepte les conditions générales d'utilisation</span>
                                                    <p class="text-sm text-gray-600 mt-1">
                                                        En continuant, vous acceptez nos
                                                        <a href="#" class="text-orange-600 hover:underline">conditions d'utilisation</a>,
                                                        notre
                                                        <a href="#" class="text-orange-600 hover:underline">politique de confidentialité</a>
                                                        et reconnaissez avoir lu notre
                                                        <a href="#" class="text-orange-600 hover:underline">documentation OHADA</a>.
                                                    </p>
                                                </label>
                                            </div>
                                            @error('acceptedTerms')
                                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- STEP 2: Company Profile --}}
                            @if ($currentStep === 2)
                                <div class="space-y-6 animate-fade-in">
                                    {{-- Business Sector --}}
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-3">
                                            Secteur d'activité <span class="text-red-500">*</span>
                                            <span class="text-xs text-gray-500 font-normal ml-2">(Pour des paramètres prédéfinis)</span>
                                        </label>

                                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                            @php
                                                $sectors = [
                                                    'services' => ['icon' => '💼', 'label' => 'Services'],
                                                    'commerce' => ['icon' => '🛒', 'label' => 'Commerce'],
                                                    'industrie' => ['icon' => '🏭', 'label' => 'Industrie'],
                                                    'bâtiment' => ['icon' => '🏗️', 'label' => 'BTP'],
                                                    'informatique' => ['icon' => '💻', 'label' => 'IT'],
                                                    'santé' => ['icon' => '🏥', 'label' => 'Santé'],
                                                    'transport' => ['icon' => '🚚', 'label' => 'Transport'],
                                                    'autre' => ['icon' => '📋', 'label' => 'Autre'],
                                                ];
                                            @endphp

                                            @foreach($sectors as $value => $data)
                                                <button type="button"
                                                    wire:click="$set('businessSector', '{{ $value }}')"
                                                    @class([
                                                        'flex flex-col items-center p-4 border rounded-xl transition-all',
                                                        'bg-orange-50 border-orange-500 ring-2 ring-orange-200' => $businessSector === $value,
                                                        'bg-white border-gray-300 hover:border-orange-300 hover:bg-orange-50' => $businessSector !== $value,
                                                    ])>
                                                    <span class="text-2xl mb-2">{{ $data['icon'] }}</span>
                                                    <span class="text-sm font-medium">{{ $data['label'] }}</span>
                                                </button>
                                            @endforeach
                                        </div>
                                        @error('businessSector')
                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    {{-- Company Information --}}
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div>
                                            <label for="companyName" class="block text-sm font-medium text-gray-700 mb-2">
                                                Nom commercial <span class="text-red-500">*</span>
                                            </label>
                                            <input type="text" id="companyName" wire:model.blur="companyName"
                                                class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('companyName') border-red-500 @enderror"
                                                placeholder="Ex: SARL Excellence Services" required>
                                            @error('companyName')
                                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div>
                                            <label for="companyLegalName" class="block text-sm font-medium text-gray-700 mb-2">
                                                Raison sociale
                                            </label>
                                            <input type="text" id="companyLegalName" wire:model.blur="companyLegalName"
                                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                                                placeholder="Nom juridique complet si différent">
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div>
                                            <label for="companyEmail" class="block text-sm font-medium text-gray-700 mb-2">
                                                Email professionnel <span class="text-red-500">*</span>
                                            </label>
                                            <input type="email" id="companyEmail" wire:model.blur="companyEmail"
                                                class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('companyEmail') border-red-500 @enderror"
                                                placeholder="contact@entreprise.ci" required>
                                            @error('companyEmail')
                                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
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
                                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>

                                    <div>
                                        <label for="companyMobile" class="block text-sm font-medium text-gray-700 mb-2">
                                            Téléphone mobile
                                        </label>
                                        <input type="tel" id="companyMobile" wire:model.blur="companyMobile"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                                            placeholder="+225 05 XX XX XX XX">
                                    </div>

                                    {{-- Address --}}
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div>
                                            <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                                                Adresse <span class="text-red-500">*</span>
                                            </label>
                                            <textarea id="address" wire:model.blur="address" rows="3"
                                                class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('address') border-red-500 @enderror"
                                                placeholder="Ex: Cocody Riviera, Rue des Jardins, Immeuble ABC, 2ème étage" required></textarea>
                                            @error('address')
                                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div class="space-y-4">
                                            <div>
                                                <label for="city" class="block text-sm font-medium text-gray-700 mb-2">
                                                    Ville <span class="text-red-500">*</span>
                                                </label>
                                                <input type="text" id="city" wire:model.blur="city"
                                                    class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('city') border-red-500 @enderror"
                                                    placeholder="Abidjan" required>
                                                @error('city')
                                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                                @enderror
                                            </div>

                                            <div>
                                                <label for="postalCode" class="block text-sm font-medium text-gray-700 mb-2">
                                                    Code postal
                                                </label>
                                                <input type="text" id="postalCode" wire:model.blur="postalCode"
                                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                                                    placeholder="01 BP 1234">
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Logo --}}
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-3">
                                            Logo de l'entreprise
                                            <span class="text-xs text-gray-500 font-normal">(Optionnel - PNG, JPG, WEBP jusqu'à 2MB)</span>
                                        </label>

                                        @if($existingLogo && !$logoChanged)
                                            <div class="flex items-center space-x-4 mb-4">
                                                <img src="{{ asset('storage/' . $existingLogo) }}" alt="Logo actuel"
                                                    class="w-32 h-32 object-contain border rounded-lg p-2">
                                                <button type="button" wire:click="removeLogo"
                                                    class="text-red-600 hover:text-red-800 text-sm font-medium">
                                                    Supprimer le logo
                                                </button>
                                            </div>
                                        @endif

                                        <div class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center hover:border-orange-500 transition cursor-pointer"
                                            x-data="{ uploading: false, progress: 0 }"
                                            x-on:livewire-upload-start="uploading = true"
                                            x-on:livewire-upload-finish="uploading = false; $wire.dispatch('logo-changed')"
                                            x-on:livewire-upload-error="uploading = false"
                                            x-on:livewire-upload-progress="progress = $event.detail.progress">

                                            <input type="file" id="logo" wire:model="logo" accept="image/*" class="hidden">

                                            <label for="logo" class="cursor-pointer block">
                                                @if($logo && $logoChanged)
                                                    <img src="{{ $logo->temporaryUrl() }}" class="mx-auto h-40 mb-4 rounded-lg shadow">
                                                @else
                                                    <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                    </svg>
                                                @endif

                                                <p class="text-lg font-medium text-gray-900 mb-2">
                                                    @if($logo && $logoChanged)
                                                        Image sélectionnée
                                                    @else
                                                        Télécharger votre logo
                                                    @endif
                                                </p>
                                                <p class="text-sm text-gray-500">
                                                    Cliquez pour sélectionner ou glissez une image
                                                </p>
                                            </label>

                                            <div x-show="uploading" class="mt-6">
                                                <div class="w-full bg-gray-200 rounded-full h-2">
                                                    <div class="bg-orange-500 h-2 rounded-full transition-all" :style="`width: ${progress}%`"></div>
                                                </div>
                                                <p class="text-sm text-gray-600 mt-2">Téléchargement en cours...</p>
                                            </div>
                                        </div>

                                        @error('logo')
                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            @endif

                            {{-- STEP 3: Fiscal Identity --}}
                            @if ($currentStep === 3)
                                <div class="space-y-6 animate-fade-in">
                                    {{-- DGI Compliance Notice --}}
                                    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-lg">
                                        <div class="flex">
                                            <div class="flex-shrink-0">
                                                <svg class="h-5 w-5 text-blue-500" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                            <div class="ml-3">
                                                <h3 class="text-sm font-medium text-blue-800">
                                                    Conformité DGI Côte d'Ivoire
                                                </h3>
                                                <div class="mt-2 text-sm text-blue-700">
                                                    <p>Ces identifiants sont obligatoires pour :</p>
                                                    <ul class="list-disc pl-5 mt-1 space-y-1">
                                                        <li>Facturation légale aux clients</li>
                                                        <li>Déclarations fiscales mensuelles</li>
                                                        <li>Conformité avec l'administration fiscale</li>
                                                        <li>Déduction de la TVA</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Fiscal Identifiers --}}
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        {{-- NIF --}}
                                        <div>
                                            <div class="flex items-center justify-between mb-2">
                                                <label for="nif" class="block text-sm font-medium text-gray-700">
                                                    Numéro d'Identification Fiscale (NIF)
                                                </label>
                                                <span class="text-xs text-gray-500">Format : 1234567890</span>
                                            </div>

                                            <div class="relative">
                                                <input type="text" id="nif" wire:model.blur="nif"
                                                    class="w-full px-4 py-3 pl-10 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('nif') border-red-500 @enderror"
                                                    placeholder="Ex: 1234567890">

                                                <div class="absolute left-3 top-1/2 transform -translate-y-1/2">
                                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                                    </svg>
                                                </div>

                                                @if($nif && !$errors->has('nif'))
                                                    <div class="absolute right-3 top-1/2 transform -translate-y-1/2">
                                                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                        </svg>
                                                    </div>
                                                @endif
                                            </div>
                                            @error('nif')
                                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        {{-- RCCM --}}
                                        <div>
                                            <label for="rccm" class="block text-sm font-medium text-gray-700 mb-2">
                                                Registre de Commerce (RCCM)
                                            </label>
                                            <input type="text" id="rccm" wire:model.blur="rccm"
                                                class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('rccm') border-red-500 @enderror"
                                                placeholder="Ex: CI-ABJ-2024-B-XXXXX">
                                            @error('rccm')
                                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        {{-- ICE --}}
                                        <div>
                                            <label for="ice" class="block text-sm font-medium text-gray-700 mb-2">
                                                Identifiant Commun Entreprise (ICE)
                                            </label>
                                            <input type="text" id="ice" wire:model.blur="ice"
                                                class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('ice') border-red-500 @enderror"
                                                placeholder="Ex: 000XXXXXXXX">
                                            @error('ice')
                                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        {{-- IFU --}}
                                        <div>
                                            <label for="ifu" class="block text-sm font-medium text-gray-700 mb-2">
                                                Identifiant Fiscal Unique (IFU)
                                            </label>
                                            <input type="text" id="ifu" wire:model.blur="ifu"
                                                class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('ifu') border-red-500 @enderror"
                                                placeholder="Ex: 9XXXXXXXXX">
                                            @error('ifu')
                                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Tax Regime --}}
                                    <div>
                                        <label for="taxRegime" class="block text-sm font-medium text-gray-700 mb-2">
                                            Régime fiscal <span class="text-red-500">*</span>
                                        </label>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <button type="button"
                                                wire:click="$set('taxRegime', 'reel_simplifie')"
                                                @class([
                                                    'p-4 border rounded-lg text-left transition-all',
                                                    'bg-orange-50 border-orange-500 ring-2 ring-orange-200' => $taxRegime === 'reel_simplifie',
                                                    'bg-white border-gray-300 hover:border-orange-300 hover:bg-orange-50' => $taxRegime !== 'reel_simplifie',
                                                ])>
                                                <div class="font-medium mb-1">Réel Simplifié</div>
                                                <div class="text-sm text-gray-600">Chiffre d'affaires &lt; 100M XOF</div>
                                            </button>

                                            <button type="button"
                                                wire:click="$set('taxRegime', 'reel_normal')"
                                                @class([
                                                    'p-4 border rounded-lg text-left transition-all',
                                                    'bg-orange-50 border-orange-500 ring-2 ring-orange-200' => $taxRegime === 'reel_normal',
                                                    'bg-white border-gray-300 hover:border-orange-300 hover:bg-orange-50' => $taxRegime !== 'reel_normal',
                                                ])>
                                                <div class="font-medium mb-1">Réel Normal</div>
                                                <div class="text-sm text-gray-600">Chiffre d'affaires ≥ 100M XOF</div>
                                            </button>
                                        </div>
                                        @error('taxRegime')
                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    {{-- Additional Tax Info --}}
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div>
                                            <label for="taxCardNumber" class="block text-sm font-medium text-gray-700 mb-2">
                                                Numéro de carte de contribuable
                                            </label>
                                            <input type="text" id="taxCardNumber" wire:model.blur="taxCardNumber"
                                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                                        </div>

                                        <div>
                                            <label for="taxOffice" class="block text-sm font-medium text-gray-700 mb-2">
                                                Centre des impôts
                                            </label>
                                            <input type="text" id="taxOffice" wire:model.blur="taxOffice"
                                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                                                placeholder="Ex: Centre des impôts de Cocody">
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- STEP 4: Financial Setup --}}
                            @if ($currentStep === 4)
                                <div class="space-y-6 animate-fade-in">
                                    {{-- Invoice Prefixes --}}
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div>
                                            <label for="invoicePrefix" class="block text-sm font-medium text-gray-700 mb-2">
                                                Préfixe factures <span class="text-red-500">*</span>
                                            </label>
                                            <input type="text" id="invoicePrefix" wire:model.blur="invoicePrefix"
                                                class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('invoicePrefix') border-red-500 @enderror uppercase"
                                                placeholder="FAC" maxlength="10" required>
                                            <p class="mt-1 text-xs text-gray-500">Ex: FAC-2024-00001</p>
                                            @error('invoicePrefix')
                                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div>
                                            <label for="quotePrefix" class="block text-sm font-medium text-gray-700 mb-2">
                                                Préfixe devis <span class="text-red-500">*</span>
                                            </label>
                                            <input type="text" id="quotePrefix" wire:model.blur="quotePrefix"
                                                class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('quotePrefix') border-red-500 @enderror uppercase"
                                                placeholder="DEV" maxlength="10" required>
                                            <p class="mt-1 text-xs text-gray-500">Ex: DEV-2024-00001</p>
                                            @error('quotePrefix')
                                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- VAT Configuration --}}
                                    <div>
                                        <div class="flex items-center justify-between mb-2">
                                            <label class="block text-sm font-medium text-gray-700">
                                                Taux de TVA par défaut
                                            </label>
                                            <div class="flex items-center">
                                                <input type="checkbox" id="vatEnabled" wire:model.live="vatEnabled"
                                                    class="w-4 h-4 text-orange-600 border-gray-300 rounded focus:ring-orange-500 mr-2">
                                                <label for="vatEnabled" class="text-sm text-gray-700">TVA applicable</label>
                                            </div>
                                        </div>

                                        @if($vatEnabled)
                                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                                <div>
                                                    <select id="defaultTaxRate" wire:model.live="defaultTaxRate"
                                                        class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                                                        <option value="18">18% (Taux standard CI)</option>
                                                        <option value="10">10% (Taux réduit)</option>
                                                        <option value="0">0% (Exonéré)</option>
                                                        <option value="custom">Personnalisé</option>
                                                    </select>
                                                </div>

                                                @if($defaultTaxRate === 'custom')
                                                    <div>
                                                        <input type="number" wire:model="customTaxRate" min="0" max="100" step="0.01"
                                                            class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                                                            placeholder="%">
                                                    </div>
                                                @endif

                                                {{-- Quick Calculator --}}
                                                <div class="bg-gray-50 p-4 rounded-lg border">
                                                    <div class="text-xs text-gray-500 mb-1">Exemple pour 100.000 XOF</div>
                                                    <div class="font-medium text-gray-900">
                                                        TVA : {{ number_format(100000 * ($this->effectiveTaxRate / 100), 0, ',', ' ') }} XOF
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <div class="bg-gray-50 p-4 rounded-lg border">
                                                <p class="text-sm text-gray-600">La TVA ne sera pas appliquée sur vos factures.</p>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Payment Terms --}}
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Conditions de paiement par défaut
                                        </label>

                                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                            @foreach([
                                                'immédiat' => 'À réception',
                                                '7j' => '7 jours nets',
                                                '30j' => '30 jours nets',
                                                '60j' => '60 jours nets',
                                            ] as $value => $label)
                                                <button type="button"
                                                    wire:click="$set('paymentTerms', '{{ $value }}')"
                                                    @class([
                                                        'py-3 border rounded-lg transition font-medium',
                                                        'bg-orange-500 text-white border-orange-600' => $paymentTerms === $value,
                                                        'bg-white text-gray-700 border-gray-300 hover:border-orange-300' => $paymentTerms !== $value,
                                                    ])>
                                                    {{ $label }}
                                                </button>
                                            @endforeach
                                        </div>
                                    </div>

                                    {{-- Currency & Timezone --}}
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div>
                                            <label for="currency" class="block text-sm font-medium text-gray-700 mb-2">
                                                Devise principale <span class="text-red-500">*</span>
                                            </label>
                                            <select id="currency" wire:model="currency"
                                                class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                                                <option value="XOF">XOF - Franc CFA Ouest Africain</option>
                                                <option value="EUR">EUR - Euro</option>
                                                <option value="USD">USD - Dollar Américain</option>
                                            </select>
                                        </div>

                                        <div>
                                            <label for="timezone" class="block text-sm font-medium text-gray-700 mb-2">
                                                Fuseau horaire <span class="text-red-500">*</span>
                                            </label>
                                            <select id="timezone" wire:model="timezone"
                                                class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                                                <option value="Africa/Abidjan">Afrique/Abidjan (GMT+0)</option>
                                                <option value="Africa/Accra">Afrique/Accra (GMT+0)</option>
                                                <option value="Europe/Paris">Europe/Paris (GMT+1)</option>
                                            </select>
                                        </div>
                                    </div>

                                    {{-- Fiscal Year --}}
                                    <div>
                                        <label for="fiscalYearStart" class="block text-sm font-medium text-gray-700 mb-2">
                                            Début de l'année fiscale <span class="text-red-500">*</span>
                                        </label>
                                        <div class="flex items-center space-x-4">
                                            <input type="text" id="fiscalYearStart" wire:model.blur="fiscalYearStart"
                                                class="w-32 px-4 py-3 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('fiscalYearStart') border-red-500 @enderror"
                                                placeholder="JJ-MM" pattern="\d{2}-\d{2}" required>
                                            <div class="text-sm text-gray-600">
                                                Format : Jour-Mois (Ex: 01-01 pour le 1er Janvier)
                                            </div>
                                        </div>
                                        @error('fiscalYearStart')
                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            @endif

                            {{-- STEP 5: Modules & Plan --}}
                            @if ($currentStep === 5)
                                <div class="space-y-8 animate-fade-in">
                                    {{-- Company Preview Card --}}
                                    <div class="bg-white border rounded-xl shadow-sm p-6 mb-6">
                                        <div class="flex items-start justify-between mb-6">
                                            <div class="flex items-center">
                                                @if($existingLogo)
                                                    <img src="{{ asset('storage/' . $existingLogo) }}" alt="Logo" class="w-16 h-16 rounded-lg object-cover border mr-4">
                                                @else
                                                    <div class="w-16 h-16 bg-gradient-to-r from-orange-500 to-orange-600 rounded-lg flex items-center justify-center text-white text-2xl mr-4">
                                                        {{ strtoupper(substr($companyName, 0, 2)) }}
                                                    </div>
                                                @endif
                                                <div>
                                                    <h3 class="text-xl font-bold text-gray-900">{{ $companyName }}</h3>
                                                    <p class="text-gray-600">{{ $companyEmail }}</p>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <div class="text-sm text-gray-500">Prêt à démarrer</div>
                                                <div class="text-2xl font-bold text-orange-600">✅</div>
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                            <div>
                                                <h4 class="font-medium text-gray-900 mb-3">Informations principales</h4>
                                                <dl class="space-y-2">
                                                    <div class="flex justify-between">
                                                        <dt class="text-gray-600">Téléphone</dt>
                                                        <dd class="font-medium">{{ $companyPhone }}</dd>
                                                    </div>
                                                    <div class="flex justify-between">
                                                        <dt class="text-gray-600">Adresse</dt>
                                                        <dd class="font-medium text-right">{{ Str::limit($address, 30) }}</dd>
                                                    </div>
                                                    <div class="flex justify-between">
                                                        <dt class="text-gray-600">Secteur</dt>
                                                        <dd class="font-medium">
                                                            @switch($businessSector)
                                                                @case('services')
                                                                    Services
                                                                    @break
                                                                @case('commerce')
                                                                    Commerce
                                                                    @break
                                                                @case('industrie')
                                                                    Industrie
                                                                    @break
                                                                @case('bâtiment')
                                                                    BTP
                                                                    @break
                                                                @case('informatique')
                                                                    IT
                                                                    @break
                                                                @case('santé')
                                                                    Santé
                                                                    @break
                                                                @case('transport')
                                                                    Transport
                                                                    @break
                                                                @default
                                                                    Autre
                                                            @endswitch
                                                        </dd>
                                                    </div>
                                                </dl>
                                            </div>

                                            <div>
                                                <h4 class="font-medium text-gray-900 mb-3">Configuration</h4>
                                                <dl class="space-y-2">
                                                    <div class="flex justify-between">
                                                        <dt class="text-gray-600">TVA par défaut</dt>
                                                        <dd class="font-medium">{{ $this->effectiveTaxRate }}%</dd>
                                                    </div>
                                                    <div class="flex justify-between">
                                                        <dt class="text-gray-600">Devise</dt>
                                                        <dd class="font-medium">{{ $currency }}</dd>
                                                    </div>
                                                    <div class="flex justify-between">
                                                        <dt class="text-gray-600">Conditions paiement</dt>
                                                        <dd class="font-medium">
                                                            @switch($paymentTerms)
                                                                @case('immédiat')
                                                                    À réception
                                                                    @break
                                                                @case('7j')
                                                                    7 jours
                                                                    @break
                                                                @case('30j')
                                                                    30 jours
                                                                    @break
                                                                @case('60j')
                                                                    60 jours
                                                                    @break
                                                            @endswitch
                                                        </dd>
                                                    </div>
                                                </dl>
                                            </div>
                                        </div>

                                        {{-- Compliance Checklist --}}
                                        <div class="mt-6 pt-6 border-t">
                                            <h4 class="font-medium text-gray-900 mb-3">Checklist de conformité</h4>
                                            <div class="space-y-2">
                                                @foreach([
                                                    ['condition' => $companyName, 'label' => 'Nom de l\'entreprise renseigné'],
                                                    ['condition' => $companyEmail, 'label' => 'Email professionnel valide'],
                                                    ['condition' => $address, 'label' => 'Adresse complète'],
                                                    ['condition' => $taxRegime, 'label' => 'Régime fiscal sélectionné'],
                                                    ['condition' => true, 'label' => 'Configuration financière complète'],
                                                ] as $item)
                                                    <div class="flex items-center">
                                                        <div class="w-5 h-5 rounded-full mr-3 flex items-center justify-center {{ $item['condition'] ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-400' }}">
                                                            @if($item['condition'])
                                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                                </svg>
                                                            @else
                                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                                </svg>
                                                            @endif
                                                        </div>
                                                        <span class="text-sm {{ $item['condition'] ? 'text-gray-900' : 'text-gray-500' }}">
                                                            {{ $item['label'] }}
                                                        </span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Module Selection --}}
                                    <div>
                                        <h3 class="text-lg font-bold text-gray-900 mb-4">
                                            Choisissez vos modules
                                        </h3>
                                        <p class="text-gray-600 mb-6">
                                            Sélectionnez les fonctionnalités dont vous avez besoin. Vous pourrez en ajouter plus tard.
                                        </p>

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            @foreach($this->moduleDetails as $key => $module)
                                                <div @class([
                                                    'relative border rounded-lg p-4 transition-all cursor-pointer',
                                                    'border-orange-500 bg-orange-50 ring-2 ring-orange-200' => in_array($key, $selectedModules) || $module['required'],
                                                    'border-gray-300 bg-white hover:border-orange-300' => !in_array($key, $selectedModules) && !$module['required'],
                                                ])
                                                wire:click="toggleModule('{{ $key }}')"
                                                @if($module['required']) onclick="return false;" @endif>

                                                    @if($module['required'])
                                                        <div class="absolute top-2 right-2 bg-orange-500 text-white text-xs px-2 py-1 rounded">
                                                            Obligatoire
                                                        </div>
                                                    @endif

                                                    @if($module['included'])
                                                        <div class="absolute top-2 right-2 bg-green-500 text-white text-xs px-2 py-1 rounded">
                                                            Inclus
                                                        </div>
                                                    @endif

                                                    <div class="flex items-start">
                                                        <span class="text-2xl mr-3">{{ $module['icon'] }}</span>
                                                        <div class="flex-1">
                                                            <div class="flex justify-between items-start">
                                                                <h4 class="font-medium text-gray-900">{{ $module['title'] }}</h4>
                                                                @if(!$module['included'])
                                                                    <span class="text-sm font-medium text-gray-600">
                                                                        +10.000 XOF/mois
                                                                    </span>
                                                                @endif
                                                            </div>
                                                            <p class="text-sm text-gray-600 mt-1">{{ $module['description'] }}</p>
                                                        </div>
                                                    </div>

                                                    @if(!$module['required'])
                                                        <div class="mt-3 flex items-center">
                                                            <div @class([
                                                                'w-5 h-5 border rounded flex items-center justify-center mr-2',
                                                                'bg-orange-500 border-orange-600' => in_array($key, $selectedModules),
                                                                'bg-white border-gray-400' => !in_array($key, $selectedModules),
                                                            ])>
                                                                @if(in_array($key, $selectedModules))
                                                                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                                                    </svg>
                                                                @endif
                                                            </div>
                                                            <span class="text-sm">Activer ce module</span>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    {{-- Plan Selection --}}
                                    <div>
                                        <h3 class="text-lg font-bold text-gray-900 mb-4">
                                            Choisissez votre plan
                                        </h3>

                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                            @foreach([
                                                [
                                                    'name' => 'Essentiel',
                                                    'price' => '50.000',
                                                    'period' => '/mois',
                                                    'users' => '2 utilisateurs',
                                                    'features' => ['Facturation illimitée', 'Comptabilité OHADA', 'Support email', 'Stockage 5GB'],
                                                    'recommended' => false,
                                                ],
                                                [
                                                    'name' => 'Professionnel',
                                                    'price' => '75.000',
                                                    'period' => '/mois',
                                                    'users' => '5 utilisateurs',
                                                    'features' => ['Tout dans Essentiel', '+ CRM Clients', '+ Gestion de stocks', 'Support prioritaire', 'Stockage 20GB'],
                                                    'recommended' => true,
                                                ],
                                                [
                                                    'name' => 'Entreprise',
                                                    'price' => '120.000',
                                                    'period' => '/mois',
                                                    'users' => '10 utilisateurs',
                                                    'features' => ['Tout dans Professionnel', '+ Paie & RH', '+ Analytique avancée', 'Support dédié', 'Stockage illimité'],
                                                    'recommended' => false,
                                                ],
                                            ] as $plan)
                                                <div @class([
                                                    'relative border rounded-xl p-6 transition-all hover:shadow-lg',
                                                    'border-orange-500 ring-2 ring-orange-200' => $plan['recommended'],
                                                    'border-gray-300' => !$plan['recommended'],
                                                ])>
                                                    @if($plan['recommended'])
                                                        <div class="absolute top-0 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-orange-500 text-white text-sm font-medium px-4 py-1 rounded-full">
                                                            Recommandé
                                                        </div>
                                                    @endif

                                                    <div class="text-center mb-6">
                                                        <h4 class="text-xl font-bold text-gray-900 mb-2">{{ $plan['name'] }}</h4>
                                                        <div class="flex items-baseline justify-center">
                                                            <span class="text-3xl font-bold text-gray-900">{{ $plan['price'] }}</span>
                                                            <span class="text-gray-600 ml-1">{{ $plan['period'] }}</span>
                                                        </div>
                                                        <div class="text-sm text-gray-500 mt-2">{{ $plan['users'] }}</div>
                                                    </div>

                                                    <ul class="space-y-3 mb-6">
                                                        @foreach($plan['features'] as $feature)
                                                            <li class="flex items-start">
                                                                <svg class="w-5 h-5 text-green-500 mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                                </svg>
                                                                <span class="text-gray-700 text-sm">{{ $feature }}</span>
                                                            </li>
                                                        @endforeach
                                                    </ul>

                                                    <button type="button"
                                                        wire:click="$set('selectedPlan', '{{ strtolower($plan['name']) }}')"
                                                        @class([
                                                            'w-full py-3 rounded-lg font-medium transition',
                                                            'bg-orange-500 text-white hover:bg-orange-600' => $selectedPlan === strtolower($plan['name']),
                                                            'bg-gray-100 text-gray-900 hover:bg-gray-200 border border-gray-300' => $selectedPlan !== strtolower($plan['name']),
                                                        ])>
                                                        {{ $selectedPlan === strtolower($plan['name']) ? '✓ Sélectionné' : 'Choisir ce plan' }}
                                                    </button>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    {{-- Additional Users --}}
                                    <div>
                                        <label for="userCount" class="block text-sm font-medium text-gray-700 mb-2">
                                            Nombre d'utilisateurs supplémentaires
                                        </label>
                                        <div class="flex items-center space-x-4">
                                            <input type="range" id="userCount" wire:model.live="userCount"
                                                min="1" max="20" step="1"
                                                class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer">
                                            <div class="text-lg font-bold text-gray-900 min-w-[4rem]">
                                                {{ $userCount }}
                                            </div>
                                        </div>
                                        <div class="flex justify-between text-sm text-gray-500 mt-2">
                                            <span>1 utilisateur</span>
                                            <span>20 utilisateurs</span>
                                        </div>
                                        <p class="text-sm text-gray-600 mt-2">
                                            Coût supplémentaire : {{ number_format(max(0, $userCount - match($selectedPlan) {
                                                'essentiel' => 2,
                                                'professionnel' => 5,
                                                'entreprise' => 10,
                                                default => 2,
                                            }) * 5000, 0, ',', ' ') }} XOF/mois
                                        </p>
                                    </div>

                                    {{-- Final Options --}}
                                    <div class="space-y-4">
                                        <div class="flex items-center">
                                            <input type="checkbox" id="newsletterSubscription" wire:model="newsletterSubscription"
                                                class="w-4 h-4 text-orange-600 border-gray-300 rounded focus:ring-orange-500 mr-3">
                                            <label for="newsletterSubscription" class="text-gray-700">
                                                Je souhaite recevoir les conseils mensuels OHADA et les mises à jour du système
                                            </label>
                                        </div>

                                        <div class="flex items-center">
                                            <input type="checkbox" id="demoRequested" wire:model="demoRequested"
                                                class="w-4 h-4 text-orange-600 border-gray-300 rounded focus:ring-orange-500 mr-3">
                                            <label for="demoRequested" class="text-gray-700">
                                                Je souhaite une démonstration personnalisée avec un expert OHADA
                                            </label>
                                        </div>
                                    </div>

                                    {{-- Final Summary --}}
                                    <div class="bg-gradient-to-r from-orange-50 to-yellow-50 rounded-xl p-6 border border-orange-200">
                                        <div class="flex flex-col md:flex-row justify-between items-center">
                                            <div class="mb-4 md:mb-0">
                                                <div class="text-sm text-gray-600">Coût mensuel estimé</div>
                                                <div class="text-3xl font-bold text-gray-900">
                                                    {{ number_format($this->monthlyEstimate, 0, ',', ' ') }} XOF
                                                </div>
                                                <div class="text-sm text-gray-500">Hors taxes. 30 jours d'essai gratuit.</div>
                                            </div>
                                            <div class="text-center md:text-right">
                                                <div class="text-sm text-gray-600 mb-2">Votre configuration comprend :</div>
                                                <div class="space-y-1">
                                                    <div class="text-sm text-gray-900">{{ count($selectedModules) }} modules activés</div>
                                                    <div class="text-sm text-gray-900">{{ $userCount }} utilisateurs</div>
                                                    <div class="text-green-600 font-medium">✓ Premier mois gratuit</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Final Confirmation --}}
                                    <div class="bg-green-50 border-l-4 border-green-500 p-6 rounded-lg">
                                        <div class="flex items-center">
                                            <svg class="w-8 h-8 text-green-500 mr-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                            <div>
                                                <p class="text-lg font-medium text-green-800">
                                                    ✅ Votre espace ERP OHADA Cloud est prêt !
                                                </p>
                                                <p class="text-sm text-green-700 mt-1">
                                                    Cliquez sur "Terminer" pour accéder immédiatement à votre tableau de bord.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- Navigation Buttons --}}
                            <div class="flex justify-between items-center mt-12 pt-6 border-t">
                                {{-- Previous Button --}}
                                @if ($this->canGoBack)
                                    <button type="button" wire:click="previousStep"
                                        class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition font-medium inline-flex items-center"
                                        wire:loading.attr="disabled">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                        </svg>
                                        Précédent
                                    </button>
                                @else
                                    <div></div>
                                @endif

                                {{-- Right Actions --}}
                                <div class="flex items-center space-x-4">
                                    {{-- Skip Button (except steps 1 and 5) --}}
                                    @if ($currentStep !== 1 && $currentStep !== $totalSteps)
                                        <button type="button" wire:click="skipStep"
                                            class="px-6 py-3 text-gray-600 hover:text-gray-800 transition font-medium"
                                            wire:loading.attr="disabled">
                                            Passer cette étape
                                        </button>
                                    @endif

                                    {{-- Next/Finish Button --}}
                                    <button type="button"
                                        wire:click="submitStep{{ $currentStep }}"
                                        class="px-8 py-3 bg-gradient-to-r from-orange-500 to-orange-600 text-white rounded-lg hover:from-orange-600 hover:to-orange-700 transition font-medium inline-flex items-center shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed"
                                        wire:loading.attr="disabled">

                                        <span wire:loading.remove wire:target="submitStep{{ $currentStep }}">
                                            @if ($currentStep === $totalSteps)
                                                Terminer & Accéder
                                                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                                </svg>
                                            @else
                                                Continuer
                                                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                                </svg>
                                            @endif
                                        </span>

                                        <span wire:loading wire:target="submitStep{{ $currentStep }}"
                                            class="inline-flex items-center">
                                            <svg class="animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
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
                            Configuration sécurisée • Conforme RGPD •
                            <a href="#" class="text-orange-600 hover:underline font-medium">Politique de confidentialité</a>
                        </p>
                    </div>
                </div>
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

            input[type="range"] {
                -webkit-appearance: none;
                appearance: none;
                background: transparent;
                cursor: pointer;
            }

            input[type="range"]::-webkit-slider-track {
                background: #e5e7eb;
                height: 0.5rem;
                border-radius: 0.25rem;
            }

            input[type="range"]::-webkit-slider-thumb {
                -webkit-appearance: none;
                appearance: none;
                height: 1.5rem;
                width: 1.5rem;
                background-color: #f97316;
                border-radius: 50%;
                border: 2px solid white;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            }

            input[type="range"]::-moz-range-track {
                background: #e5e7eb;
                height: 0.5rem;
                border-radius: 0.25rem;
            }

            input[type="range"]::-moz-range-thumb {
                height: 1.5rem;
                width: 1.5rem;
                background-color: #f97316;
                border-radius: 50%;
                border: 2px solid white;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            }
        </style>
    @endpush
</div>
