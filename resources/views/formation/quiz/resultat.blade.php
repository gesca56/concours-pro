<x-app-layout>
    <x-slot name="header">
        <a href="{{ route('formation.modules.show', $quiz->module) }}" class="text-xs font-semibold text-institutionnel hover:underline">← {{ $quiz->module->titre }}</a>
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Correction') }} — {{ $quiz->titre }}</h2>
        <p class="text-sm text-gray-500">{{ __('Copie du') }} {{ $tentative->created_at->format('d/m/Y à H\hi') }}</p>
    </x-slot>

    @php $reussi = $tentative->note >= 10; @endphp

    <div class="py-10">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <section class="rounded-xl p-6 flex flex-wrap items-center justify-between gap-4 {{ $reussi ? 'bg-emerald-50 border border-emerald-200' : 'bg-amber-50 border border-amber-200' }}">
                <div>
                    <p class="text-4xl font-bold {{ $reussi ? 'text-emerald-700' : 'text-amber-700' }}">{{ number_format($tentative->note, 2, ',', ' ') }}<span class="text-xl">/20</span></p>
                    <p class="text-sm text-gray-700 mt-1">{{ $tentative->bonnes_reponses }} {{ __('bonne(s) réponse(s) sur') }} {{ $tentative->nombre_questions }}</p>
                </div>
                <div class="text-sm text-gray-700 max-w-xs">
                    {{ $reussi ? __('Bon travail ! Relisez les explications pour consolider vos acquis.') : __('Relisez les leçons du module puis retentez le quiz : chaque essai vous fait progresser.') }}
                </div>
                @if ($restantes !== 0)
                    <a href="{{ route('formation.quiz.show', $quiz) }}" class="px-4 py-2 bg-institutionnel text-white text-sm font-semibold rounded-md hover:bg-institutionnel-hover">{{ __('Recommencer') }}</a>
                @endif
            </section>

            @foreach ($quiz->questions as $question)
                @php
                    $repondue = array_key_exists($question->id, $tentative->reponses);
                    $choisi = $tentative->reponses[$question->id] ?? null;
                    $juste = $choisi === $question->bonne_reponse;
                @endphp
                @if ($repondue)
                    <section class="bg-white rounded-lg shadow-sm p-6">
                        <p class="text-xs font-semibold {{ $juste ? 'text-emerald-700' : 'text-red-700' }}">
                            {{ __('Question') }} {{ $loop->iteration }} · {{ $juste ? __('Juste') : ($choisi === null ? __('Sans réponse') : __('Faux')) }}
                        </p>
                        <p class="font-medium text-gray-900 mt-1 whitespace-pre-line">{{ $question->enonce }}</p>
                        <ul class="mt-3 space-y-1.5 text-sm">
                            @foreach ($question->choix as $i => $choix)
                                <li class="px-3 py-2 rounded-md border
                                    {{ $i === $question->bonne_reponse ? 'border-emerald-300 bg-emerald-50 text-emerald-900' : ($i === $choisi ? 'border-red-300 bg-red-50 text-red-900' : 'border-gray-100 text-gray-600') }}">
                                    <span class="font-semibold">{{ chr(65 + $i) }}.</span> {{ $choix }}
                                    @if ($i === $question->bonne_reponse) <span class="float-right">✓</span>
                                    @elseif ($i === $choisi) <span class="float-right">✗</span>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                        @if ($question->explication)
                            <p class="mt-3 text-sm text-gray-700 p-3 rounded-md bg-institutionnel/5"><strong>{{ __('Explication') }} :</strong> {{ $question->explication }}</p>
                        @endif
                    </section>
                @endif
            @endforeach
        </div>
    </div>
</x-app-layout>
