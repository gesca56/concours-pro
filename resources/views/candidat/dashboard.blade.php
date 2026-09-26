<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tableau de bord') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if ($derniereCandidature)
                @php
                    $etapes = ['en_attente' => 0, 'eligible' => 1, 'validee' => 2, 'admise' => 3, 'recalee' => 3, 'rejetee' => 0, 'inelegible' => 0];
                    $etapeActuelle = $etapes[$derniereCandidature->statut] ?? 0;
                    $enEchec = in_array($derniereCandidature->statut, ['rejetee', 'inelegible', 'recalee']);
                    $labels = ['Inscription soumise', 'Paiements validés', 'Dossier vérifié', 'Délibération'];
                @endphp

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="font-medium text-gray-900">{{ $derniereCandidature->concours->nom }}</h3>
                            <p class="text-sm text-gray-500">{{ $derniereCandidature->concours->cycle }} · {{ $derniereCandidature->concours->filiere }}</p>
                        </div>
                        <x-statut-badge :statut="$derniereCandidature->statut" />
                    </div>

                    <ol class="flex items-center w-full">
                        @foreach ($labels as $index => $label)
                            <li class="flex items-center {{ $index < count($labels) - 1 ? 'w-full' : '' }}">
                                <div class="flex flex-col items-center shrink-0">
                                    <span @class([
                                        'flex items-center justify-center w-8 h-8 rounded-full text-xs font-semibold shrink-0',
                                        'bg-red-100 text-red-700' => $enEchec && $index === $etapeActuelle,
                                        'bg-emerald-600 text-white' => !($enEchec && $index === $etapeActuelle) && $index < $etapeActuelle,
                                        'bg-institutionnel text-white' => !$enEchec && $index === $etapeActuelle,
                                        'bg-gray-100 text-gray-400' => $index > $etapeActuelle,
                                    ])>
                                        {{ $index + 1 }}
                                    </span>
                                    <span class="mt-1 text-xs text-gray-500 text-center max-w-[80px]">{{ $label }}</span>
                                </div>
                                @if ($index < count($labels) - 1)
                                    <div @class([
                                        'flex-1 h-0.5 mx-2',
                                        'bg-emerald-600' => $index < $etapeActuelle,
                                        'bg-gray-200' => $index >= $etapeActuelle,
                                    ])></div>
                                @endif
                            </li>
                        @endforeach
                    </ol>

                    <div class="mt-6 flex justify-end">
                        <a href="{{ route('candidatures.show', $derniereCandidature) }}" class="text-sm text-institutionnel hover:text-institutionnel-hover font-medium">
                            {{ __('Voir le dossier complet →') }}
                        </a>
                    </div>
                </div>
            @else
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-center space-y-4">
                    <p class="text-gray-600">{{ __("Vous n'avez pas encore de candidature en cours.") }}</p>
                    <a href="{{ route('candidatures.create') }}"
                       class="inline-flex items-center px-4 py-2 bg-institutionnel border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-institutionnel-hover">
                        {{ __("S'inscrire à un concours") }}
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
