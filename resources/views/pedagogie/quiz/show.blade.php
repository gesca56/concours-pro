<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <a href="{{ route('pedagogie.modules.show', $quiz->module) }}" class="text-xs font-semibold text-institutionnel hover:underline">← {{ $quiz->module->titre }}</a>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $quiz->titre }}</h2>
                <p class="text-sm text-gray-500">
                    {{ $quiz->questions->count() }} {{ __('question(s)') }}
                    · {{ $quiz->tentatives_max ? $quiz->tentatives_max.' '.__('tentative(s) max.') : __('tentatives illimitées') }}
                    · {{ $quiz->publie ? __('publié') : __('brouillon') }}
                </p>
            </div>
            <a href="{{ route('pedagogie.quiz.edit', $quiz) }}" class="px-4 py-2 border border-gray-300 text-sm rounded-md hover:bg-gray-50">{{ __('Modifier') }}</a>
        </div>
    </x-slot>

    @php
        $tentatives = $quiz->tentatives;
        $moyenne = $tentatives->avg('note');
    @endphp

    <div class="py-10">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <x-flash />

            <div class="grid grid-cols-3 gap-4">
                @foreach ([
                    ['label' => 'Tentatives', 'valeur' => $tentatives->count()],
                    ['label' => 'Apprenants', 'valeur' => $tentatives->unique('user_id')->count()],
                    ['label' => 'Moyenne', 'valeur' => $moyenne !== null ? number_format($moyenne, 2, ',', ' ').'/20' : '—'],
                ] as $tuile)
                    <div class="bg-white rounded-lg shadow-sm p-5">
                        <p class="text-xs text-gray-500 uppercase tracking-wide">{{ $tuile['label'] }}</p>
                        <p class="text-2xl font-bold text-marine mt-1">{{ $tuile['valeur'] }}</p>
                    </div>
                @endforeach
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <section class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="font-medium text-gray-900 mb-4">{{ __('Questions et taux de réussite') }}</h3>
                    <ol class="space-y-4 text-sm">
                        @foreach ($quiz->questions as $question)
                            @php
                                $repondues = $tentatives->filter(fn ($t) => array_key_exists($question->id, $t->reponses));
                                $reussite = $repondues->count() ? round($repondues->filter(fn ($t) => $t->reponses[$question->id] === $question->bonne_reponse)->count() / $repondues->count() * 100) : null;
                            @endphp
                            <li>
                                <p class="font-medium text-gray-900">{{ $loop->iteration }}. {{ $question->enonce }}</p>
                                <p class="text-emerald-700 mt-0.5">✓ {{ $question->choix[$question->bonne_reponse] ?? '—' }}</p>
                                @if ($reussite !== null)
                                    <div class="mt-1 flex items-center gap-2">
                                        <div class="h-1.5 flex-1 rounded bg-gray-100"><div class="h-1.5 rounded {{ $reussite >= 50 ? 'bg-emerald-500' : 'bg-amber-500' }}" style="width: {{ $reussite }}%"></div></div>
                                        <span class="text-xs text-gray-500 w-10 text-right">{{ $reussite }} %</span>
                                    </div>
                                @endif
                            </li>
                        @endforeach
                    </ol>
                </section>

                <section class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <h3 class="font-medium text-gray-900 p-6 pb-3">{{ __('Dernières tentatives') }}</h3>
                    <table class="w-full text-sm">
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($tentatives->take(30) as $tentative)
                                <tr>
                                    <td class="px-6 py-2">{{ $tentative->eleve->name }}</td>
                                    <td class="px-3 py-2 text-gray-500">{{ $tentative->created_at->format('d/m H:i') }}</td>
                                    <td class="px-6 py-2 text-right font-semibold {{ $tentative->note >= 10 ? 'text-emerald-700' : 'text-red-700' }}">{{ number_format($tentative->note, 2, ',', ' ') }}/20</td>
                                </tr>
                            @empty
                                <tr><td class="px-6 py-4 text-gray-500">{{ __('Personne n\'a encore répondu.') }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </section>
            </div>
        </div>
    </div>
</x-app-layout>
