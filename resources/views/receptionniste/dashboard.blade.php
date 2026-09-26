<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dossiers à vérifier') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-emerald-50 text-emerald-800 text-sm p-4 rounded-md">{{ session('status') }}</div>
            @endif

            <div class="flex justify-end">
                <a href="{{ route('verification-qr') }}"
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50">
                    {{ __('Vérifier un QR Code') }}
                </a>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg divide-y divide-gray-100">
                @forelse ($candidatures as $candidature)
                    <a href="{{ route('receptionniste.candidatures.show', $candidature) }}" class="block p-6 hover:bg-gray-50">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-medium text-gray-900">{{ $candidature->candidat->name }}</p>
                                <p class="text-sm text-gray-500">{{ $candidature->concours->nom }}</p>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="text-xs text-gray-500">
                                    {{ $candidature->documents->where('statut_verification', 'valide')->count() }}/{{ $candidature->documents->count() }} {{ __('pièces validées') }}
                                </span>
                                <x-statut-badge :statut="$candidature->statut" />
                            </div>
                        </div>
                    </a>
                @empty
                    <p class="p-6 text-gray-500">{{ __('Aucun dossier en attente de vérification.') }}</p>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
