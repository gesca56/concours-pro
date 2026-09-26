<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-fond flex flex-col">
            <header class="bg-marine">
                <div class="max-w-5xl mx-auto px-6 py-4 flex items-center justify-between">
                    <span class="text-white font-bold tracking-wide">
                        SIGEC <span class="text-institutionnel font-normal">IPNETP</span>
                    </span>
                    <nav class="flex items-center gap-4 text-sm">
                        @auth
                            <a href="{{ route('dashboard') }}" class="text-slate-200 hover:text-white font-medium">
                                Tableau de bord
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="text-slate-200 hover:text-white font-medium">
                                Connexion
                            </a>
                            <a href="{{ route('register') }}"
                               class="px-4 py-2 bg-institutionnel text-white rounded-md font-semibold hover:bg-institutionnel-hover">
                                Créer un compte
                            </a>
                        @endauth
                    </nav>
                </div>
            </header>

            <main class="flex-1 max-w-3xl mx-auto px-6 py-16 text-center">
                <h1 class="text-3xl sm:text-4xl font-bold text-marine mb-4">
                    Plateforme sécurisée de gestion des concours
                </h1>
                <p class="text-gray-600 text-lg mb-10">
                    Inscrivez-vous en ligne aux concours de l'IPNETP (CAP/PL, CAP/PC, CAP/IFPB, CAP/IAFPB),
                    suivez votre dossier en temps réel et téléchargez votre convocation.
                </p>

                @guest
                    <div class="flex items-center justify-center gap-4">
                        <a href="{{ route('register') }}"
                           class="px-6 py-3 bg-institutionnel text-white rounded-md font-semibold hover:bg-institutionnel-hover">
                            Créer un compte candidat
                        </a>
                        <a href="{{ route('login') }}"
                           class="px-6 py-3 border border-gray-300 text-gray-700 rounded-md font-semibold hover:bg-gray-50">
                            J'ai déjà un compte
                        </a>
                    </div>
                @else
                    <a href="{{ route('dashboard') }}"
                       class="inline-block px-6 py-3 bg-institutionnel text-white rounded-md font-semibold hover:bg-institutionnel-hover">
                        Accéder à mon tableau de bord
                    </a>
                @endguest
            </main>

            <footer class="text-center text-xs text-gray-400 py-6">
                SIGEC — Institut Pédagogique National de l'Enseignement Technique et Professionnel
            </footer>
        </div>
    </body>
</html>
