@php
    $cycles = config('ipnetp.cycles');
    $epreuves = config('ipnetp.epreuves');
@endphp

<x-public-layout titre="Les concours" description="Les quatre concours directs d'entrée à l'IPNETP : diplômes requis, spécialités, épreuves et sessions ouvertes.">

    <section class="bg-fond border-b border-gray-100">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 py-16">
            <p class="text-xs font-bold tracking-wider text-institutionnel uppercase mb-3">Concours directs d'entrée</p>
            <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-marine mb-5">Quel concours présenter ?</h1>
            <p class="text-lg text-gray-600 leading-relaxed">
                Le concours se choisit d'après votre plus haut diplôme <em>dans la spécialité</em> que vous
                souhaitez enseigner. Tous partagent les mêmes conditions générales : nationalité ivoirienne
                et âge compris entre {{ config('ipnetp.age_min') }} et {{ config('ipnetp.age_max') }} ans au 1er janvier.
            </p>
            <nav class="mt-8 flex flex-wrap gap-2">
                @foreach ($cycles as $code => $cycle)
                    <a href="#{{ Str::slug($code) }}" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white border border-gray-200 text-sm font-medium text-marine hover:border-institutionnel">
                        <span class="w-2.5 h-2.5 rounded-sm {{ $cycle['chemise']['classe'] }}"></span>{{ $code }}
                    </a>
                @endforeach
                <a href="#sessions" class="px-3 py-1.5 rounded-full bg-institutionnel text-white text-sm font-medium">Sessions ouvertes ({{ $concoursOuverts->count() }})</a>
            </nav>
        </div>
    </section>

    {{-- Fiches concours --}}
    <section class="max-w-6xl mx-auto px-4 sm:px-6 py-16 space-y-8">
        @foreach ($cycles as $code => $cycle)
            <article id="{{ Str::slug($code) }}" class="scroll-mt-24 rounded-2xl border border-gray-100 bg-white overflow-hidden">
                <div class="flex">
                    <span class="w-1.5 shrink-0 {{ $cycle['chemise']['classe'] }}"></span>
                    <div class="flex-1 p-6 sm:p-8 grid lg:grid-cols-3 gap-8">
                        <div class="lg:col-span-2">
                            <p class="text-xs font-bold tracking-wider text-institutionnel mb-1">{{ $code }}</p>
                            <h2 class="text-xl sm:text-2xl font-bold text-marine mb-1">{{ $cycle['intitule'] }}</h2>
                            <p class="text-sm text-gray-500 mb-6">{{ $cycle['certificat'] }}</p>

                            <h3 class="text-sm font-semibold text-marine mb-2">Diplômes admis ({{ $cycle['niveau'] }})</h3>
                            <ul class="flex flex-wrap gap-2 mb-6">
                                @foreach ($cycle['diplomes'] as $diplome)
                                    <li class="px-2.5 py-1 rounded-md bg-institutionnel/10 text-institutionnel text-sm font-medium">{{ $diplome }}</li>
                                @endforeach
                            </ul>

                            <h3 class="text-sm font-semibold text-marine mb-2">Exemples de spécialités</h3>
                            <p class="text-sm text-gray-600 leading-relaxed">{{ implode(' · ', $cycle['specialites']) }}</p>
                        </div>
                        <dl class="text-sm space-y-4 lg:border-l lg:border-gray-100 lg:pl-8">
                            <div>
                                <dt class="text-gray-500">Chemise du dossier physique</dt>
                                <dd class="font-medium text-marine flex items-center gap-2 mt-1"><span class="w-4 h-4 rounded {{ $cycle['chemise']['classe'] }}"></span>{{ $cycle['chemise']['nom'] }}</dd>
                            </div>
                            <div>
                                <dt class="text-gray-500">Épreuve de spécialité</dt>
                                <dd class="font-medium text-marine mt-1">{{ $cycle['duree_specialite'] }} · coefficient 5</dd>
                            </div>
                            <div>
                                <dt class="text-gray-500">Frais d'inscription</dt>
                                <dd class="font-medium text-marine mt-1">{{ number_format(config('ipnetp.frais_inscription'), 0, ',', ' ') }} FCFA</dd>
                            </div>
                            <div>
                                <dt class="text-gray-500">Sessions ouvertes</dt>
                                <dd class="font-medium text-marine mt-1">{{ $concoursOuverts->where('cycle', $code)->count() }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </article>
        @endforeach
    </section>

    {{-- Épreuves --}}
    <section class="bg-marine">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 py-16">
            <h2 class="text-2xl font-bold text-white mb-2">Les épreuves</h2>
            <p class="text-slate-400 mb-8">Identiques pour les quatre concours, seule la durée de l'épreuve de spécialité varie.</p>
            <div class="grid md:grid-cols-3 gap-5">
                @foreach ($epreuves as $epreuve)
                    <div class="rounded-2xl bg-white/5 border border-white/10 p-6">
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-xs uppercase tracking-wider text-institutionnel font-bold">{{ $epreuve['phase'] }}</span>
                            <span class="text-xs px-2 py-0.5 rounded-full bg-white/10 text-white">coef. {{ $epreuve['coefficient'] }}</span>
                        </div>
                        <h3 class="text-white font-semibold mb-2">{{ $epreuve['nom'] }}</h3>
                        <p class="text-sm text-slate-300 leading-relaxed">{{ $epreuve['detail'] }}</p>
                        @if ($epreuve['duree'])
                            <p class="text-xs text-slate-400 mt-3">Durée : {{ $epreuve['duree'] }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
            <p class="text-sm text-slate-400 mt-8 max-w-3xl">
                Les copies sont corrigées sous <strong class="text-slate-200">numéro d'anonymat</strong>. Seuls les
                candidats admissibles à l'écrit sont convoqués à l'entretien oral ; la délibération finale
                compare la moyenne pondérée au seuil d'admission fixé par le jury.
            </p>
        </div>
    </section>

    {{-- Sessions ouvertes --}}
    <section id="sessions" class="scroll-mt-24 max-w-6xl mx-auto px-4 sm:px-6 py-16">
        <h2 class="text-2xl font-bold text-marine mb-2">Sessions ouvertes</h2>
        <p class="text-gray-500 mb-8">Préinscription possible jusqu'à la date de clôture indiquée.</p>
        @if ($concoursOuverts->isNotEmpty())
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($concoursOuverts as $c)
                    @include('pages.partials.carte-concours', ['c' => $c])
                @endforeach
            </div>
        @else
            <div class="rounded-2xl border border-dashed border-gray-300 p-10 text-center">
                <p class="font-semibold text-marine mb-1">Aucune session ouverte pour le moment</p>
                <p class="text-sm text-gray-500">Consultez le <a href="{{ route('pages.guide') }}#calendrier" class="text-institutionnel underline">calendrier indicatif</a> pour anticiper la prochaine ouverture.</p>
            </div>
        @endif
    </section>

</x-public-layout>
