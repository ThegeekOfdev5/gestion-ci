<x-layouts.auth>
    <div class="flex flex-col gap-6">
        <x-auth-header
            title="Connexion"
            description="Accédez à votre espace de gestion"
        />

        <x-auth-session-status
            class="text-center"
            :status="session('status')"
        />

        @if ($errors->any())
            <div class="p-4 border border-red-200 rounded-lg bg-red-50 text-sm text-red-700 text-center">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-6">
            @csrf

            <flux:input
                name="email"
                label="Email"
                type="email"
                :value="old('email')"
                required
                autofocus
                autocomplete="email"
                placeholder="email@example.com"
            />

            <div class="relative">
                <flux:input
                    name="password"
                    label="Mot de passe"
                    type="password"
                    required
                    autocomplete="current-password"
                    placeholder="Mot de passe"
                    viewable
                />

                @if (Route::has('password.request'))
                    <flux:link
                        class="absolute top-0 end-0 text-sm"
                        href="{{ route('password.request') }}"
                        wire:navigate
                    >
                        Mot de passe oublié ?
                    </flux:link>
                @endif
            </div>

            <flux:checkbox
                name="remember"
                label="Se souvenir de moi"
                :checked="old('remember')"
            />

            <flux:button
                type="submit"
                variant="primary"
                class="w-full"
                data-test="login-button"
            >
                Se connecter
            </flux:button>
        </form>

        @if (Route::has('register'))
            <div class="text-center text-sm text-zinc-600">
                <span>Pas encore de compte ?</span>
                <flux:link href="{{ route('register') }}" wire:navigate>
                    Créer un compte
                </flux:link>
            </div>
        @endif
    </div>
</x-layouts.auth>
