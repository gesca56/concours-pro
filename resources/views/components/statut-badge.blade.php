@props(['statut'])

@php
    $styles = match ($statut) {
        'validee', 'admise', 'eligible', 'valide', 'ouvert', 'traite' => 'bg-emerald-100 text-emerald-800',
        'en_attente' => 'bg-amber-100 text-amber-800',
        'rejetee', 'recalee', 'inelegible', 'rejete', 'echoue' => 'bg-red-100 text-red-800',
        default => 'bg-gray-100 text-gray-800',
    };

    $labels = [
        'en_attente' => 'En attente',
        'eligible' => 'Éligible',
        'inelegible' => 'Inéligible',
        'validee' => 'Validée',
        'rejetee' => 'Rejetée',
        'admise' => 'Admise',
        'recalee' => 'Recalée',
        'valide' => 'Validé',
        'rejete' => 'Rejeté',
        'echoue' => 'Échoué',
        'brouillon' => 'Brouillon',
        'ouvert' => 'Ouvert',
        'cloture' => 'Clôturé',
        'deliberation' => 'Délibération',
        'termine' => 'Terminé',
        'traite' => 'Traité',
    ];
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium $styles"]) }}>
    {{ $labels[$statut] ?? ucfirst($statut) }}
</span>
