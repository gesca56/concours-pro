<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Direction des concours') }}</h2>
            <p class="text-sm text-gray-500">{{ __('Pilotage des sessions de concours directs de l\'IPNETP') }}</p>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-emerald-50 text-emerald-800 text-sm p-4 rounded-md">{{ session('status') }}</div>
            @endif

            @if ($stats['signalements'] > 0)
                <a href="{{ route('administration.signalements.index') }}"
                   class="flex items-center justify-between gap-4 p-4 rounded-lg border border-amber-200 bg-amber-50 text-sm text-amber-900 hover:bg-amber-100">
                    <span><strong>{{ $stats['signalements'] }}</strong> {{ __('paiement(s) contesté(s) en attente de réponse') }}</span>
                    <span class="font-semibold">{{ __('Traiter') }} →</span>
                </a>
            @endif

            {{-- Indicateurs --}}
            <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
                @foreach ([
                    ['label' => 'Concours', 'valeur' => $stats['concours'], 'detail' => $stats['concours_ouverts'].' ouvert(s)'],
                    ['label' => 'Candidatures', 'valeur' => $stats['candidatures'], 'detail' => 'toutes sessions'],
                    ['label' => 'Admis', 'valeur' => $stats['admises'], 'detail' => $stats['candidatures'] ? round($stats['admises'] / $stats['candidatures'] * 100).' % des candidats' : '—'],
                    ['label' => 'Recettes encaissées', 'valeur' => number_format($stats['recettes'], 0, ',', ' '), 'detail' => 'FCFA, paiements validés'],
                    ['label' => 'Paiements contestés', 'valeur' => $stats['signalements'], 'detail' => 'en attente'],
                ] as $tuile)
                    <div class="bg-white rounded-lg shadow-sm p-5">
                        <p class="text-xs text-gray-500 uppercase tracking-wide">{{ $tuile['label'] }}</p>
                        <p class="text-2xl font-bold text-marine mt-1">{{ $tuile['valeur'] }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $tuile['detail'] }}</p>
                    </div>
                @endforeach
            </div>

            <div class="grid lg:grid-cols-3 gap-6">
                {{-- Répartition par corps --}}
                <section class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="font-medium text-gray-900">{{ __('Candidatures par concours') }}</h3>
                    <p class="text-xs text-gray-500 mb-5">{{ __('Nombre de dossiers par corps') }}</p>
                    @php $maxCycle = max(1, $parCycle->max() ?? 0); @endphp
                    <ul class="space-y-4">
                        @foreach (config('ipnetp.cycles') as $code => $cycle)
                            @php $n = (int) ($parCycle[$code] ?? 0); @endphp
                            <li title="{{ $code }} : {{ $n }} candidature(s)">
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="flex items-center gap-1.5 text-gray-700"><span class="w-2 h-2 rounded-sm {{ $cycle['chemise']['classe'] }}"></span>{{ $code }}</span>
                                    <span class="font-medium text-gray-900">{{ $n }}</span>
                                </div>
                                <div class="h-2 rounded bg-gray-100">
                                    <div class="h-2 rounded-r bg-institutionnel" style="width: {{ $n / $maxCycle * 100 }}%"></div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </section>

                {{-- Actions --}}
                <section class="lg:col-span-2 bg-white rounded-lg shadow-sm p-6">
                    <h3 class="font-medium text-gray-900 mb-4">{{ __('Actions rapides') }}</h3>
                    <div class="grid sm:grid-cols-3 gap-3 text-sm">
                        <a href="{{ route('administration.concours.create') }}" class="p-4 rounded-lg border border-gray-100 hover:border-institutionnel">
                            <p class="font-semibold text-marine">{{ __('Nouveau concours') }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ __('Ouvrir une session à partir du communiqué') }}</p>
                        </a>
                        <a href="{{ route('administration.signalements.index') }}" class="p-4 rounded-lg border border-gray-100 hover:border-institutionnel">
                            <p class="font-semibold text-marine">{{ __('Paiements contestés') }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ __('Répondre aux réclamations des candidats') }}</p>
                        </a>
                        <a href="{{ route('verification-qr') }}" class="p-4 rounded-lg border border-gray-100 hover:border-institutionnel">
                            <p class="font-semibold text-marine">{{ __('Vérifier un QR Code') }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ __('Contrôle des convocations le jour J') }}</p>
                        </a>
                        <a href="{{ route('administration.promotions.index') }}" class="p-4 rounded-lg border border-gray-100 hover:border-institutionnel">
                            <p class="font-semibold text-marine">{{ __('Promotions') }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ __('Inscrire les admis, emploi du temps, annonces') }}</p>
                        </a>
                        <a href="{{ route('pedagogie.dashboard') }}" class="p-4 rounded-lg border border-gray-100 hover:border-institutionnel">
                            <p class="font-semibold text-marine">{{ __('Gestion pédagogique') }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ __('Modules, cours en ligne, quiz et devoirs') }}</p>
                        </a>
                    </div>
                    <p class="text-xs text-gray-500 mt-4">
                        {{ __('Les résultats des concours terminés sont publiés automatiquement sur la') }}
                        <a href="{{ route('pages.resultats') }}" target="_blank" class="text-institutionnel underline">{{ __('page publique des résultats') }}</a>.
                    </p>
                </section>
            </div>

            {{-- Suivi par session --}}
            <section class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="p-6 pb-3">
                    <h3 class="font-medium text-gray-900">{{ __('Avancement des sessions') }}</h3>
                    <p class="text-xs text-gray-500">{{ __('Nombre de dossiers ayant franchi chaque étape') }}</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wide">
                            <tr>
                                <th class="text-left font-medium px-6 py-2">{{ __('Session') }}</th>
                                <th class="text-right font-medium px-3 py-2">{{ __('Inscrits') }}</th>
                                <th class="text-right font-medium px-3 py-2">{{ __('Éligibles') }}</th>
                                <th class="text-right font-medium px-3 py-2">{{ __('Aptes') }}</th>
                                <th class="text-right font-medium px-3 py-2">{{ __('Notés') }}</th>
                                <th class="text-right font-medium px-3 py-2">{{ __('Admis') }}</th>
                                <th class="text-right font-medium px-6 py-2">{{ __('Statut') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($concours as $c)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-3">
                                        <a href="{{ route('administration.concours.show', $c) }}" class="font-medium text-gray-900 hover:text-institutionnel">{{ $c->nom }}</a>
                                        <p class="text-xs text-gray-500">{{ $c->cycle }} · {{ __('clôture le') }} {{ $c->date_cloture->format('d/m/Y') }}</p>
                                    </td>
                                    @foreach (['candidatures_count', 'eligibles_count', 'validees_count', 'notees_count', 'admises_count'] as $colonne)
                                        <td class="px-3 py-3 text-right tabular-nums {{ $c->$colonne ? 'text-gray-900' : 'text-gray-300' }}">{{ $c->$colonne }}</td>
                                    @endforeach
                                    <td class="px-6 py-3 text-right"><x-statut-badge :statut="$c->statut->value" /></td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="px-6 py-6 text-gray-500">{{ __('Aucun concours créé.') }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
