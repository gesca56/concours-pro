@props(['titre' => null, 'description' => null])

@php
    $institut = config('ipnetp.institut');
    $liens = [
        ['route' => 'pages.institut', 'libelle' => "L'IPNETP"],
        ['route' => 'pages.concours', 'libelle' => 'Les concours'],
        ['route' => 'pages.guide', 'libelle' => 'Guide du candidat'],
        ['route' => 'pages.preparation', 'libelle' => 'Préparer le concours'],
    ];
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="{{ $description ?? 'Plateforme de préinscription aux concours directs d\'entrée à l\'IPNETP : CAP/PL, CAP/PC, CAP/IFPB et CAP/IAFPB.' }}">

        <title>{{ $titre ? $titre.' — ' : '' }}{{ config('app.name') }} · Concours de l'IPNETP</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        @include('partials.pwa')

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased text-gray-900 bg-white">

        {{-- Bandeau institutionnel --}}
        <div class="bg-marine text-slate-300 text-xs">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 h-8 flex items-center justify-between gap-4">
                <span class="truncate">République de Côte d'Ivoire · {{ $institut['sigle'] }} — sous tutelle du METFPA</span>
                <span class="hidden md:inline shrink-0">{{ $institut['telephones'][0] }} · {{ $institut['email'] }}</span>
            </div>
        </div>

        {{-- En-tête --}}
        <header class="sticky top-0 z-20 bg-white/85 backdrop-blur border-b border-gray-100" x-data="{ menu: false }">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">
                <x-logo />
                <nav class="hidden md:flex items-center gap-7 text-sm font-medium text-gray-600">
                    @foreach ($liens as $lien)
                        <a href="{{ route($lien['route']) }}"
                           class="{{ request()->routeIs($lien['route']) ? 'text-institutionnel' : 'hover:text-marine' }}">{{ $lien['libelle'] }}</a>
                    @endforeach
                </nav>
                <div class="hidden md:flex items-center gap-3">
                    @auth
                        <a href="{{ route('dashboard') }}"
                           class="px-4 py-2 bg-institutionnel text-white rounded-lg text-sm font-semibold hover:bg-institutionnel-hover transition">
                            Tableau de bord
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-medium text-gray-600 hover:text-marine">Connexion</a>
                        <a href="{{ route('register') }}"
                           class="px-4 py-2 bg-institutionnel text-white rounded-lg text-sm font-semibold hover:bg-institutionnel-hover transition shadow-sm shadow-institutionnel/30">
                            Se préinscrire
                        </a>
                    @endauth
                </div>
                <button type="button" @click="menu = !menu" class="md:hidden p-2 -mr-2 text-gray-600" aria-label="Menu">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path x-show="!menu" stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16" />
                        <path x-show="menu" x-cloak stroke-linecap="round" d="M6 6l12 12M18 6L6 18" />
                    </svg>
                </button>
            </div>
            <div x-show="menu" x-cloak class="md:hidden border-t border-gray-100 bg-white px-4 py-3 space-y-1">
                @foreach ($liens as $lien)
                    <a href="{{ route($lien['route']) }}" class="block py-2 text-sm font-medium text-gray-700">{{ $lien['libelle'] }}</a>
                @endforeach
                <div class="pt-2 flex gap-3">
                    @auth
                        <a href="{{ route('dashboard') }}" class="flex-1 text-center px-4 py-2 bg-institutionnel text-white rounded-lg text-sm font-semibold">Tableau de bord</a>
                    @else
                        <a href="{{ route('login') }}" class="flex-1 text-center px-4 py-2 border border-gray-200 rounded-lg text-sm font-semibold text-gray-700">Connexion</a>
                        <a href="{{ route('register') }}" class="flex-1 text-center px-4 py-2 bg-institutionnel text-white rounded-lg text-sm font-semibold">Se préinscrire</a>
                    @endauth
                </div>
            </div>
        </header>

        <main>
            {{ $slot }}
        </main>

        {{-- Pied de page --}}
        <footer class="bg-marine text-slate-300">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 py-14 grid gap-10 sm:grid-cols-2 lg:grid-cols-4">
                <div class="lg:col-span-2">
                    <x-logo dark />
                    <p class="mt-4 text-sm leading-relaxed max-w-md">
                        Plateforme de préinscription aux concours directs d'entrée à
                        l'{{ $institut['nom'] }}.
                    </p>
                    <p class="mt-3 text-xs italic text-slate-400 max-w-md">« {{ $institut['devise'] }} »</p>
                </div>
                <div>
                    <h3 class="text-white font-semibold text-sm mb-3">Secrétariat des concours</h3>
                    <ul class="space-y-1.5 text-sm">
                        <li>{{ $institut['secretariat'] }}</li>
                        <li>{{ $institut['adresse_postale'] }}</li>
                        @foreach ($institut['telephones'] as $tel)
                            <li><a href="tel:{{ str_replace(' ', '', $tel) }}" class="hover:text-white">{{ $tel }}</a></li>
                        @endforeach
                        <li><a href="mailto:{{ $institut['email'] }}" class="hover:text-white">{{ $institut['email'] }}</a></li>
                        <li class="text-slate-400">{{ $institut['horaires'] }}</li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-white font-semibold text-sm mb-3">S'informer</h3>
                    <ul class="space-y-1.5 text-sm">
                        @foreach ($liens as $lien)
                            <li><a href="{{ route($lien['route']) }}" class="hover:text-white">{{ $lien['libelle'] }}</a></li>
                        @endforeach
                        <li><a href="{{ route('pages.guide') }}#faq" class="hover:text-white">Questions fréquentes</a></li>
                        <li><a href="{{ $institut['site_officiel'] }}" target="_blank" rel="noopener" class="hover:text-white">Site officiel ipnetp.ci ↗</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-white/10">
                <div class="max-w-6xl mx-auto px-4 sm:px-6 py-5 text-xs text-slate-400 flex flex-col sm:flex-row gap-2 justify-between">
                    <span>© {{ date('Y') }} Concours-Pro — projet de fin d'études consacré aux concours de l'IPNETP.</span>
                    <span>Informations indicatives : seuls les communiqués officiels du METFPA et de l'IPNETP font foi.</span>
                </div>
            </div>
        </footer>
    </body>
</html>
