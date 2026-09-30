@php
    // Nom abrégé pour la publication : premier mot complet, initiales pour la suite.
    $abreger = function (string $nom): string {
        $mots = preg_split('/\s+/', trim($nom));
        $premier = array_shift($mots);

        return trim($premier.' '.implode(' ', array_map(fn ($m) => mb_substr($m, 0, 1).'.', $mots)));
    };
@endphp

<x-public-layout titre="Résultats" description="Résultats définitifs des concours directs d'entrée à l'IPNETP publiés sur Concours-Pro.">

    <section class="bg-fond border-b border-gray-100">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 py-16">
            <p class="text-xs font-bold tracking-wider text-institutionnel uppercase mb-3">Résultats définitifs</p>
            <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-marine mb-5">Listes des admis</h1>
            <p class="text-lg text-gray-600 leading-relaxed">
                Les listes sont publiées après délibération du jury. Par discrétion, seuls le numéro de dossier
                et le nom abrégé apparaissent : votre résultat détaillé est consultable dans votre espace candidat.
            </p>
        </div>
    </section>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 py-16 space-y-10" x-data="{ recherche: '' }">
        @if ($concoursTermines->isNotEmpty())
            <label class="block">
                <span class="text-sm font-medium text-marine">Rechercher votre numéro de dossier</span>
                <input type="search" x-model.trim="recherche" inputmode="numeric" placeholder="Ex. 00042"
                       class="mt-1 block w-full sm:w-72 border-gray-300 rounded-lg shadow-sm">
            </label>
        @endif

        @forelse ($concoursTermines as $c)
            @php
                $cycle = config('ipnetp.cycles.'.$c->cycle);
                $admis = $c->candidatures;
                $taux = $c->notees_count ? round($admis->count() / $c->notees_count * 100) : 0;
            @endphp
            <section class="rounded-2xl border border-gray-100 overflow-hidden">
                <header class="p-6 bg-white flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold text-institutionnel flex items-center gap-1.5">
                            @if ($cycle)<span class="w-2.5 h-2.5 rounded-sm {{ $cycle['chemise']['classe'] }}"></span>@endif
                            {{ $c->cycle }} · {{ $cycle['intitule'] ?? '' }}
                        </p>
                        <h2 class="text-lg font-bold text-marine mt-1">{{ $c->nom }}</h2>
                    </div>
                    <dl class="flex gap-6 text-sm">
                        <div><dt class="text-xs text-gray-500">Ont composé</dt><dd class="font-semibold text-marine">{{ $c->notees_count }}</dd></div>
                        <div><dt class="text-xs text-gray-500">Admis</dt><dd class="font-semibold text-marine">{{ $admis->count() }}</dd></div>
                        <div><dt class="text-xs text-gray-500">Taux</dt><dd class="font-semibold text-marine">{{ $taux }} %</dd></div>
                    </dl>
                </header>
                @if ($admis->isNotEmpty())
                    <ol class="divide-y divide-gray-100 border-t border-gray-100 bg-fond/50">
                        @foreach ($admis as $candidature)
                            @php $numero = str_pad($candidature->id, 5, '0', STR_PAD_LEFT); @endphp
                            <li class="px-6 py-2.5 flex justify-between text-sm"
                                x-show="recherche === '' || '{{ $numero }}'.includes(recherche)"
                                :class="recherche !== '' && '{{ $numero }}' === recherche.padStart(5, '0') ? 'bg-emerald-50' : ''">
                                <span class="font-mono text-gray-700">{{ $numero }}</span>
                                <span class="text-gray-900">{{ $abreger($candidature->candidat->name) }}</span>
                            </li>
                        @endforeach
                    </ol>
                @else
                    <p class="px-6 py-4 border-t border-gray-100 text-sm text-gray-500">Aucun admis pour cette session.</p>
                @endif
            </section>
        @empty
            <div class="rounded-2xl border border-dashed border-gray-300 p-10 text-center">
                <p class="font-semibold text-marine mb-1">Aucun résultat publié pour le moment</p>
                <p class="text-sm text-gray-500">Les listes d'admis apparaîtront ici après la délibération. D'après le <a href="{{ route('pages.guide') }}#calendrier" class="text-institutionnel underline">calendrier indicatif</a>, les résultats sont généralement publiés en septembre.</p>
            </div>
        @endforelse

        <p class="text-xs text-gray-500">Seule la liste officielle proclamée par le jury fait foi. En cas d'erreur matérielle, contactez le secrétariat des concours au {{ config('ipnetp.institut.telephones.0') }}.</p>
    </div>

</x-public-layout>
