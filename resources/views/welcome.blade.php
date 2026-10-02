@php
    $cycles = config('ipnetp.cycles');
    $parcours = [
        ['t' => 'Préinscription en ligne', 'd' => 'Créez votre compte, choisissez votre concours : l\'âge (au 1er janvier) et le diplôme sont contrôlés automatiquement.'],
        ['t' => 'Paiement Mobile Money', 'd' => 'Réglez les 25 000 FCFA d\'inscription puis la visite médicale, et téléchargez vos reçus.'],
        ['t' => 'Dépôt du dossier', 'd' => 'Déposez vos pièces en ligne puis le dossier physique au secrétariat, dans la chemise de couleur de votre concours.'],
        ['t' => 'Vérification & visite médicale', 'd' => 'Le secrétariat contrôle chaque pièce, puis le médecin se prononce sur votre aptitude.'],
        ['t' => 'Écrits sous anonymat', 'd' => 'Composition française et spécialité, corrigées sous numéro d\'anonymat, avec convocation à QR Code.'],
        ['t' => 'Oral & résultats', 'd' => 'Entretien devant jury pour les admissibles, puis délibération et publication des admis.'],
    ];
@endphp

<x-public-layout>

    {{-- Hero --}}
    <section class="relative overflow-hidden bg-fond">
        <div class="absolute inset-0 bg-gradient-to-br from-institutionnel/5 via-transparent to-transparent"></div>
        <div class="absolute -top-32 -right-32 w-96 h-96 bg-institutionnel/10 rounded-full blur-3xl"></div>
        <div class="absolute top-40 -left-24 w-72 h-72 bg-marine-light/10 rounded-full blur-3xl"></div>

        <div class="relative max-w-4xl mx-auto px-4 sm:px-6 pt-16 sm:pt-20 pb-16 text-center">
            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-institutionnel/10 text-institutionnel text-xs font-semibold tracking-wide uppercase mb-6">
                <span class="w-1.5 h-1.5 rounded-full bg-institutionnel {{ $concoursOuverts->isNotEmpty() ? 'animate-pulse' : '' }}"></span>
                {{ $concoursOuverts->isNotEmpty() ? 'Session '.now()->year.' · inscriptions ouvertes' : 'Concours directs d\'entrée à l\'IPNETP' }}
            </span>
            <h1 class="text-4xl sm:text-5xl font-extrabold tracking-tight text-marine leading-[1.1] mb-6">
                Devenez professeur de<br class="hidden sm:block"> l'<span class="text-institutionnel">enseignement technique</span>
            </h1>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto mb-10 leading-relaxed">
                Préinscrivez-vous aux concours CAP/PL, CAP/PC, CAP/IFPB et CAP/IAFPB de l'IPNETP,
                payez en Mobile Money, suivez chaque étape de votre dossier et recevez une
                convocation sécurisée par QR Code — sans vous déplacer avant le dépôt final.
            </p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                @guest
                    <a href="{{ route('register') }}"
                       class="w-full sm:w-auto px-7 py-3.5 bg-institutionnel text-white rounded-lg font-semibold hover:bg-institutionnel-hover transition shadow-lg shadow-institutionnel/30">
                        Commencer ma préinscription
                    </a>
                @else
                    <a href="{{ route('dashboard') }}"
                       class="w-full sm:w-auto px-7 py-3.5 bg-institutionnel text-white rounded-lg font-semibold hover:bg-institutionnel-hover transition shadow-lg shadow-institutionnel/30">
                        Accéder à mon tableau de bord
                    </a>
                @endguest
                <a href="{{ route('pages.guide') }}"
                   class="w-full sm:w-auto px-7 py-3.5 bg-white border border-gray-200 text-gray-700 rounded-lg font-semibold hover:border-gray-300 hover:bg-gray-50 transition">
                    Lire le guide du candidat
                </a>
            </div>
        </div>

        {{-- Chiffres clés --}}
        <div class="relative max-w-5xl mx-auto px-4 sm:px-6 pb-16">
            <dl class="grid grid-cols-2 lg:grid-cols-4 gap-px bg-gray-200 rounded-2xl overflow-hidden border border-gray-200">
                @foreach ([
                    ['v' => '4', 'l' => 'concours directs', 'd' => 'du CAP/PL au CAP/IAFPB'],
                    ['v' => '18 – 39 ans', 'l' => 'au 1er janvier', 'd' => 'nationalité ivoirienne'],
                    ['v' => number_format(config('ipnetp.frais_inscription'), 0, ',', ' ').' F', 'l' => "frais d'inscription", 'd' => 'payés en Mobile Money'],
                    ['v' => '0 F', 'l' => 'de frais de formation', 'd' => 'pour les admis'],
                ] as $chiffre)
                    <div class="bg-white p-5 text-center">
                        <dt class="sr-only">{{ $chiffre['l'] }}</dt>
                        <dd class="text-2xl font-extrabold text-marine">{{ $chiffre['v'] }}</dd>
                        <dd class="text-sm font-medium text-gray-700">{{ $chiffre['l'] }}</dd>
                        <dd class="text-xs text-gray-400 mt-0.5">{{ $chiffre['d'] }}</dd>
                    </div>
                @endforeach
            </dl>
        </div>
    </section>

    {{-- Concours ouverts --}}
    <section id="concours-ouverts" class="max-w-6xl mx-auto px-4 sm:px-6 py-16">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-8">
            <div>
                <h2 class="text-2xl sm:text-3xl font-bold text-marine mb-2">Concours ouverts</h2>
                <p class="text-gray-500">Les sessions pour lesquelles la préinscription est actuellement possible.</p>
            </div>
            <a href="{{ route('pages.concours') }}" class="text-sm font-semibold text-institutionnel hover:text-institutionnel-hover">Tous les concours →</a>
        </div>

        @if ($concoursOuverts->isNotEmpty())
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($concoursOuverts->take(6) as $c)
                    @include('pages.partials.carte-concours', ['c' => $c])
                @endforeach
            </div>
        @else
            <div class="rounded-2xl border border-dashed border-gray-300 p-10 text-center">
                <p class="font-semibold text-marine mb-1">Aucune session ouverte pour le moment</p>
                <p class="text-sm text-gray-500">Les inscriptions ouvrent généralement en mai. Créez votre compte dès maintenant pour être prêt le jour J.</p>
            </div>
        @endif
    </section>

    {{-- Les 4 concours --}}
    <section class="bg-fond border-y border-gray-100">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 py-16">
            <div class="text-center mb-12">
                <h2 class="text-2xl sm:text-3xl font-bold text-marine mb-3">Quatre concours, quatre profils</h2>
                <p class="text-gray-500 max-w-2xl mx-auto">Chaque concours prépare à un Certificat d'Aptitude Pédagogique (CAP) et correspond à un niveau de diplôme. Au dépôt, chaque dossier se range dans une chemise de couleur.</p>
            </div>
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5">
                @foreach ($cycles as $code => $cycle)
                    <a href="{{ route('pages.concours') }}#{{ Str::slug($code) }}"
                       class="group relative p-6 rounded-2xl bg-white border border-gray-100 hover:shadow-lg hover:-translate-y-0.5 transition overflow-hidden">
                        <span class="absolute inset-x-0 top-0 h-1 {{ $cycle['chemise']['classe'] }}"></span>
                        <p class="text-xs font-bold tracking-wider text-institutionnel mb-2">{{ $code }}</p>
                        <h3 class="font-semibold text-marine leading-snug mb-3">{{ $cycle['intitule'] }}</h3>
                        <p class="text-sm text-gray-500 mb-4">{{ $cycle['niveau'] }} — {{ implode(', ', $cycle['diplomes']) }}</p>
                        <p class="text-xs text-gray-400 flex items-center gap-1.5">
                            <span class="w-3 h-3 rounded-sm {{ $cycle['chemise']['classe'] }}"></span>
                            Chemise {{ strtolower($cycle['chemise']['nom']) }}
                        </p>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Parcours --}}
    <section id="parcours" class="bg-marine">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 py-16">
            <h2 class="text-2xl sm:text-3xl font-bold text-white text-center mb-3">Le parcours du candidat</h2>
            <p class="text-slate-400 text-center max-w-2xl mx-auto mb-12">Les étapes administratives de l'IPNETP, reprises une à une dans Concours-Pro.</p>
            <ol class="grid sm:grid-cols-2 lg:grid-cols-3 gap-x-10 gap-y-10">
                @foreach ($parcours as $i => $etape)
                    <li class="flex gap-4">
                        <span class="shrink-0 w-9 h-9 rounded-full border border-institutionnel/60 text-institutionnel font-mono text-sm font-bold flex items-center justify-center">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                        <div>
                            <h3 class="text-white font-semibold mb-1.5">{{ $etape['t'] }}</h3>
                            <p class="text-slate-300 text-sm leading-relaxed">{{ $etape['d'] }}</p>
                        </div>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>

    {{-- Fonctionnalités --}}
    <section id="fonctionnalites" class="max-w-6xl mx-auto px-4 sm:px-6 py-16">
        <div class="text-center mb-12">
            <h2 class="text-2xl sm:text-3xl font-bold text-marine mb-3">Du concours à la formation, tout en ligne</h2>
            <p class="text-gray-500 max-w-xl mx-auto">Chaque service de l'institut dispose de son propre espace : réception des dossiers, service médical, correcteurs, direction des concours et équipe pédagogique.</p>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @php
                $features = [
                    ['icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', 'titre' => 'Éligibilité vérifiée', 'texte' => 'Âge apprécié au 1er janvier et diplôme contrôlé avant toute inscription.'],
                    ['icon' => 'M3 10h18M7 15h1m4 0h1m-7 4h12a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z', 'titre' => 'Paiement Mobile Money', 'texte' => 'Frais d\'inscription et visite médicale réglés en ligne, reçus en PDF.'],
                    ['icon' => 'M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z', 'titre' => 'Dossier complet', 'texte' => 'Les 10 pièces exigées par l\'IPNETP, suivies une à une avec motif en cas de rejet.'],
                    ['icon' => 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z', 'titre' => 'Anonymat & QR Code', 'texte' => 'Correction sous numéro d\'anonymat et convocation vérifiée à l\'entrée des salles.'],
                    ['icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253', 'titre' => 'Préparation en ligne', 'texte' => 'Cours de méthode et quiz d\'entraînement corrigés, gratuits pour tous les candidats.'],
                    ['icon' => 'M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422A12.083 12.083 0 0118 17.5c-2.03 1.2-4.04 1.5-6 1.5s-3.97-.3-6-1.5a12.083 12.083 0 01-.16-6.922L12 14z', 'titre' => 'Formation des admis', 'texte' => 'Promotions, cours, devoirs notés, emploi du temps et bulletin pour les élèves-professeurs.'],
                ];
            @endphp

            @foreach ($features as $f)
                <div class="p-6 rounded-2xl border border-gray-100 bg-white hover:shadow-lg hover:-translate-y-0.5 transition">
                    <div class="w-11 h-11 rounded-xl bg-institutionnel/10 flex items-center justify-center mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-institutionnel" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $f['icon'] }}" />
                        </svg>
                    </div>
                    <h3 class="font-semibold text-marine mb-1.5">{{ $f['titre'] }}</h3>
                    <p class="text-sm text-gray-500 leading-relaxed">{{ $f['texte'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Appel final --}}
    <section class="max-w-4xl mx-auto px-4 sm:px-6 pb-20">
        <div class="rounded-3xl bg-gradient-to-br from-institutionnel to-marine-light p-10 text-center text-white">
            <h2 class="text-2xl sm:text-3xl font-bold mb-3">Préparez votre dossier dès aujourd'hui</h2>
            <p class="text-blue-100 mb-8 max-w-xl mx-auto">Extrait de naissance, casier judiciaire de moins de 3 mois, certificat de non-bégaiement… consultez la liste complète des pièces avant l'ouverture.</p>
            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                <a href="{{ route('pages.guide') }}#dossier" class="px-7 py-3 bg-white text-marine rounded-lg font-semibold hover:bg-blue-50 transition">Voir les pièces à fournir</a>
                @guest
                    <a href="{{ route('register') }}" class="px-7 py-3 border border-white/40 rounded-lg font-semibold hover:bg-white/10 transition">Créer mon compte</a>
                @endguest
            </div>
        </div>
    </section>

</x-public-layout>
