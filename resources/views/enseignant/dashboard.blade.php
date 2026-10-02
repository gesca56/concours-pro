<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Copies à corriger') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-emerald-50 text-emerald-800 text-sm p-4 rounded-md">{{ session('status') }}</div>
            @endif

            <a href="{{ route('pedagogie.dashboard') }}" class="flex items-center justify-between gap-4 p-4 rounded-lg bg-white shadow-sm hover:ring-2 hover:ring-institutionnel/40 text-sm">
                <span>
                    <span class="block font-semibold text-marine">{{ __('Gestion pédagogique et e-learning') }}</span>
                    <span class="block text-gray-500">{{ __('Vos modules, leçons, quiz, devoirs et copies d\'élèves-professeurs') }}</span>
                </span>
                <span class="text-institutionnel font-semibold">→</span>
            </a>

            <div class="bg-institutionnel/5 border border-institutionnel/20 rounded-md p-4 text-sm text-marine">
                {{ __("Correction anonyme : seul le numéro de copie est affiché, l'identité du candidat n'est pas accessible.") }}
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg divide-y divide-gray-100">
                @forelse ($candidatures as $candidature)
                    <div class="p-6 flex items-center justify-between gap-4">
                        <div>
                            <p class="font-mono font-semibold text-marine">{{ $candidature->numero_anonymat }}</p>
                            <p class="text-sm text-gray-500">{{ $candidature->concours->nom }}</p>
                        </div>
                        <form method="POST" action="{{ route('enseignant.candidatures.note', $candidature) }}" class="flex items-center gap-2">
                            @csrf
                            @method('PATCH')
                            <input type="number" name="note_totale" min="0" max="20" step="0.25" required
                                   placeholder="/20" class="w-20 text-sm border-gray-300 rounded-md">
                            <button type="submit"
                                    class="px-4 py-2 bg-institutionnel text-white text-xs rounded-md font-semibold uppercase tracking-widest hover:bg-institutionnel-hover">
                                {{ __('Enregistrer') }}
                            </button>
                        </form>
                    </div>
                @empty
                    <p class="p-6 text-gray-500">{{ __('Aucune copie à corriger pour le moment.') }}</p>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
