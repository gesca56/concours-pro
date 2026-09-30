<x-guest-layout>
    <div class="mb-6">
        <h2 class="text-xl font-bold text-marine">Créer mon compte candidat</h2>
        <p class="text-sm text-gray-500">Étape 1 de la préinscription. Vous choisirez ensuite votre concours.</p>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <p class="mt-1 text-xs text-gray-500">Tels qu'ils figurent sur votre carte nationale d'identité : ils seront repris sur la convocation.</p>
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Date de naissance (requise pour l'éligibilité aux concours) -->
        <div class="mt-4">
            <x-input-label for="date_naissance" :value="__('Date de naissance')" />
            <x-text-input id="date_naissance" class="block mt-1 w-full" type="date" name="date_naissance" :value="old('date_naissance')" required max="{{ now()->toDateString() }}" autocomplete="bday" />
            <p class="mt-1 text-xs text-gray-500">L'âge est apprécié au 1er janvier de l'année du concours ({{ config('ipnetp.age_min') }} à {{ config('ipnetp.age_max') }} ans).</p>
            <x-input-error :messages="$errors->get('date_naissance')" class="mt-2" />
        </div>

        <!-- Téléphone -->
        <div class="mt-4">
            <x-input-label for="telephone" :value="__('Téléphone (facultatif)')" />
            <x-text-input id="telephone" class="block mt-1 w-full" type="tel" name="telephone" :value="old('telephone')" autocomplete="tel" placeholder="07 00 00 00 00" />
            <p class="mt-1 text-xs text-gray-500">Utile au secrétariat pour vous joindre en cas de pièce rejetée.</p>
            <x-input-error :messages="$errors->get('telephone')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
