<x-layouts.auth>
    <div class="flex flex-col gap-6">
        <x-auth-header
            title="Inscription"
            description="Essai gratuit 30 jours. Sans carte bancaire"
        />

        @if ($errors->any())
            <div class="p-4 border border-red-200 rounded-lg bg-red-50 text-sm text-red-700">
                <ul class="space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>• {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}" class="flex flex-col gap-6">
            @csrf

            <flux:input
                name="company_name"
                label="Entreprise"
                type="text"
                :value="old('company_name')"
                required
                autofocus
            />

            <flux:input
                name="first_name"
                label="Prénom"
                type="text"
                :value="old('first_name')"
                required
            />

            <flux:input
                name="last_name"
                label="Nom"
                type="text"
                :value="old('last_name')"
                required
            />

            <flux:input
                name="email"
                label="Email professionnel"
                type="email"
                :value="old('email')"
                required
                autocomplete="email"
                placeholder="email@entreprise.ci"
            />

            <flux:input
                name="phone"
                label="Téléphone"
                type="tel"
                :value="old('phone')"
                placeholder="+225 XX XX XX XX XX"
            />

            <flux:input
                name="password"
                label="Mot de passe"
                type="password"
                required
                autocomplete="new-password"
                viewable
                hint="8 caractères minimum"
            />

            <flux:input
                name="password_confirmation"
                label="Confirmation du mot de passe"
                type="password"
                required
                autocomplete="new-password"
                viewable
            />

            <flux:checkbox
                name="terms"
                required
                label="J’accepte les conditions d’utilisation et la politique de confidentialité"
            />

            <flux:button
                type="submit"
                variant="primary"
                class="w-full"
                data-test="register-button"
            >
                Valider l’inscription
            </flux:button>
        </form>

        <div class="text-center text-sm text-zinc-600">
            <span>Compte existant ?</span>
            <flux:link href="{{ route('login') }}" wire:navigate>
                Connexion
            </flux:link>
        </div>
    </div>
</x-layouts.auth>
