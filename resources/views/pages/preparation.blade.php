@php
    $epreuves = config('ipnetp.epreuves');
    $conseils = [
        [
            'epreuve' => 'Composition française',
            'coef' => 3,
            'points' => [
                'Lisez le sujet deux fois et soulignez les mots-clés avant d\'écrire quoi que ce soit.',
                'Dissertation : introduction (amorce, problématique, annonce du plan), deux ou trois parties argumentées, conclusion avec ouverture.',
                'Analyse de texte : situez le texte, dégagez l\'idée générale, puis étudiez-le mouvement par mouvement.',
                'Gardez 10 minutes pour relire l\'orthographe et les accords : c\'est une épreuve de futur enseignant.',
            ],
        ],
        [
            'epreuve' => 'Épreuve de spécialité',
            'coef' => 5,
            'points' => [
                'C\'est l\'épreuve décisive : son coefficient pèse plus que le français et l\'oral réunis.',
                'Révisez les fondamentaux du programme de votre diplôme (BTS, licence, master…) plutôt que les cas rares.',
                'Entraînez-vous en temps réel : 3 h (IFPB/IAFPB) ou 4 h (PL/PC), sans interruption.',
                'Présentez vos calculs et schémas proprement : le correcteur note aussi la démarche.',
            ],
        ],
        [
            'epreuve' => 'Entretien oral',
            'coef' => 1,
            'points' => [
                'Préparez une présentation de deux minutes : parcours, diplôme, expérience, projet.',
                'Sachez expliquer pourquoi l\'enseignement technique et professionnel, et pas une carrière en entreprise.',
                'Attendez-vous à une mise en situation : comment expliqueriez-vous une notion de votre spécialité à des élèves ?',
                'Tenue correcte, ponctualité et engagement à servir dans tout établissement public : le jury y est attentif.',
            ],
        ],
    ];
    $jourJ = [
        ['t' => 'Convocation à QR Code', 'd' => 'Imprimée ou sur téléphone ; elle est scannée à l\'entrée.'],
        ['t' => 'Pièce d\'identité originale', 'd' => 'La même que celle déposée dans votre dossier.'],
        ['t' => 'Arrivez en avance', 'd' => 'Aucun candidat n\'est admis en salle après la distribution des sujets.'],
        ['t' => 'Matériel', 'd' => 'Stylos, règle ; le téléphone doit être éteint et rangé.'],
        ['t' => 'Copie anonyme', 'd' => 'N\'écrivez ni nom ni signature : seul le numéro d\'anonymat identifie la copie.'],
    ];
@endphp

