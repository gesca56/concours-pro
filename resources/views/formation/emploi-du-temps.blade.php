<x-app-layout>
    <x-slot name="header">
        <a href="{{ route('formation.index') }}" class="text-xs font-semibold text-institutionnel hover:underline">← {{ __('E-learning') }}</a>
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Emploi du temps') }}</h2>
        <p class="text-sm text-gray-500">{{ $promotion->nom }}</p>
    </x-slot>

    <div class="py-10">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="flex items-center justify-between gap-4">
                <a href="{{ route('formation.emploi-du-temps', ['semaine' => $decalage - 1]) }}" class="px-3 py-2 text-sm border border-gray-300 rounded-md hover:bg-white">← {{ __('Semaine précédente') }}</a>
                <p class="font-semibold text-marine text-center">
                    {{ __('Semaine du') }} {{ $lundi->translatedFormat('d F') }} {{ __('au') }} {{ $lundi->copy()->addDays(5)->translatedFormat('d F Y') }}
                    @if ($decalage !== 0)
                        <a href="{{ route('formation.emploi-du-temps') }}" class="block text-xs font-normal text-institutionnel hover:underline">{{ __('Revenir à cette semaine') }}</a>
                    @endif
                </p>
                <a href="{{ route('formation.emploi-du-temps', ['semaine' => $decalage + 1]) }}" class="px-3 py-2 text-sm border border-gray-300 rounded-md hover:bg-white">{{ __('Semaine suivante') }} →</a>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @for ($i = 0; $i < 6; $i++)
                    @php
                        $jour = $lundi->copy()->addDays($i);
                        $duJour = $seances->get($jour->toDateString(), collect());
                    @endphp
                    <section class="bg-white rounded-lg shadow-sm overflow-hidden {{ $jour->isToday() ? 'ring-2 ring-institutionnel' : '' }}">
                        <h3 class="px-4 py-2 text-sm font-semibold {{ $jour->isToday() ? 'bg-institutionnel text-white' : 'bg-gray-50 text-gray-700' }}">
                            {{ ucfirst($jour->translatedFormat('l d/m')) }}
                        </h3>
                        <ul class="divide-y divide-gray-100">
                            @forelse ($duJour as $seance)
                                <li class="p-4 text-sm">
                                    <p class="text-xs font-semibold text-institutionnel">{{ $seance->debut->format('H\hi') }} – {{ $seance->fin->format('H\hi') }}</p>
                                    <p class="font-medium text-gray-900">{{ $seance->module?->titre ?? $seance->observations ?? $seance->libelleType() }}</p>
                                    <p class="text-xs text-gray-500">
                                        {{ $seance->libelleType() }}
                                        @if ($seance->salle) · {{ $seance->salle }} @endif
                                        @if ($seance->module?->enseignant) · {{ $seance->module->enseignant->name }} @endif
                                    </p>
                                    @if ($seance->lien_visio)
                                        <a href="{{ $seance->lien_visio }}" target="_blank" rel="noopener noreferrer" class="text-xs text-institutionnel underline">{{ __('Rejoindre la classe virtuelle') }}</a>
                                    @endif
                                </li>
                            @empty
                                <li class="p-4 text-sm text-gray-400">{{ __('Pas de cours') }}</li>
                            @endforelse
                        </ul>
                    </section>
                @endfor
            </div>
        </div>
    </div>
</x-app-layout>
