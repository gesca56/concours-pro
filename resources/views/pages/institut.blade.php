@php
    $institut = config('ipnetp.institut');
    $missions = config('ipnetp.missions');
    $cycles = config('ipnetp.cycles');
@endphp

<x-public-layout titre="L'IPNETP" description="Présentation de l'Institut Pédagogique National de l'Enseignement Technique et Professionnel : missions, formations et services.">

    <section class="bg-fond border-b border-gray-100">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 py-16">
            <p class="text-xs font-bold tracking-wider text-institutionnel uppercase mb-3">L'institut</p>
            <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-marine mb-5">{{ $institut['nom'] }}</h1>
            <p class="text-lg text-gray-600 leading-relaxed">
                Établissement public placé sous la tutelle du {{ $institut['tutelle'] }}, l'IPNETP forme
                à {{ $institut['ville'] }} les enseignants et les instructeurs des lycées professionnels,
                collèges d'enseignement technique et centres de formation professionnelle de Côte d'Ivoire.
            </p>
            <blockquote class="mt-8 pl-4 border-l-4 border-institutionnel text-marine font-medium italic">
                « {{ $institut['devise'] }} »
            </blockquote>
        </div>
    </section>

    {{-- Missions --}}
    <section class="max-w-6xl mx-auto px-4 sm:px-6 py-16">
        <h2 class="text-2xl font-bold text-marine mb-8">Ses missions</h2>
        <div class="grid sm:grid-cols-2 gap-6">
            @foreach ($missions as $i => $mission)
                <div class="p-6 rounded-2xl border border-gray-100 bg-white">
                    <p class="font-mono text-sm font-bold text-institutionnel mb-2">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</p>
                    <h3 class="font-semibold text-marine mb-2">{{ $mission['titre'] }}</h3>
                    <p class="text-sm text-gray-600 leading-relaxed">{{ $mission['texte'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Organisation du recrutement --}}
    <section class="bg-marine text-slate-300">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 py-16 grid lg:grid-cols-2 gap-12 items-start">
            <div>
                <h2 class="text-2xl font-bold text-white mb-4">Comment l'institut recrute</h2>
                <p class="leading-relaxed mb-4">
                    L'entrée à l'IPNETP se fait par <strong class="text-white">concours direct</strong>, ouvert chaque
                    année par communiqué du ministère. La session comporte une phase d'admissibilité (épreuves
                    écrites) et une phase d'admission (entretien oral devant jury).
                </p>
                <p class="leading-relaxed mb-4">
                    Lors de la session 2026, <strong class="text-white">411 candidats</strong> déclarés admissibles à
                    l'écrit se sont présentés aux oraux, ouverts le 21 août — preuve du volume de dossiers que
                    le secrétariat des concours doit traiter chaque année.
                </p>
                <p class="leading-relaxed">
                    Les élèves-professeurs admis suivent une formation pédagogique sanctionnée par un
                    Certificat d'Aptitude Pédagogique, <strong class="text-white">sans frais de scolarité</strong>,
                    avant d'être affectés dans un établissement public de l'ETFP.
                </p>
            </div>
            <div class="rounded-2xl bg-white/5 border border-white/10 p-6">
                <h3 class="text-white font-semibold mb-4">Ce que Concours-Pro change pour le secrétariat</h3>
                <ul class="space-y-3 text-sm">
                    @foreach ([
                        'Des dossiers reçus déjà complets, pièce par pièce, avec rejet motivé en ligne.',
                        'Des paiements tracés et des reçus générés automatiquement, sans file d\'attente à la caisse.',
                        'Un contrôle d\'âge et de diplôme automatique, conforme au communiqué.',
                        'Des numéros d\'anonymat attribués en un clic et une délibération sur seuil.',
                        'Des convocations vérifiables par QR Code à l\'entrée des salles.',
                    ] as $point)
                        <li class="flex gap-3">
                            <svg class="w-5 h-5 text-institutionnel shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                            <span>{{ $point }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </section>

    {{-- Formations --}}
    <section class="max-w-6xl mx-auto px-4 sm:px-6 py-16">
        <h2 class="text-2xl font-bold text-marine mb-2">Les formations accessibles par concours</h2>
        <p class="text-gray-500 mb-8">Quatre corps d'enseignants et d'instructeurs, du lycée au centre de formation professionnelle de base.</p>
        <div class="overflow-x-auto rounded-2xl border border-gray-100">
            <table class="w-full text-sm text-left">
                <thead class="bg-fond text-gray-500">
                    <tr>
                        <th class="px-4 py-3 font-medium">Concours</th>
                        <th class="px-4 py-3 font-medium">Forme au métier de</th>
                        <th class="px-4 py-3 font-medium">Niveau d'entrée</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($cycles as $code => $cycle)
                        <tr>
                            <td class="px-4 py-3 font-semibold text-marine whitespace-nowrap">
                                <span class="inline-block w-2.5 h-2.5 rounded-sm mr-1.5 {{ $cycle['chemise']['classe'] }}"></span>{{ $code }}
                            </td>
                            <td class="px-4 py-3 text-gray-700">{{ $cycle['intitule'] }}</td>
                            <td class="px-4 py-3 text-gray-700">{{ $cycle['niveau'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>

    {{-- Contact --}}
    <section class="max-w-6xl mx-auto px-4 sm:px-6 pb-20">
        <div class="grid md:grid-cols-3 gap-6">
            <div class="p-6 rounded-2xl bg-fond">
                <h3 class="font-semibold text-marine mb-2">Où déposer son dossier</h3>
                <p class="text-sm text-gray-600">{{ $institut['secretariat'] }}, {{ $institut['ville'] }}.</p>
            </div>
            <div class="p-6 rounded-2xl bg-fond">
                <h3 class="font-semibold text-marine mb-2">Nous joindre</h3>
                <p class="text-sm text-gray-600">{{ implode(' / ', $institut['telephones']) }}<br>{{ $institut['email'] }}</p>
            </div>
            <div class="p-6 rounded-2xl bg-fond">
                <h3 class="font-semibold text-marine mb-2">Horaires</h3>
                <p class="text-sm text-gray-600">{{ $institut['horaires'] }}<br>{{ $institut['adresse_postale'] }}</p>
            </div>
        </div>
    </section>

</x-public-layout>
