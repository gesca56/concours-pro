<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $concours->nom }}</h2>
            <a href="{{ route('administration.concours.edit', $concours) }}" class="text-sm text-institutionnel hover:text-institutionnel-hover font-medium">
                {{ __('Modifier') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-emerald-50 text-emerald-800 text-sm p-4 rounded-md">{{ session('status') }}</div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="font-medium text-gray-900">{{ __('Détails') }}</h3>
                    <x-statut-badge :statut="$concours->statut->value" />
                </div>

                <dl class="grid grid-cols-2 gap-4 text-sm">
                    <div><dt class="text-gray-500">{{ __('Cycle') }}</dt><dd class="text-gray-900">{{ $concours->cycle }}</dd></div>
                    <div><dt class="text-gray-500">{{ __('Filière') }}</dt><dd class="text-gray-900">{{ $concours->filiere }}</dd></div>
                    <div><dt class="text-gray-500">{{ __('Diplôme requis') }}</dt><dd class="text-gray-900">{{ $concours->diplome_requis }}</dd></div>
                    <div><dt class="text-gray-500">{{ __('Tranche d\'âge') }}</dt><dd class="text-gray-900">{{ $concours->age_min }}–{{ $concours->age_max }} ans</dd></div>
                    <div><dt class="text-gray-500">{{ __('Frais inscription') }}</dt><dd class="text-gray-900">{{ number_format($concours->frais_inscription, 0, ',', ' ') }} FCFA</dd></div>
                    <div><dt class="text-gray-500">{{ __('Frais visite médicale') }}</dt><dd class="text-gray-900">{{ number_format($concours->frais_visite_medicale, 0, ',', ' ') }} FCFA</dd></div>
                    <div><dt class="text-gray-500">{{ __('Ouverture') }}</dt><dd class="text-gray-900">{{ $concours->date_ouverture->format('d/m/Y') }}</dd></div>
                    <div><dt class="text-gray-500">{{ __('Clôture') }}</dt><dd class="text-gray-900">{{ $concours->date_cloture->format('d/m/Y') }}</dd></div>
                    <div><dt class="text-gray-500">{{ __('Seuil d\'admission') }}</dt><dd class="text-gray-900">{{ $concours->seuil_admission }}/20</dd></div>
                </dl>

                @php
                    $transitions = [
                        'brouillon' => 'ouvert',
                        'ouvert' => 'cloture',
                        'cloture' => 'deliberation',
                        'deliberation' => 'termine',
                    ];
                    $prochain = $transitions[$concours->statut->value] ?? null;
                @endphp

                <div class="flex flex-wrap gap-3 pt-2 border-t border-gray-100">
                    @if ($prochain)
                        <form method="POST" action="{{ route('administration.concours.statut', $concours) }}">
                            @csrf
                            <input type="hidden" name="statut" value="{{ $prochain }}">
                            <button type="submit" class="px-4 py-2 bg-institutionnel text-white text-xs rounded-md font-semibold uppercase tracking-widest hover:bg-institutionnel-hover">
                                {{ __('Passer au statut') }} « {{ $prochain }} »
                            </button>
                        </form>
                    @endif

                    @if ($concours->statut->value === 'cloture')
                        <form method="POST" action="{{ route('administration.concours.anonymat', $concours) }}">
                            @csrf
                            <button type="submit" class="px-4 py-2 border border-gray-300 text-gray-700 text-xs rounded-md font-semibold uppercase tracking-widest hover:bg-gray-50">
                                {{ __("Attribuer les numéros d'anonymat") }}
                            </button>
                        </form>
                    @endif

                    @if (in_array($concours->statut->value, ['cloture', 'deliberation']))
                        <form method="POST" action="{{ route('administration.concours.deliberation', $concours) }}">
                            @csrf
                            <button type="submit" class="px-4 py-2 bg-marine text-white text-xs rounded-md font-semibold uppercase tracking-widest hover:bg-marine-light">
                                {{ __('Lancer la délibération') }}
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg divide-y divide-gray-100">
                <h3 class="font-medium text-gray-900 p-6 pb-0">{{ __('Candidatures') }} ({{ $concours->candidatures->count() }})</h3>
                @forelse ($concours->candidatures as $candidature)
                    <div class="p-6 flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $candidature->candidat->name }}</p>
                            <p class="text-xs text-gray-500">
                                {{ $candidature->numero_anonymat ?? __('Pas encore anonymisé') }}
                                @if ($candidature->note_totale !== null)
                                    · {{ __('Note :') }} {{ $candidature->note_totale }}/20
                                @endif
                            </p>
                        </div>
                        <x-statut-badge :statut="$candidature->statut" />
                    </div>
                @empty
                    <p class="p-6 text-gray-500">{{ __('Aucune candidature.') }}</p>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
