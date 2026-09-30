@php
    $conditions = config('ipnetp.conditions_generales');
    $pieces = config('ipnetp.pieces');
    $calendrier = config('ipnetp.calendrier');
    $faq = config('ipnetp.faq');
    $cycles = config('ipnetp.cycles');
    $sources = config('ipnetp.sources');
    $sommaire = ['conditions' => 'Conditions', 'dossier' => 'Constituer son dossier', 'calendrier' => 'Calendrier', 'faq' => 'Questions fréquentes'];
@endphp

<x-public-layout titre="Guide du candidat" description="Conditions d'accès, pièces du dossier, calendrier et questions fréquentes sur les concours directs de l'IPNETP.">

    <section class="bg-fond border-b border-gray-100">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 py-16">
            <p class="text-xs font-bold tracking-wider text-institutionnel uppercase mb-3">Guide du candidat</p>
            <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-marine mb-5">Tout savoir avant de s'inscrire</h1>
            <p class="text-lg text-gray-600 leading-relaxed">
                La candidature se fait en deux temps : une <strong>préinscription en ligne</strong> sur
                Concours-Pro, puis le <strong>dépôt du dossier physique</strong> au secrétariat des concours.
                Ce guide reprend les règles des communiqués officiels de l'IPNETP.
            </p>
            <nav class="mt-8 flex flex-wrap gap-2">
                @foreach ($sommaire as $ancre => $libelle)
                    <a href="#{{ $ancre }}" class="px-3 py-1.5 rounded-full bg-white border border-gray-200 text-sm font-medium text-marine hover:border-institutionnel">{{ $libelle }}</a>
                @endforeach
            </nav>
        </div>
    </section>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 py-16 space-y-20">

        {{-- Conditions --}}
        <section id="conditions" class="scroll-mt-24">
            <h2 class="text-2xl font-bold text-marine mb-6">1. Conditions d'accès</h2>
            <ul class="space-y-3">
                @foreach ($conditions as $condition)
                    <li class="flex gap-3 text-gray-700">
                        <svg class="w-5 h-5 mt-0.5 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                        <span>{{ $condition }}</span>
                    </li>
                @endforeach
            </ul>
            <div class="mt-6 p-4 rounded-xl bg-institutionnel/5 border border-institutionnel/20 text-sm text-gray-700">
                <strong class="text-marine">Exemple pour la session {{ now()->year }} :</strong>
                il faut être né entre le 2 janvier {{ now()->year - config('ipnetp.age_max') - 1 }} et le
                1er janvier {{ now()->year - config('ipnetp.age_min') }}. Concours-Pro calcule votre âge
                à cette date à partir de votre profil.
            </div>
        </section>

        {{-- Dossier --}}
        <section id="dossier" class="scroll-mt-24">
            <h2 class="text-2xl font-bold text-marine mb-2">2. Constituer son dossier</h2>
            <p class="text-gray-500 mb-6">Déposez chaque pièce scannée depuis votre candidature (JPG, PNG ou PDF, 8 Mo max), puis apportez les originaux ou copies légalisées au secrétariat.</p>
            <ol class="divide-y divide-gray-100 rounded-2xl border border-gray-100">
                @foreach ($pieces as $i => $piece)
                    <li class="flex gap-4 p-4">
                        <span class="shrink-0 w-7 h-7 rounded-full bg-fond text-marine text-xs font-bold flex items-center justify-center">{{ $i + 1 }}</span>
                        <div class="flex-1">
                            <p class="font-medium text-marine">{{ $piece['libelle'] }}</p>
                            @if ($piece['note'])
                                <p class="text-sm text-gray-500">{{ $piece['note'] }}</p>
                            @endif
                        </div>
                        <span class="shrink-0 self-center text-xs px-2 py-0.5 rounded-full {{ $piece['type'] ? 'bg-emerald-50 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">
                            {{ $piece['type'] ? 'Dépôt en ligne' : 'Au dépôt physique' }}
                        </span>
                    </li>
                @endforeach
            </ol>

            <h3 class="font-semibold text-marine mt-10 mb-3">La couleur de votre chemise</h3>
            <p class="text-sm text-gray-600 mb-4">Le dossier physique se remet dans une chemise cartonnée dont la couleur identifie le concours — un dossier dans la mauvaise chemise risque d'être mal orienté.</p>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                @foreach ($cycles as $code => $cycle)
                    <div class="rounded-xl border border-gray-100 p-4 text-center">
                        <div class="mx-auto w-12 h-9 rounded-md mb-2 {{ $cycle['chemise']['classe'] }}"></div>
                        <p class="text-sm font-semibold text-marine">{{ $code }}</p>
                        <p class="text-xs text-gray-500">{{ $cycle['chemise']['nom'] }}</p>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- Calendrier --}}
        <section id="calendrier" class="scroll-mt-24">
            <h2 class="text-2xl font-bold text-marine mb-2">3. Calendrier indicatif d'une session</h2>
            <p class="text-gray-500 mb-8">Observé sur les dernières sessions ; les dates exactes sont fixées chaque année par communiqué.</p>
            <ol class="relative border-l-2 border-gray-100 ml-3 space-y-8">
                @foreach ($calendrier as $etape)
                    <li class="pl-8 relative">
                        <span class="absolute -left-[9px] top-1 w-4 h-4 rounded-full bg-white border-2 border-institutionnel"></span>
                        <p class="text-xs font-bold uppercase tracking-wider text-institutionnel">{{ $etape['periode'] }}</p>
                        <p class="font-semibold text-marine">{{ $etape['etape'] }}</p>
                        <p class="text-sm text-gray-600">{{ $etape['detail'] }}</p>
                    </li>
                @endforeach
            </ol>
        </section>

        {{-- FAQ --}}
        <section id="faq" class="scroll-mt-24">
            <h2 class="text-2xl font-bold text-marine mb-6">4. Questions fréquentes</h2>
            <div class="divide-y divide-gray-100 rounded-2xl border border-gray-100">
                @foreach ($faq as $item)
                    <details class="group p-5">
                        <summary class="flex justify-between items-center gap-4 cursor-pointer list-none font-medium text-marine">
                            {{ $item['q'] }}
                            <svg class="w-5 h-5 text-gray-400 shrink-0 transition group-open:rotate-45" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M12 5v14M5 12h14" /></svg>
                        </summary>
                        <p class="mt-3 text-sm text-gray-600 leading-relaxed">{{ $item['r'] }}</p>
                    </details>
                @endforeach
            </div>
        </section>

        {{-- Sources --}}
        <section class="rounded-2xl bg-fond p-6 text-sm">
            <h2 class="font-semibold text-marine mb-3">Sources consultées</h2>
            <ul class="space-y-1.5 text-gray-600">
                @foreach ($sources as $source)
                    <li><a href="{{ $source['url'] }}" target="_blank" rel="noopener" class="text-institutionnel hover:underline">{{ $source['libelle'] }} ↗</a></li>
                @endforeach
            </ul>
            <p class="mt-3 text-xs text-gray-500">Ces informations ont été rassemblées pour la conception de Concours-Pro ; en cas de différence, le communiqué officiel de la session prévaut.</p>
        </section>
    </div>

</x-public-layout>
