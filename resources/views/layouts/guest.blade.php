<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Concours-Pro') }} · Espace candidat</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @include('partials.pwa')
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased bg-fond">
        @php
            $rappels = [
                ['t' => 'Nationalité ivoirienne', 'd' => 'et '.config('ipnetp.age_min').' à '.config('ipnetp.age_max').' ans au 1er janvier de l\'année du concours.'],
                ['t' => 'Le bon diplôme', 'd' => 'Ingénieur/Master (PL), Licence pro/BTS/DUT (PC), BT/Bac (IFPB), CAP/BEPC (IAFPB).'],
                ['t' => number_format(config('ipnetp.frais_inscription'), 0, ',', ' ').' FCFA', 'd' => 'de frais d\'inscription, réglés en Mobile Money depuis votre dossier.'],
                ['t' => '10 pièces à réunir', 'd' => 'dont un casier judiciaire de moins de 3 mois et un certificat de non-bégaiement.'],
            ];
        @endphp

        <div class="min-h-screen grid lg:grid-cols-2">
            {{-- Panneau d'information (ordinateur) --}}
            <aside class="hidden lg:flex flex-col justify-between bg-marine text-slate-300 p-12 relative overflow-hidden">
                <div class="absolute -top-24 -right-24 w-80 h-80 bg-institutionnel/20 rounded-full blur-3xl"></div>
                <div class="relative">
                    <x-logo dark />
                </div>
                <div class="relative max-w-md">
                    <p class="text-xs font-bold tracking-wider text-institutionnel uppercase mb-3">Concours directs d'entrée à l'IPNETP</p>
                    <h1 class="text-3xl font-extrabold text-white leading-tight mb-8">Votre dossier, de la préinscription à la délibération.</h1>
                    <ul class="space-y-5">
                        @foreach ($rappels as $rappel)
                            <li class="flex gap-3">
                                <svg class="w-5 h-5 mt-0.5 text-institutionnel shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                                <p class="text-sm"><strong class="text-white">{{ $rappel['t'] }}</strong> {{ $rappel['d'] }}</p>
                            </li>
                        @endforeach
                    </ul>
                    <a href="{{ route('pages.guide') }}" class="inline-block mt-8 text-sm font-semibold text-white hover:text-institutionnel">Lire le guide du candidat →</a>
                </div>
                <p class="relative text-xs text-slate-500">Secrétariat des concours · {{ config('ipnetp.institut.telephones.0') }} · {{ config('ipnetp.institut.horaires') }}</p>
            </aside>

            {{-- Formulaire --}}
            <main class="flex flex-col items-center justify-center px-4 py-10 sm:px-6">
                <div class="lg:hidden mb-8">
                    <x-logo />
                </div>

                <div class="w-full sm:max-w-md bg-white shadow-sm border border-gray-100 rounded-2xl px-6 py-8">
                    @if (session('error'))
                        <div role="alert" class="mb-4 p-3 rounded-md bg-red-50 border border-red-200 text-sm text-red-800">
                            {{ session('error') }}
                        </div>
                    @endif

                    {{ $slot }}
                </div>

                <p class="mt-6 text-xs text-gray-500 text-center max-w-sm">
                    Besoin d'aide ? Consultez les <a href="{{ route('pages.guide') }}#faq" class="text-institutionnel underline">questions fréquentes</a>
                    ou <a href="{{ url('/') }}" class="text-institutionnel underline">revenez à l'accueil</a>.
                </p>
            </main>
        </div>
    </body>
</html>
