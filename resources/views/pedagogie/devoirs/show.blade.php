<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <a href="{{ route('pedagogie.modules.show', $devoir->module) }}" class="text-xs font-semibold text-institutionnel hover:underline">← {{ $devoir->module->titre }}</a>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $devoir->titre }}</h2>
                <p class="text-sm {{ $devoir->estEchu() ? 'text-gray-500' : 'text-amber-700' }}">
                    {{ $devoir->estEchu() ? __('Clos depuis le') : __('À rendre avant le') }} {{ $devoir->date_limite->format('d/m/Y à H\hi') }}
                    · {{ $rendus->count() }}/{{ $eleves->count() }} {{ __('copie(s) rendue(s)') }}
                </p>
            </div>
            <a href="{{ route('pedagogie.devoirs.edit', $devoir) }}" class="px-4 py-2 border border-gray-300 text-sm rounded-md hover:bg-gray-50">{{ __('Modifier') }}</a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <x-flash />

            <details class="bg-white rounded-lg shadow-sm p-6">
                <summary class="cursor-pointer font-medium text-gray-900">{{ __('Consignes') }}</summary>
                <p class="mt-3 text-sm text-gray-700 whitespace-pre-line">{{ $devoir->consignes }}</p>
            </details>

            <section class="space-y-4">
                @forelse ($eleves as $eleve)
                    @php $rendu = $rendus->get($eleve->id); @endphp
                    <article id="eleve-{{ $eleve->id }}" class="bg-white rounded-lg shadow-sm p-6">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div>
                                <p class="font-medium text-gray-900">{{ $eleve->name }}</p>
                                <p class="text-xs text-gray-500 font-mono">{{ $eleve->pivot->matricule }}</p>
                            </div>
                            @if (! $rendu)
                                <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $devoir->estEchu() ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-600' }}">
                                    {{ $devoir->estEchu() ? __('Non rendu — compte 0') : __('Pas encore rendu') }}
                                </span>
                            @elseif ($rendu->estCorrige())
                                <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">{{ __('Corrigé') }} · {{ number_format($rendu->note, 2, ',', ' ') }}/20</span>
                            @else
                                <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800">{{ __('À corriger') }}</span>
                            @endif
                        </div>

                        @if ($rendu)
                            <p class="text-xs text-gray-500 mt-2">
                                {{ __('Rendu le') }} {{ $rendu->rendu_le->format('d/m/Y à H\hi') }}
                                @if ($rendu->en_retard)<span class="text-amber-700 font-medium">· {{ __('en retard') }}</span>@endif
                            </p>
                            <div class="mt-3 p-4 rounded-md bg-gray-50 text-sm text-gray-800 whitespace-pre-line max-h-80 overflow-y-auto">{{ $rendu->contenu }}</div>
                            @if ($rendu->lien)
                                <a href="{{ $rendu->lien }}" target="_blank" rel="noopener noreferrer" class="inline-block mt-2 text-sm text-institutionnel underline">{{ __('Ouvrir le fichier joint') }} ↗</a>
                            @endif

                            <form method="POST" action="{{ route('pedagogie.rendus.corriger', $rendu) }}" class="mt-4 grid sm:grid-cols-6 gap-3 items-start">
                                @csrf
                                @method('PATCH')
                                <div>
                                    <label class="text-xs text-gray-500">{{ __('Note /20') }}</label>
                                    <input type="number" name="note" min="0" max="20" step="0.25" required value="{{ $rendu->note }}" class="mt-1 block w-full text-sm border-gray-300 rounded-md">
                                </div>
                                <div class="sm:col-span-4">
                                    <label class="text-xs text-gray-500">{{ __('Appréciation') }}</label>
                                    <textarea name="appreciation" rows="2" class="mt-1 block w-full text-sm border-gray-300 rounded-md">{{ $rendu->appreciation }}</textarea>
                                </div>
                                <button class="sm:mt-5 px-4 py-2 bg-institutionnel text-white text-xs rounded-md font-semibold uppercase tracking-widest hover:bg-institutionnel-hover">
                                    {{ $rendu->estCorrige() ? __('Modifier') : __('Noter') }}
                                </button>
                            </form>
                        @endif
                    </article>
                @empty
                    <p class="bg-white rounded-lg shadow-sm p-6 text-sm text-gray-500">{{ __('Aucun élève inscrit dans cette promotion.') }}</p>
                @endforelse
            </section>
        </div>
    </div>
</x-app-layout>
