<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $candidature->candidat->name }} — {{ $candidature->concours->nom }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-emerald-50 text-emerald-800 text-sm p-4 rounded-md">{{ session('status') }}</div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="font-medium text-gray-900">{{ __('Dossier') }}</h3>
                    <x-statut-badge :statut="$candidature->statut" />
                </div>
                <dl class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <dt class="text-gray-500">{{ __('Candidat') }}</dt>
                        <dd class="text-gray-900">{{ $candidature->candidat->name }} ({{ $candidature->candidat->email }})</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">{{ __('Diplôme déclaré') }}</dt>
                        <dd class="text-gray-900">{{ $candidature->diplome_candidat }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">{{ __('Âge au 1er janvier') }} {{ $candidature->concours->dateReferenceAge()->year }}</dt>
                        <dd class="text-gray-900">{{ $candidature->candidat->date_naissance ? (int) $candidature->candidat->date_naissance->diffInYears($candidature->concours->dateReferenceAge()).' ans' : '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">{{ __('N° de dossier') }}</dt>
                        <dd class="text-gray-900">{{ str_pad($candidature->id, 5, '0', STR_PAD_LEFT) }}</dd>
                    </div>
                </dl>
            </div>

            @php
                $manquantes = $candidature->piecesManquantes();
                $cycleIpnetp = config('ipnetp.cycles.'.$candidature->concours->cycle);
            @endphp
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="font-medium text-gray-900">{{ __('Complétude du dossier IPNETP') }}</h3>
                    @if ($cycleIpnetp)
                        <span class="inline-flex items-center gap-2 text-xs text-gray-600">
                            <span class="w-5 h-3.5 rounded-sm {{ $cycleIpnetp['chemise']['classe'] }}"></span>{{ __('Chemise') }} {{ strtolower($cycleIpnetp['chemise']['nom']) }}
                        </span>
                    @endif
                </div>
                @if (count($manquantes) === 0)
                    <p class="text-sm text-emerald-700 bg-emerald-50 rounded-md p-3">{{ __('Toutes les pièces obligatoires ont été déposées en ligne.') }}</p>
                @else
                    <p class="text-sm text-amber-800 bg-amber-50 rounded-md p-3 mb-2">{{ count($manquantes) }} {{ __('pièce(s) obligatoire(s) non déposée(s) :') }}</p>
                    <ul class="text-sm text-gray-700 list-disc ml-5 space-y-0.5">
                        @foreach ($manquantes as $piece)
                            <li>{{ $piece }}</li>
                        @endforeach
                    </ul>
                @endif
                <p class="text-xs text-gray-500 mt-3">{{ __('Rappel : casier judiciaire de moins de 3 mois, diplôme en copie légalisée, nom identique à la CNI.') }}</p>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="font-medium text-gray-900 mb-4">{{ __('Pièces justificatives') }}</h3>
                @forelse ($candidature->documents as $document)
                    <div class="py-3 border-b border-gray-100 last:border-0 space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-700">
                                <a href="{{ route('documents.fichier', $document) }}" target="_blank" class="text-institutionnel hover:text-institutionnel-hover underline">{{ $document->nom_original }}</a>
                                <span class="text-gray-400">({{ $document->libelleType() }})</span>
                            </span>
                            <x-statut-badge :statut="$document->statut_verification" />
                        </div>
                        @if ($document->statut_verification === 'en_attente')
                            <form method="POST" action="{{ route('receptionniste.documents.update', $document) }}"
                                  class="flex items-center gap-2" x-data="{ rejet: false }">
                                @csrf
                                @method('PATCH')
                                <button type="submit" name="statut_verification" value="valide" x-show="! rejet"
                                        class="px-3 py-1 bg-emerald-600 text-white text-xs rounded-md font-semibold hover:bg-emerald-700">
                                    {{ __('Valider') }}
                                </button>
                                <button type="button" @click="rejet = !rejet"
                                        class="px-3 py-1 bg-red-600 text-white text-xs rounded-md font-semibold hover:bg-red-700">
                                    {{ __('Rejeter') }}
                                </button>
                                <template x-if="rejet">
                                    <div class="flex items-center gap-2 flex-1">
                                        <input type="text" name="motif_rejet" placeholder="{{ __('Motif du rejet') }}"
                                               class="flex-1 text-xs border-gray-300 rounded-md">
                                        <input type="hidden" name="statut_verification" value="rejete">
                                        <button type="submit" class="px-3 py-1 bg-red-600 text-white text-xs rounded-md font-semibold hover:bg-red-700">
                                            {{ __('Confirmer') }}
                                        </button>
                                    </div>
                                </template>
                            </form>
                        @elseif ($document->statut_verification === 'rejete' && $document->motif_rejet)
                            <p class="text-xs text-red-600">{{ __('Motif :') }} {{ $document->motif_rejet }}</p>
                        @endif
                    </div>
                @empty
                    <p class="text-sm text-gray-500">{{ __("Aucun document déposé.") }}</p>
                @endforelse
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="font-medium text-gray-900 mb-4">{{ __('Visite médicale') }}</h3>
                @if ($candidature->visite_medicale_programmee_le)
                    <p class="text-sm text-emerald-700">
                        {{ __('Visite médicale programmée le') }} {{ $candidature->visite_medicale_programmee_le->format('d/m/Y à H:i') }}.
                    </p>
                @else
                    @php
                        $toutesValidees = $candidature->piecesVerifiees();
                    @endphp
                    <form method="POST" action="{{ route('receptionniste.candidatures.visite-medicale', $candidature) }}">
                        @csrf
                        <button type="submit" @disabled(! $toutesValidees)
                                class="px-4 py-2 bg-institutionnel text-white rounded-md font-semibold text-xs uppercase tracking-widest hover:bg-institutionnel-hover disabled:opacity-40">
                            {{ __('Programmer la visite médicale') }}
                        </button>
                        @unless ($toutesValidees)
                            <p class="text-xs text-gray-500 mt-2">{{ __('Vérifiez toutes les pièces en attente (au moins une doit être validée).') }}</p>
                        @endunless
                    </form>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