<x-public-layout titre="Préparer le concours" description="Conseils de préparation aux épreuves des concours directs de l'IPNETP : composition française, spécialité, entretien oral et jour J.">

    <section class="bg-fond border-b border-gray-100">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 py-16">
            <p class="text-xs font-bold tracking-wider text-institutionnel uppercase mb-3">Préparation</p>
            <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-marine mb-5">Réussir les épreuves</h1>
            <p class="text-lg text-gray-600 leading-relaxed">
                Trois épreuves, neuf coefficients au total. Voici comment répartir votre effort, ce qui vous
                attend le jour J et ce qui se passe après l'admission.
            </p>
        </div>
    </section>

    {{-- Conseils par épreuve --}}
    <section class="max-w-6xl mx-auto px-4 sm:px-6 py-16">
        <div class="grid md:grid-cols-3 gap-6">
            @foreach ($conseils as $c)
                <article class="rounded-2xl border border-gray-100 bg-white p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="font-semibold text-marine">{{ $c['epreuve'] }}</h2>
                        <span class="text-xs px-2 py-0.5 rounded-full bg-institutionnel/10 text-institutionnel font-semibold">coef. {{ $c['coef'] }}</span>
                    </div>
                    <ul class="space-y-3 text-sm text-gray-600">
                        @foreach ($c['points'] as $point)
                            <li class="flex gap-2">
                                <span class="mt-2 w-1.5 h-1.5 rounded-full bg-institutionnel shrink-0"></span>
                                <span>{{ $point }}</span>
                            </li>
                        @endforeach
                    </ul>
                </article>
            @endforeach
        </div>
    </section>

    {{-- Simulateur --}}
    <section class="bg-marine">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 py-16"
             x-data="{ fr: 10, spe: 10, oral: 10,
                       get ecrit() { return ((this.fr * 3 + this.spe * 5) / 8).toFixed(2) },
                       get finale() { return ((this.fr * 3 + this.spe * 5 + this.oral * 1) / 9).toFixed(2) } }">
            <h2 class="text-2xl font-bold text-white mb-2">Simulez votre moyenne</h2>
            <p class="text-slate-400 mb-8">Déplacez les curseurs pour voir le poids de chaque épreuve. Calcul indicatif : le seuil d'admission est fixé par le jury à chaque session.</p>
            <div class="grid md:grid-cols-2 gap-10">
                <div class="space-y-6">
                    @foreach ([['fr', 'Composition française', 3], ['spe', 'Épreuve de spécialité', 5], ['oral', 'Entretien oral', 1]] as [$cle, $nom, $coef])
                        <label class="block">
                            <span class="flex justify-between text-sm text-slate-300 mb-2">
                                <span>{{ $nom }} <span class="text-slate-500">(coef. {{ $coef }})</span></span>
                                <span class="font-mono text-white" x-text="{{ $cle }} + ' / 20'"></span>
                            </span>
                            <input type="range" min="0" max="20" step="0.5" x-model.number="{{ $cle }}" class="w-full accent-blue-500">
                        </label>
                    @endforeach
                </div>
                <div class="grid grid-cols-2 gap-4 content-start">
                    <div class="rounded-2xl bg-white/5 border border-white/10 p-5">
                        <p class="text-xs uppercase tracking-wider text-slate-400">Moyenne d'écrit</p>
                        <p class="text-3xl font-extrabold text-white mt-1" x-text="ecrit"></p>
                        <p class="text-xs text-slate-400 mt-1">Admissibilité</p>
                    </div>
                    <div class="rounded-2xl p-5 border" :class="finale >= 10 ? 'bg-emerald-500/10 border-emerald-400/30' : 'bg-red-500/10 border-red-400/30'">
                        <p class="text-xs uppercase tracking-wider text-slate-400">Moyenne finale</p>
                        <p class="text-3xl font-extrabold mt-1" :class="finale >= 10 ? 'text-emerald-300' : 'text-red-300'" x-text="finale"></p>
                        <p class="text-xs text-slate-400 mt-1" x-text="finale >= 10 ? 'Au-dessus de 10/20' : 'Sous 10/20'"></p>
                    </div>
                    <p class="col-span-2 text-sm text-slate-400">
                        Un point gagné en spécialité vaut cinq fois un point gagné à l'oral.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- Jour J --}}
    <section class="max-w-6xl mx-auto px-4 sm:px-6 py-16">
        <h2 class="text-2xl font-bold text-marine mb-8">Le jour des épreuves</h2>
        <div class="grid sm:grid-cols-2 lg:grid-cols-5 gap-4">
            @foreach ($jourJ as $i => $item)
                <div class="rounded-2xl bg-fond p-5">
                    <p class="font-mono text-sm font-bold text-institutionnel mb-2">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</p>
                    <p class="font-semibold text-marine mb-1">{{ $item['t'] }}</p>
                    <p class="text-sm text-gray-600">{{ $item['d'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Après l'admission --}}
    <section class="max-w-6xl mx-auto px-4 sm:px-6 pb-20">
        <div class="rounded-3xl border border-gray-100 p-8 sm:p-10 grid lg:grid-cols-3 gap-8">
            <div>
                <h2 class="text-2xl font-bold text-marine mb-2">Et après l'admission ?</h2>
                <p class="text-gray-500 text-sm">Ce qui attend les élèves-professeurs admis par concours direct.</p>
            </div>
            <ol class="lg:col-span-2 grid sm:grid-cols-3 gap-6 text-sm">
                <li>
                    <p class="font-semibold text-marine mb-1">Formation à l'IPNETP</p>
                    <p class="text-gray-600">Pédagogie, didactique de la spécialité et stages en établissement, sans frais de formation.</p>
                </li>
                <li>
                    <p class="font-semibold text-marine mb-1">Certificat d'Aptitude Pédagogique</p>
                    <p class="text-gray-600">Le CAP correspondant à votre concours (PL, PC, IFPB ou IAFPB) sanctionne la formation.</p>
                </li>
                <li>
                    <p class="font-semibold text-marine mb-1">Affectation</p>
                    <p class="text-gray-600">Dans un établissement public de l'ETFP, conformément à l'engagement signé dans votre dossier.</p>
                </li>
            </ol>
        </div>
    </section>

</x-public-layout>
