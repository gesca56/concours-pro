<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Mes candidatures') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="flex justify-end">
                <a href="{{ route('candidatures.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-institutionnel border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-institutionnel-hover">
                    {{ __("S'inscrire à un concours") }}
                </a>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg divide-y divide-gray-100">
                @forelse ($candidatures as $candidature)
                    <a href="{{ route('candidatures.show', $candidature) }}" class="block p-6 hover:bg-gray-50">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-medium text-gray-900">{{ $candidature->concours->nom }}</p>
                                <p class="text-sm text-gray-500">{{ $candidature->concours->cycle }} · {{ $candidature->concours->filiere }}</p>
                            </div>
                            <x-statut-badge :statut="$candidature->statut" />
                        </div>
                    </a>
                @empty
                    <p class="p-6 text-gray-500">{{ __('Aucune candidature pour le moment.') }}</p>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
