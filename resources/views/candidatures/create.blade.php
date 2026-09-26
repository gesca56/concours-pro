<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __("S'inscrire à un concours") }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('candidatures.store') }}" class="space-y-6"
                      x-data="{ concours: null, dateNaissance: '{{ auth()->user()->date_naissance?->toDateString() }}' }">
                    @csrf

                    <div>
                        <x-input-label for="concours_id" :value="__('Concours')" />
                        <select id="concours_id" name="concours_id" x-model="concours"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                            <option value="">{{ __('— Choisir un concours —') }}</option>
                            @foreach ($concoursOuverts as $c)
                                <option value="{{ $c->id }}">
                                    {{ $c->nom }} ({{ $c->cycle }} · {{ $c->filiere }}) — diplôme requis : {{ $c->diplome_requis }}, âge {{ $c->age_min }}-{{ $c->age_max }} ans
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('concours_id')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="diplome_candidat" :value="__('Votre diplôme')" />
                        <x-text-input id="diplome_candidat" name="diplome_candidat" type="text" class="mt-1 block w-full"
                                      :value="old('diplome_candidat')" required />
                        <p class="mt-1 text-xs text-gray-500">{{ __('Doit correspondre exactement au diplôme requis par le concours choisi.') }}</p>
                        <x-input-error :messages="$errors->get('diplome_candidat')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-end">
                        <x-primary-button>{{ __('Soumettre la candidature') }}</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
