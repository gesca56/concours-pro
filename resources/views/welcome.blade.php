<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name') }} — Concours de l'IPNETP en ligne</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased text-gray-900">

        {{-- Header --}}
        <header class="sticky top-0 z-20 bg-white/80 backdrop-blur border-b border-gray-100">
            <div class="max-w-6xl mx-auto px-6 h-16 flex items-center justify-between">
                <x-logo />
                <nav class="hidden sm:flex items-center gap-8 text-sm font-medium text-gray-600">
                    <a href="#fonctionnalites" class="hover:text-marine">Fonctionnalités</a>
                    <a href="#comment-ca-marche" class="hover:text-marine">Comment ça marche</a>
                </nav>
                <div class="flex items-center gap-3">
                    @auth
                        <a href="{{ route('dashboard') }}"
                           class="px-4 py-2 bg-institutionnel text-white rounded-lg text-sm font-semibold hover:bg-institutionnel-hover transition">
                            Tableau de bord
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-medium text-gray-600 hover:text-marine">
                            Connexion
                        </a>
                        <a href="{{ route('register') }}"
                           class="px-4 py-2 bg-institutionnel text-white rounded-lg text-sm font-semibold hover:bg-institutionnel-hover transition shadow-sm shadow-institutionnel/30">
                            Créer un compte
                        </a>
                    @endauth
                </div>
            </div>
        </header>

        {{-- Hero --}}
        <section class="relative overflow-hidden bg-fond">
            <div class="absolute inset-0 bg-gradient-to-br from-institutionnel/5 via-transparent to-transparent"></div>
            <div class="absolute -top-32 -right-32 w-96 h-96 bg-institutionnel/10 rounded-full blur-3xl"></div>
            <div class="absolute top-40 -left-24 w-72 h-72 bg-marine-light/10 rounded-full blur-3xl"></div>

            <div class="relative max-w-4xl mx-auto px-6 pt-20 pb-24 text-center">
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-institutionnel/10 text-institutionnel text-xs font-semibold tracking-wide uppercase mb-6">
                    Inscriptions en ligne
                </span>
                <h1 class="text-4xl sm:text-5xl font-extrabold tracking-tight text-marine leading-[1.1] mb-6">
                    Le concours d'entrée<br class="hidden sm:block"> qui se joue <span class="text-institutionnel">sans la queue</span>
                </h1>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto mb-10 leading-relaxed">
                    Inscrivez-vous en quelques minutes, payez en Mobile Money, suivez votre dossier en
                    temps réel et téléchargez votre convocation sécurisée par QR Code — pour tous les
                    concours CAP/PL, CAP/PC, CAP/IFPB et CAP/IAFPB.
                </p>
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                    @guest
                        <a href="{{ route('register') }}"
                           class="w-full sm:w-auto px-7 py-3.5 bg-institutionnel text-white rounded-lg font-semibold hover:bg-institutionnel-hover transition shadow-lg shadow-institutionnel/30">
                            Créer mon compte candidat
                        </a>
                        <a href="{{ route('login') }}"
                           class="w-full sm:w-auto px-7 py-3.5 bg-white border border-gray-200 text-gray-700 rounded-lg font-semibold hover:border-gray-300 hover:bg-gray-50 transition">
                            J'ai déjà un compte
                        </a>
                    @else
                        <a href="{{ route('dashboard') }}"
                           class="px-7 py-3.5 bg-institutionnel text-white rounded-lg font-semibold hover:bg-institutionnel-hover transition shadow-lg shadow-institutionnel/30">
                            Accéder à mon tableau de bord
                        </a>
                    @endguest
                </div>
            </div>
        </section>

        {{-- Feature strip --}}
        <section id="fonctionnalites" class="max-w-6xl mx-auto px-6 py-20">
            <div class="text-center mb-14">
                <h2 class="text-2xl sm:text-3xl font-bold text-marine mb-3">Tout le parcours, une seule plateforme</h2>
                <p class="text-gray-500 max-w-xl mx-auto">De l'inscription à la délibération, chaque étape est numérisée et sécurisée.</p>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @php
                    $features = [
                        ['icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', 'titre' => 'Éligibilité vérifiée', 'texte' => 'Contrôle automatique de l\'âge et du diplôme requis avant toute inscription.'],
                        ['icon' => 'M3 10h18M7 15h1m4 0h1m-7 4h12a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z', 'titre' => 'Paiement Mobile Money', 'texte' => 'Frais d\'inscription et visite médicale réglés en ligne, en toute sécurité.'],
                        ['icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4', 'titre' => 'Suivi en temps réel', 'texte' => 'Une timeline claire pour voir où en est son dossier, à chaque étape.'],
                        ['icon' => 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z', 'titre' => 'Convocation anti-fraude', 'texte' => 'QR Code unique vérifié par les surveillants le jour du concours.'],
                    ];
                @endphp

                @foreach ($features as $f)
                    <div class="p-6 rounded-2xl border border-gray-100 bg-white hover:shadow-lg hover:-translate-y-0.5 transition">
                        <div class="w-11 h-11 rounded-xl bg-institutionnel/10 flex items-center justify-center mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5.5 h-5.5 text-institutionnel" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $f['icon'] }}" />
                            </svg>
                        </div>
                        <h3 class="font-semibold text-marine mb-1.5">{{ $f['titre'] }}</h3>
                        <p class="text-sm text-gray-500 leading-relaxed">{{ $f['texte'] }}</p>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- How it works --}}
        <section id="comment-ca-marche" class="bg-marine">
            <div class="max-w-5xl mx-auto px-6 py-20">
                <h2 class="text-2xl sm:text-3xl font-bold text-white text-center mb-14">Comment ça marche</h2>
                <div class="grid sm:grid-cols-3 gap-10">
                    @foreach ([
                        ['n' => '01', 't' => 'Créez votre compte', 'd' => "Inscrivez-vous en quelques secondes avec votre email."],
                        ['n' => '02', 't' => 'Choisissez votre concours', 'd' => "Sélectionnez le concours, déposez vos pièces et payez en ligne."],
                        ['n' => '03', 't' => 'Suivez votre dossier', 'd' => "Recevez votre convocation dès que votre dossier est validé."],
                    ] as $etape)
                        <div class="text-center sm:text-left">
                            <div class="text-institutionnel font-mono text-sm font-bold mb-2">{{ $etape['n'] }}</div>
                            <h3 class="text-white font-semibold text-lg mb-2">{{ $etape['t'] }}</h3>
                            <p class="text-slate-300 text-sm leading-relaxed">{{ $etape['d'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- Final CTA --}}
        @guest
            <section class="max-w-4xl mx-auto px-6 py-20 text-center">
                <h2 class="text-2xl sm:text-3xl font-bold text-marine mb-4">Prêt à vous inscrire ?</h2>
                <p class="text-gray-500 mb-8">Créez votre compte candidat et déposez votre dossier en quelques minutes.</p>
                <a href="{{ route('register') }}"
                   class="inline-block px-8 py-3.5 bg-institutionnel text-white rounded-lg font-semibold hover:bg-institutionnel-hover transition shadow-lg shadow-institutionnel/30">
                    Créer mon compte candidat
                </a>
            </section>
        @endguest

        {{-- Footer --}}
        <footer class="border-t border-gray-100">
            <div class="max-w-6xl mx-auto px-6 py-10 flex flex-col sm:flex-row items-center justify-between gap-4">
                <x-logo />
                <p class="text-xs text-gray-400 text-center sm:text-right">
                    © {{ date('Y') }} Concours-Pro — Institut Pédagogique National de l'Enseignement
                    Technique et Professionnel
                </p>
            </div>
        </footer>
    </body>
</html>
