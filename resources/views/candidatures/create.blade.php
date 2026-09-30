<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __("S'inscrire à un concours") }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            @php
                // Données enrichies du référentiel IPNETP pour l'assistant (diplômes admis, chemise, année de référence).
                $concoursParId = $concoursOuverts->keyBy('id')->map(fn ($c) => $c->toArray() + [
                    'diplomes_admis' => $c->diplomesAdmis(),
                    'intitule' => config('ipnetp.cycles.'.$c->cycle.'.intitule'),
                    'chemise' => config('ipnetp.cycles.'.$c->cycle.'.chemise'),
                    'annee_reference' => $c->dateReferenceAge()->year,
                ]);
            @endphp
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6"
                 x-data="candidatureWizard({{ $concoursParId->toJson() }})">

                <!-- Fil d'étapes -->
                <ol class="flex items-center w-full mb-8">
                    <template x-for="(label, index) in ['Concours', 'Diplôme', 'Récapitulatif']" :key="index">
                        <li class="flex items-center" :class="{ 'w-full': index < 2 }">
                            <div class="flex flex-col items-center shrink-0">
                                <span class="flex items-center justify-center w-8 h-8 rounded-full text-xs font-semibold"
                                      :class="step > index ? 'bg-emerald-600 text-white' : (step === index ? 'bg-institutionnel text-white' : 'bg-gray-100 text-gray-400')"
                                      x-text="index + 1"></span>
                                <span class="mt-1 text-xs text-gray-500" x-text="label"></span>
                            </div>
                            <div class="flex-1 h-0.5 mx-2" :class="step > index ? 'bg-emerald-600' : 'bg-gray-200'" x-show="index < 2"></div>
                        </li>
                    </template>
                </ol>

                <form method="POST" action="{{ route('candidatures.store') }}" @submit="submitting = true">
                    @csrf

                    <!-- Étape 1 : Concours -->
                    <div x-show="step === 0" x-cloak class="space-y-4">
                        <x-input-label for="concours_id" :value="__('Choisissez un concours')" />
                        <select id="concours_id" name="concours_id" x-model="concoursId" @change="diplome = ''"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                            <option value="">{{ __('— Choisir un concours —') }}</option>
                            @foreach ($concoursOuverts as $c)
                                <option value="{{ $c->id }}" @selected(old('concours_id') == $c->id)>{{ $c->nom }} ({{ $c->cycle }} · {{ $c->filiere }})</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('concours_id')" class="mt-2" />

                        <template x-if="concoursChoisi">
                            <div class="text-sm bg-institutionnel/5 border border-institutionnel/20 rounded-md p-4 space-y-1">
                                <p class="font-medium text-marine" x-text="concoursChoisi.intitule"></p>
                                <p><span class="text-gray-500">{{ __('Diplômes admis :') }}</span> <span x-text="concoursChoisi.diplomes_admis.join(', ')"></span></p>
                                <p><span class="text-gray-500">{{ __('Tranche d\'âge :') }}</span> <span x-text="concoursChoisi.age_min + ' - ' + concoursChoisi.age_max + ' ans au 1er janvier ' + concoursChoisi.annee_reference"></span></p>
                                <p><span class="text-gray-500">{{ __('Frais d\'inscription :') }}</span> <span x-text="Number(concoursChoisi.frais_inscription).toLocaleString('fr-FR') + ' FCFA'"></span></p>
                                <p><span class="text-gray-500">{{ __('Visite médicale :') }}</span> <span x-text="Number(concoursChoisi.frais_visite_medicale).toLocaleString('fr-FR') + ' FCFA'"></span></p>
                                <p x-show="concoursChoisi.chemise" class="flex items-center gap-2 pt-1">
                                    <span class="w-3 h-3 rounded-sm" :class="concoursChoisi.chemise?.classe"></span>
                                    <span class="text-gray-500">{{ __('Dossier physique en chemise') }}</span>
                                    <span x-text="concoursChoisi.chemise?.nom.toLowerCase()"></span>
                                </p>
                            </div>
                        </template>

                        <div class="flex justify-end pt-4">
                            <button type="button" @click="step = 1" :disabled="!concoursId"
                                    class="inline-flex items-center px-4 py-2 bg-institutionnel border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-institutionnel-hover disabled:opacity-40">
                                {{ __('Suivant') }}
                            </button>
                        </div>
                    </div>

                    <!-- Étape 2 : Diplôme -->
                    <div x-show="step === 1" x-cloak class="space-y-4">
                        <x-input-label for="diplome_candidat" :value="__('Votre diplôme')" />
                        <select id="diplome_candidat" name="diplome_candidat" x-model="diplome"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                            <option value="">{{ __('— Choisir votre diplôme —') }}</option>
                            <template x-for="d in (concoursChoisi?.diplomes_admis ?? [])" :key="d">
                                <option :value="d" x-text="d" :selected="d === diplome"></option>
                            </template>
                        </select>
                        <p class="text-xs text-gray-500">{{ __('Diplôme obtenu dans la spécialité du concours ; une copie légalisée sera exigée dans le dossier.') }}</p>
                        <p class="text-xs text-gray-500">
                            {{ __('Votre diplôme n\'apparaît pas ? Ce concours ne vous est pas ouvert, consultez') }}
                            <a href="{{ route('pages.concours') }}" target="_blank" class="text-institutionnel underline">{{ __('les quatre concours') }}</a>.
                        </p>

                        <x-input-error :messages="$errors->get('diplome_candidat')" class="mt-2" />

                        <div class="flex justify-between pt-4">
                            <button type="button" @click="step = 0" class="text-xs uppercase tracking-widest text-gray-500 hover:text-gray-700">
                                {{ __('Précédent') }}
                            </button>
                            <button type="button" @click="step = 2" :disabled="!diplome"
                                    class="inline-flex items-center px-4 py-2 bg-institutionnel border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-institutionnel-hover disabled:opacity-40">
                                {{ __('Suivant') }}
                            </button>
                        </div>
                    </div>

                    <!-- Étape 3 : Récapitulatif -->
                    <div x-show="step === 2" x-cloak class="space-y-4">
                        <h3 class="font-medium text-gray-900">{{ __('Vérifiez votre candidature') }}</h3>
                        <dl class="text-sm space-y-2 bg-gray-50 rounded-md p-4">
                            <div class="flex justify-between"><dt class="text-gray-500">{{ __('Concours') }}</dt><dd x-text="concoursChoisi?.nom"></dd></div>
                            <div class="flex justify-between"><dt class="text-gray-500">{{ __('Diplôme déclaré') }}</dt><dd x-text="diplome"></dd></div>
                        </dl>
                        <p class="text-xs text-gray-500">{{ __('En soumettant, vous certifiez l\'exactitude de ces informations. Toute fausse déclaration entraîne l\'annulation de la candidature, même après admission.') }}</p>

                        <div class="flex justify-between pt-4">
                            <button type="button" @click="step = 1" class="text-xs uppercase tracking-widest text-gray-500 hover:text-gray-700">
                                {{ __('Précédent') }}
                            </button>
                            <x-primary-button :disabled="false" x-bind:disabled="submitting">
                                <span x-show="!submitting">{{ __('Soumettre la candidature') }}</span>
                                <span x-show="submitting" x-cloak>{{ __('Envoi…') }}</span>
                            </x-primary-button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function candidatureWizard(concoursParId) {
            return {
                step: {{ $errors->has('concours_id') || $errors->has('diplome_candidat') ? 0 : 0 }},
                concoursId: '{{ old('concours_id') }}',
                diplome: '{{ old('diplome_candidat') }}',
                submitting: false,
                concoursParId: concoursParId,
                get concoursChoisi() {
                    return this.concoursId ? this.concoursParId[this.concoursId] : null;
                },
            };
        }
    </script>
</x-app-layout>
