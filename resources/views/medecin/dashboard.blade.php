<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Visites médicales à valider') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-emerald-50 text-emerald-800 text-sm p-4 rounded-md">{{ session('status') }}</div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg divide-y divide-gray-100">
                @forelse ($candidatures as $candidature)
                    <div class="p-6 space-y-3" x-data="{ inapte: false }">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-medium text-gray-900">{{ $candidature->candidat->name }}</p>
                                <p class="text-sm text-gray-500">{{ $candidature->concours->nom }}</p>
                            </div>
                            <span class="text-xs text-gray-400">
                                {{ __('Programmée le') }} {{ $candidature->visite_medicale_programmee_le->format('d/m/Y') }}
                            </span>
                        </div>

                        <form method="POST" action="{{ route('medecin.candidatures.valider', $candidature) }}" class="flex items-start gap-3">
                            @csrf
                            @method('PATCH')
                            <button type="submit" name="aptitude_medicale" value="apte"
                                    class="px-4 py-2 bg-emerald-600 text-white text-xs rounded-md font-semibold uppercase tracking-widest hover:bg-emerald-700">
                                {{ __('Apte') }}
                            </button>
                            <button type="button" @click="inapte = !inapte"
                                    class="px-4 py-2 bg-red-600 text-white text-xs rounded-md font-semibold uppercase tracking-widest hover:bg-red-700">
                                {{ __('Inapte') }}
                            </button>
                            <template x-if="inapte">
                                <div class="flex items-center gap-2 flex-1">
                                    <input type="hidden" name="aptitude_medicale" value="inapte">
                                    <input type="text" name="motif_inaptitude" placeholder="{{ __('Motif médical') }}"
                                           class="flex-1 text-xs border-gray-300 rounded-md">
                                    <button type="submit" class="px-3 py-2 bg-red-600 text-white text-xs rounded-md font-semibold hover:bg-red-700">
                                        {{ __('Confirmer') }}
                                    </button>
                                </div>
                            </template>
                        </form>
                    </div>
                @empty
                    <p class="p-6 text-gray-500">{{ __('Aucune visite médicale en attente.') }}</p>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
