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

            @php
                $cycleIpnetp = config('ipnetp.cycles.'.$candidature->concours->cycle);
                $typesDeposes = $candidature->documents->where('statut_verification', '!=', 'rejete')->pluck('type')->all();
                $piecesEnLigne = collect(config('ipnetp.pieces'))->whereNotNull('type');
                $nbDeposees = $piecesEnLigne->filter(fn ($p) => in_array($p['type'], $typesDeposes, true))->count();
            @endphp
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6" x-data="{ ouvert: {{ $nbDeposees < $piecesEnLigne->count() ? 'true' : 'false' }} }">
                <button type="button" @click="ouvert = !ouvert" class="w-full flex items-center justify-between text-left">
                    <div>
                        <h3 class="font-medium text-gray-900">{{ __('Dossier IPNETP') }}</h3>
                        <p class="text-xs text-gray-500">{{ $nbDeposees }} / {{ $piecesEnLigne->count() }} {{ __('pièces déposées en ligne') }}</p>
                    </div>
                    <span class="text-xs text-institutionnel" x-text="ouvert ? 'Masquer' : 'Afficher'"></span>
                </button>
                <div class="mt-3 h-1.5 rounded-full bg-gray-100 overflow-hidden">
                    <div class="h-full bg-emerald-500" style="width: {{ $piecesEnLigne->count() ? round($nbDeposees / $piecesEnLigne->count() * 100) : 0 }}%"></div>
                </div>
                <div x-show="ouvert" x-cloak class="mt-4 space-y-4">
                    <ul class="text-sm space-y-1.5">
                        @foreach (config('ipnetp.pieces') as $piece)
                            @php $ok = $piece['type'] && in_array($piece['type'], $typesDeposes, true); @endphp
                            <li class="flex items-start gap-2">
                                <span class="mt-0.5 w-4 h-4 shrink-0 rounded-full flex items-center justify-center text-[10px] {{ $ok ? 'bg-emerald-500 text-white' : 'border border-gray-300' }}">{!! $ok ? '&#10003;' : '' !!}</span>
                                <span class="{{ $ok ? 'text-gray-500' : 'text-gray-800' }}">{{ $piece['libelle'] }}
                                    @if ($piece['note'])<span class="text-xs text-gray-400">— {{ $piece['note'] }}</span>@endif
                                    @unless ($piece['type'])<span class="text-xs text-gray-400">({{ __('au dépôt physique') }})</span>@endunless
                                </span>
                            </li>
                        @endforeach
                    </ul>
                    @if ($cycleIpnetp)
                        <div class="flex items-center gap-3 p-3 rounded-md bg-gray-50 text-sm">
                            <span class="w-8 h-6 rounded shrink-0 {{ $cycleIpnetp['chemise']['classe'] }}"></span>
                            <p class="text-gray-700">{{ __('Dossier physique à remettre dans une chemise') }} <strong>{{ strtolower($cycleIpnetp['chemise']['nom']) }}</strong> {{ __('au') }} {{ config('ipnetp.institut.secretariat') }}.</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="font-medium text-gray-900 mb-4">{{ __('Pièces justificatives') }}</h3>
                @forelse ($candidature->documents as $document)
                    <div class="py-2 border-b border-gray-100 last:border-0">
                        <div class="flex items-center justify-between">
                            <div class="min-w-0">
                                <a href="{{ route('documents.fichier', $document) }}" target="_blank" class="text-sm text-institutionnel hover:text-institutionnel-hover underline break-all">{{ $document->nom_original }}</a>
                                <p class="text-xs text-gray-400">{{ $document->libelleType() }}</p>
                            </div>
                            <x-statut-badge :statut="$document->statut_verification" />
                        </div>
                        @if ($document->statut_verification === 'rejete' && $document->motif_rejet)
                            <p class="text-xs text-red-600 mt-1">{{ __('Motif du rejet :') }} {{ $document->motif_rejet }} — {{ __('déposez une nouvelle pièce ci-dessous.') }}</p>
                        @endif
                    </div>
                @empty
                    <p class="text-sm text-gray-500">{{ __('Aucun document déposé.') }}</p>
                @endforelse

                @if ($candidature->visite_medicale_programmee_le)
                    <p class="text-xs text-gray-500 mt-4 border-t border-gray-100 pt-4">{{ __('Dossier vérifié : les pièces ne peuvent plus être modifiées.') }}</p>
                @else
                <form method="POST" action="{{ route('candidatures.documents.store', $candidature) }}"
                      enctype="multipart/form-data" class="mt-4 space-y-3 border-t border-gray-100 pt-4">
                    @csrf
                    <div>
                        <x-input-label for="type" :value="__('Type de document')" />
                        <select id="type" name="type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                            @foreach (config('ipnetp.types_documents') as $valeur => $libelle)
                                <option value="{{ $valeur }}" @selected(old('type') === $valeur)>{{ $libelle }}</option>
                            @endforeach
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
                @endif
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="font-medium text-gray-900 mb-4">{{ __('Paiements') }}</h3>
                @forelse ($candidature->paiements as $paiement)
                    @php
                        $signalementEnCours = $paiement->signalements->firstWhere('statut', 'en_attente');
                    @endphp
                    <div class="py-2 border-b border-gray-100 last:border-0" x-data="{ ouvert: false }">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-700">{{ $paiement->type === 'visite_medicale' ? __('Visite médicale') : __('Inscription') }} — {{ number_format($paiement->montant, 0, ',', ' ') }} FCFA</span>
                            <div class="flex items-center gap-3">
                                @if ($paiement->statut === 'valide')
                                    <a href="{{ route('paiements.recu', $paiement) }}" target="_blank" class="text-xs text-institutionnel hover:text-institutionnel-hover">
                                        {{ __('Reçu') }}
                                    </a>
                                @endif
                                @if ($signalementEnCours)
                                    <span class="text-xs text-amber-700">{{ __('Signalement en cours') }}</span>
                                @elseif ($paiement->statut === 'valide')
                                    <button type="button" @click="ouvert = !ouvert" class="text-xs text-gray-500 hover:text-gray-700 underline">
                                        {{ __('Signaler un problème') }}
                                    </button>
                                @endif
                                <x-statut-badge :statut="$paiement->statut" />
                            </div>
                        </div>

                        @foreach ($paiement->signalements->where('statut', 'traite') as $traite)
                            <div class="mt-2 text-xs rounded-md bg-gray-50 border border-gray-100 p-3">
                                <p class="text-gray-500">{{ __('Votre signalement du') }} {{ $traite->created_at->format('d/m/Y') }} : « {{ Str::limit($traite->message, 80) }} »</p>
                                <p class="text-gray-800 mt-1"><span class="font-semibold">{{ __('Réponse du service des concours :') }}</span> {{ $traite->reponse_administration }}</p>
                            </div>
                        @endforeach

                        @if (! $signalementEnCours && $paiement->statut === 'valide')
                            <div x-show="ouvert" x-cloak class="mt-3">
                                <form method="POST" action="{{ route('paiements.signalement', $paiement) }}" class="space-y-2">
                                    @csrf
                                    <textarea name="message" rows="3" placeholder="{{ __('Décrivez le problème rencontré avec ce paiement…') }}"
                                              class="block w-full text-sm border-gray-300 rounded-md shadow-sm"></textarea>
                                    <x-input-error :messages="$errors->get('message')" class="mt-1" />
                                    <button type="submit"
                                            class="px-3 py-1.5 bg-amber-600 text-white text-xs rounded-md font-semibold hover:bg-amber-700">
                                        {{ __('Envoyer le signalement') }}
                                    </button>
                                </form>
                            </div>
                        @endif
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
