@php
    $cycle = config('ipnetp.cycles.'.$c->cycle);
    $joursRestants = (int) now()->startOfDay()->diffInDays($c->date_cloture, false);
@endphp

<article class="flex flex-col p-5 rounded-2xl border border-gray-100 bg-white hover:shadow-lg transition">
    <div class="flex items-center justify-between gap-3 mb-3">
        <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-marine">
            <span class="w-2.5 h-2.5 rounded-sm {{ $cycle['chemise']['classe'] ?? 'bg-gray-400' }}"></span>
            {{ $c->cycle }}
        </span>
        <span class="text-xs px-2 py-0.5 rounded-full {{ $joursRestants <= 7 ? 'bg-amber-50 text-amber-700' : 'bg-emerald-50 text-emerald-700' }}">
            @if ($joursRestants > 1)
                Clôture dans {{ $joursRestants }} jours
            @elseif ($joursRestants === 1)
                Clôture demain
            @else
                Clôture aujourd'hui
            @endif
        </span>
    </div>
    <h3 class="font-semibold text-marine leading-snug mb-1">{{ $c->nom }}</h3>
    <p class="text-xs text-gray-500 mb-4">{{ $cycle['intitule'] ?? '' }} · Filière {{ strtolower($c->filiere) }}</p>
    <dl class="text-sm space-y-1.5 mb-5">
        <div class="flex justify-between gap-3"><dt class="text-gray-500">Diplôme</dt><dd class="text-right text-gray-800">{{ $c->diplome_requis }}</dd></div>
        <div class="flex justify-between gap-3"><dt class="text-gray-500">Âge</dt><dd class="text-gray-800">{{ $c->age_min }} – {{ $c->age_max }} ans</dd></div>
        <div class="flex justify-between gap-3"><dt class="text-gray-500">Inscription</dt><dd class="text-gray-800">{{ number_format($c->frais_inscription, 0, ',', ' ') }} FCFA</dd></div>
        <div class="flex justify-between gap-3"><dt class="text-gray-500">Clôture</dt><dd class="text-gray-800">{{ $c->date_cloture->translatedFormat('d F Y') }}</dd></div>
        @if ($c->date_concours)
            <div class="flex justify-between gap-3"><dt class="text-gray-500">Écrits</dt><dd class="text-gray-800">{{ $c->date_concours->translatedFormat('d F Y') }}</dd></div>
        @endif
    </dl>
    <a href="{{ auth()->check() ? route('candidatures.create') : route('register') }}"
       class="mt-auto text-center px-4 py-2.5 bg-institutionnel text-white rounded-lg text-sm font-semibold hover:bg-institutionnel-hover transition">
        Postuler à ce concours
    </a>
</article>
