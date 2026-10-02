<x-app-layout>
    <x-slot name="header">
        <a href="{{ route('formation.modules.show', $quiz->module) }}" class="text-xs font-semibold text-institutionnel hover:underline">← {{ $quiz->module->titre }}</a>
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $quiz->titre }}</h2>
        <p class="text-sm text-gray-500">
            {{ $quiz->questions->count() }} {{ __('question(s)') }}
            @if ($quiz->duree_minutes) · {{ __('environ') }} {{ $quiz->duree_minutes }} min @endif
            · {{ $restantes === null ? __('tentatives illimitées') : $restantes.' '.__('tentative(s) restante(s)') }}
        </p>
    </x-slot>

    <div class="py-10">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            @if ($tentatives->isNotEmpty())
                <section class="bg-white rounded-lg shadow-sm p-5 text-sm">
                    <p class="font-medium text-gray-900 mb-2">{{ __('Vos tentatives') }}</p>
                    <ul class="flex flex-wrap gap-2">
                        @foreach ($tentatives as $tentative)
                            <li>
                                <a href="{{ route('formation.quiz.resultat', $tentative) }}"
                                   class="inline-block px-3 py-1 rounded-full text-xs font-medium {{ $tentative->note >= 10 ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                    {{ $tentative->created_at->format('d/m H:i') }} · {{ number_format($tentative->note, 1, ',', ' ') }}/20
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </section>
            @endif

            @if ($restantes === 0)
                <div class="p-6 rounded-lg bg-amber-50 border border-amber-200 text-sm text-amber-900">
                    {{ __('Vous avez utilisé toutes vos tentatives pour ce quiz. Consultez la correction de vos copies ci-dessus.') }}
                </div>
            @else
                @if ($quiz->consignes)
                    <div class="p-4 rounded-lg bg-institutionnel/5 border border-institutionnel/20 text-sm text-marine whitespace-pre-line">{{ $quiz->consignes }}</div>
                @endif

                <form method="POST" action="{{ route('formation.quiz.store', $quiz) }}" class="space-y-4"
                      x-data="{ repondues: 0, total: {{ $quiz->questions->count() }} }"
                      @change="repondues = $el.querySelectorAll('input[type=radio]:checked').length">
                    @csrf
                    @foreach ($quiz->questions as $question)
                        <fieldset class="bg-white rounded-lg shadow-sm p-6">
                            <legend class="sr-only">{{ __('Question') }} {{ $loop->iteration }}</legend>
                            <p class="text-xs font-semibold text-institutionnel">{{ __('Question') }} {{ $loop->iteration }}</p>
                            <p class="font-medium text-gray-900 mt-1 whitespace-pre-line">{{ $question->enonce }}</p>
                            <div class="mt-4 space-y-2">
                                @foreach ($question->choix as $i => $choix)
                                    <label class="flex items-start gap-3 p-3 rounded-md border border-gray-200 cursor-pointer hover:bg-gray-50 has-[:checked]:border-institutionnel has-[:checked]:bg-institutionnel/5">
                                        <input type="radio" name="reponses[{{ $question->id }}]" value="{{ $i }}" class="mt-0.5 text-institutionnel">
                                        <span class="text-sm text-gray-800"><span class="font-semibold text-gray-500">{{ chr(65 + $i) }}.</span> {{ $choix }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </fieldset>
                    @endforeach

                    <div class="sticky bottom-4 bg-white rounded-lg shadow-lg p-4 flex items-center justify-between gap-4">
                        <p class="text-sm text-gray-600"><span x-text="repondues"></span>/<span x-text="total"></span> {{ __('réponse(s)') }}</p>
                        <button type="submit"
                                @click="if (repondues < total && ! confirm('Certaines questions sont sans réponse. Valider quand même ?')) $event.preventDefault()"
                                class="px-5 py-2.5 bg-institutionnel text-white text-sm font-semibold rounded-md hover:bg-institutionnel-hover">
                            {{ __('Valider mes réponses') }}
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</x-app-layout>
