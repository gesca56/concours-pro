<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Administration') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-emerald-50 text-emerald-800 text-sm p-4 rounded-md">{{ session('status') }}</div>
            @endif

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                @foreach ([
                    ['label' => 'Concours', 'valeur' => $stats['concours']],
                    ['label' => 'Concours ouverts', 'valeur' => $stats['concours_ouverts']],
                    ['label' => 'Candidatures', 'valeur' => $stats['candidatures']],
                    ['label' => 'Admis', 'valeur' => $stats['admises']],
                ] as $tuile)
                    <div class="bg-white rounded-lg shadow-sm p-5">
                        <p class="text-2xl font-bold text-marine">{{ $tuile['valeur'] }}</p>
                        <p class="text-xs text-gray-500 uppercase tracking-wide mt-1">{{ $tuile['label'] }}</p>
                    </div>
                @endforeach
            </div>

            <div class="flex justify-between items-center">
                <h3 class="font-medium text-gray-900">{{ __('Concours') }}</h3>
                <div class="flex gap-3">
                    <a href="{{ route('verification-qr') }}"
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50">
                        {{ __('Vérifier un QR Code') }}
                    </a>
                    <a href="{{ route('administration.concours.create') }}"
                       class="inline-flex items-center px-4 py-2 bg-institutionnel text-white rounded-md font-semibold text-xs uppercase tracking-widest hover:bg-institutionnel-hover">
                        {{ __('Nouveau concours') }}
                    </a>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg divide-y divide-gray-100">
                @forelse ($concours as $c)
                    <a href="{{ route('administration.concours.show', $c) }}" class="block p-6 hover:bg-gray-50">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-medium text-gray-900">{{ $c->nom }}</p>
                                <p class="text-sm text-gray-500">{{ $c->cycle }} · {{ $c->filiere }} — {{ $c->candidatures_count }} {{ __('candidature(s)') }}</p>
                            </div>
                            <x-statut-badge :statut="$c->statut->value" />
                        </div>
                    </a>
                @empty
                    <p class="p-6 text-gray-500">{{ __('Aucun concours créé.') }}</p>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
