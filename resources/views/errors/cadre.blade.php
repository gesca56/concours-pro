{{-- Gabarit commun des pages d'erreur : autonome (aucune requête en base). --}}
<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $code }} · {{ config('app.name', 'Concours-Pro') }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600,800&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css'])
    </head>
    <body class="font-sans antialiased bg-fond text-gray-900">
        <main class="min-h-screen flex flex-col items-center justify-center px-4 py-16 text-center">
            <x-logo class="mb-12" />
            <p class="font-mono text-sm font-bold text-institutionnel mb-3">Erreur {{ $code }}</p>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-marine mb-4">{{ $titre }}</h1>
            <p class="text-gray-600 max-w-md mb-10">{{ $texte }}</p>
            <div class="flex flex-col sm:flex-row gap-3">
                <a href="{{ url('/') }}" class="px-6 py-3 bg-institutionnel text-white rounded-lg font-semibold hover:bg-institutionnel-hover transition">Retour à l'accueil</a>
                <a href="{{ url('/guide-du-candidat') }}" class="px-6 py-3 bg-white border border-gray-200 text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition">Guide du candidat</a>
            </div>
            <p class="mt-12 text-xs text-gray-400">Secrétariat des concours · {{ config('ipnetp.institut.telephones.0') }} · {{ config('ipnetp.institut.email') }}</p>
        </main>
    </body>
</html>
