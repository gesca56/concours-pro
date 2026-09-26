<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $candidature->concours->nom }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-emerald-50 text-emerald-800 text-sm p-4 rounded-md">{{ session('status') }}</div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="font-medium text-gray-900">{{ __('Statut du dossier') }}</h3>
                    <x-statut-badge :statut="$candidature->statut" />
                </div>

                <dl class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <dt class="text-gray-500">{{ __('Concours') }}</dt>
                        <dd class="text-gray-900">{{ $candidature->concours->cycle }} · {{ $candidature->concours->filiere }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">{{ __('Diplôme déclaré') }}</dt>
                        <dd class="text-gray-900">{{ $candidature->diplome_candidat }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">{{ __('Numéro d\'anonymat') }}</dt>
                        <dd class="text-gray-900">{{ $candidature->numero_anonymat ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">{{ __('Date de soumission') }}</dt>
                        <dd class="text-gray-900">{{ $candidature->date_soumission?->format('d/m/Y') }}</dd>
                    </div>
                </dl>

                <div class="flex gap-4">
                    <a href="{{ route('candidatures.fiche', $candidature) }}" target="_blank"
                       class="inline-flex items-center text-sm text-institutionnel hover:text-institutionnel-hover font-medium">
                        {{ __('Fiche de candidature') }}
                    </a>
                    @if ($candidature->jeton_convocation)
                        <a href="{{ route('candidatures.convocation', $candidature) }}" target="_blank"
                           class="inline-flex items-center text-sm text-institutionnel hover:text-institutionnel-hover font-medium">
                            {{ __('Convocation (QR Code)') }}
                        </a>
                    @endif
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="font-medium text-gray-900 mb-4">{{ __('Pièces justificatives') }}</h3>
                @forelse ($candidature->documents as $document)
                    <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-0">
                        <span class="text-sm text-gray-700">{{ $document->nom_original }}</span>
                        <x-statut-badge :statut="$document->statut_verification" />
                    </div>
                @empty
                    <p class="text-sm text-gray-500">{{ __('Aucun document déposé.') }}</p>
                @endforelse

                <form method="POST" action="{{ route('candidatures.documents.store', $candidature) }}"
                      enctype="multipart/form-data" class="mt-4 space-y-3 border-t border-gray-100 pt-4">
                    @csrf
                    <div>
                        <x-input-label for="type" :value="__('Type de document')" />
                        <select id="type" name="type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                            <option value="acte_naissance">{{ __('Acte de naissance') }}</option>
                            <option value="diplome">{{ __('Diplôme') }}</option>
                            <option value="photo_identite">{{ __("Photo d'identité") }}</option>
                            <option value="certificat_medical">{{ __('Certificat médical') }}</option>
                            <option value="piece_identite">{{ __("Pièce d'identité") }}</option>
                            <option value="autre">{{ __('Autre') }}</option>
                        </select>
                    </div>
                    <div>
                        <x-input-label for="fichier" :value="__('Fichier (JPG, PNG ou PDF, 8 Mo max)')" />
                        <input id="fichier" name="fichier" type="file" accept=".jpg,.jpeg,.png,.pdf"
                               class="mt-1 block w-full text-sm" required>
                        <x-input-error :messages="$errors->get('fichier')" class="mt-2" />
                    </div>
                    <x-primary-button>{{ __('Déposer le document') }}</x-primary-button>
                </form>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="font-medium text-gray-900 mb-4">{{ __('Paiements') }}</h3>
                @forelse ($candidature->paiements as $paiement)
                    <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-0">
                        <span class="text-sm text-gray-700">{{ ucfirst($paiement->type) }} — {{ number_format($paiement->montant, 0, ',', ' ') }} FCFA</span>
                        <div class="flex items-center gap-3">
                            @if ($paiement->statut === 'valide')
                                <a href="{{ route('paiements.recu', $paiement) }}" target="_blank" class="text-xs text-institutionnel hover:text-institutionnel-hover">
                                    {{ __('Reçu') }}
                                </a>
                            @endif
                            <x-statut-badge :statut="$paiement->statut" />
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">{{ __('Aucun paiement enregistré.') }}</p>
                @endforelse

                @php
                    $typesRestants = collect(['inscription' => $candidature->concours->frais_inscription, 'visite_medicale' => $candidature->concours->frais_visite_medicale])
                        ->except($candidature->paiements->where('statut', 'valide')->pluck('type')->all());
                @endphp

                @if ($typesRestants->isNotEmpty())
                    <form method="POST" action="{{ route('candidatures.paiements.store', $candidature) }}" class="mt-4 space-y-3 border-t border-gray-100 pt-4">
                        @csrf
                        <div>
                            <x-input-label for="type" :value="__('Type de paiement')" />
                            <select id="type" name="type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                                @foreach ($typesRestants as $type => $montant)
                                    <option value="{{ $type }}">{{ ucfirst(str_replace('_', ' ', $type)) }} — {{ number_format($montant, 0, ',', ' ') }} FCFA</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <x-input-label for="numero_telephone" :value="__('Numéro Mobile Money')" />
                            <x-text-input id="numero_telephone" name="numero_telephone" type="text" class="mt-1 block w-full" required />
                            <x-input-error :messages="$errors->get('numero_telephone')" class="mt-2" />
                        </div>
                        <x-primary-button>{{ __('Payer') }}</x-primary-button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
