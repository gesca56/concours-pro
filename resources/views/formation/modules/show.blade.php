<x-app-layout>
    <x-slot name="header">
        <a href="{{ route('formation.index') }}" class="text-xs font-semibold text-institutionnel hover:underline">← {{ __('E-learning') }}</a>
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $module->titre }}</h2>
        <p class="text-sm text-gray-500">
            {{ $module->libellePublic() }}
            @if ($module->enseignant) · {{ $module->enseignant->name }} @endif
            @unless ($module->estPreparation()) · {{ __('coefficient') }} {{ $module->coefficient }} @endunless
        </p>
    </x-slot>

    @php
        $total = $module->lecons->count();
        $faites = $leconsTerminees->count();
        $pourcentage = $total ? round($faites / $total * 100) : 0;
        $prochaine = $module->lecons->first(fn ($l) => ! $leconsTerminees->contains($l->id));
    @endphp

    <div class="py-10">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <x-flash />

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 space-y-6">
                    {{-- Progression --}}
                    <section class="bg-white rounded-lg shadow-sm p-6">
                        <div class="flex items-center justify-between gap-4">
                            <div class="flex-1">
                                <p class="text-sm text-gray-600">{{ __('Progression') }} : <strong class="text-marine">{{ $faites }}/{{ $total }}</strong> {{ __('leçon(s) terminée(s)') }}</p>
                                <div class="mt-2 h-2 rounded-full bg-gray-100"><div class="h-2 rounded-full bg-emerald-500" style="width: {{ $pourcentage }}%"></div></div>
                            </div>
                            @if ($prochaine)
                                <a href="{{ route('formation.lecons.show', $prochaine) }}" class="px-4 py-2 bg-institutionnel text-white text-sm font-semibold rounded-md hover:bg-institutionnel-hover shrink-0">
                                    {{ $faites ? __('Continuer') : __('Commencer') }}
                                </a>
                            @elseif ($total)
                                <span class="text-sm font-semibold text-emerald-700 shrink-0">✓ {{ __('Module terminé') }}</span>
                            @endif
                        </div>
                    </section>

                    {{-- Leçons --}}
                    <section class="bg-white rounded-lg shadow-sm overflow-hidden">
                        <h3 class="font-medium text-gray-900 p-6 pb-3">{{ __('Leçons') }}</h3>
                        <ol class="divide-y divide-gray-100">
                            @forelse ($module->lecons as $lecon)
                                @php $faite = $leconsTerminees->contains($lecon->id); @endphp
                                <li>
                                    <a href="{{ route('formation.lecons.show', $lecon) }}" class="px-6 py-3 flex items-center gap-4 hover:bg-gray-50">
                                        <span class="w-7 h-7 shrink-0 rounded-full text-xs font-semibold flex items-center justify-center {{ $faite ? 'bg-emerald-500 text-white' : 'bg-gray-100 text-gray-600' }}">
                                            {{ $faite ? '✓' : $loop->iteration }}
                                        </span>
                                        <span class="min-w-0 flex-1">
                                            <span class="block font-medium text-gray-900">{{ $lecon->titre }}</span>
                                            @if ($lecon->resume)<span class="block text-xs text-gray-500 truncate">{{ $lecon->resume }}</span>@endif
                                        </span>
                                        <span class="text-xs text-gray-400 shrink-0">
                                            @if ($lecon->video_url)▶ @endif
                                            @if ($lecon->duree_minutes){{ $lecon->duree_minutes }} min @endif
                                        </span>
                                    </a>
                                </li>
                            @empty
                                <li class="px-6 py-6 text-sm text-gray-500">{{ __('Les leçons de ce module seront bientôt publiées.') }}</li>
                            @endforelse
                        </ol>
                    </section>

                    {{-- Devoirs --}}
                    @if ($module->devoirs->isNotEmpty())
                        <section class="bg-white rounded-lg shadow-sm overflow-hidden">
                            <h3 class="font-medium text-gray-900 p-6 pb-3">{{ __('Devoirs notés') }}</h3>
                            <ul class="divide-y divide-gray-100">
                                @foreach ($module->devoirs as $devoir)
                                    @php $rendu = $devoir->rendus->first(); @endphp
                                    <li>
                                        <a href="{{ route('formation.devoirs.show', $devoir) }}" class="px-6 py-3 flex items-center justify-between gap-4 hover:bg-gray-50 text-sm">
                                            <span>
                                                <span class="block font-medium text-gray-900">{{ $devoir->titre }}</span>
                                                <span class="block text-xs text-gray-500">{{ __('À rendre avant le') }} {{ $devoir->date_limite->format('d/m/Y à H\hi') }}</span>
                                            </span>
                                            @if ($rendu?->estCorrige())
                                                <span class="font-semibold {{ $rendu->note >= 10 ? 'text-emerald-700' : 'text-red-700' }}">{{ number_format($rendu->note, 2, ',', ' ') }}/20</span>
                                            @elseif ($rendu)
                                                <span class="text-xs px-2 py-0.5 rounded-full bg-amber-100 text-amber-800">{{ __('Rendu, en correction') }}</span>
                                            @elseif ($devoir->estEchu())
                                                <span class="text-xs px-2 py-0.5 rounded-full bg-red-100 text-red-800">{{ __('Non rendu') }}</span>
                                            @else
                                                <span class="text-xs px-2 py-0.5 rounded-full bg-institutionnel/10 text-institutionnel">{{ __('À faire') }}</span>
                                            @endif
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </section>
                    @endif
                </div>

                <div class="space-y-6">
                    @if ($module->description || $module->objectifs)
                        <section class="bg-white rounded-lg shadow-sm p-6 text-sm space-y-3">
                            @if ($module->description)<p class="text-gray-700">{{ $module->description }}</p>@endif
                            @if ($module->objectifs)
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 mb-1">{{ __('À la fin du module, vous saurez') }}</p>
                                    <ul class="list-disc ps-5 text-gray-700 space-y-1">
                                        @foreach (preg_split('/\R/', trim($module->objectifs)) as $objectif)
                                            @if (trim($objectif))<li>{{ ltrim($objectif, '-• ') }}</li>@endif
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </section>
                    @endif

                    {{-- Quiz --}}
                    <section class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="font-medium text-gray-900 mb-3">{{ __('Quiz d\'entraînement') }}</h3>
                        <ul class="space-y-3 text-sm">
                            @forelse ($module->quiz as $quiz)
                                @php $meilleure = $quiz->tentatives->max('note'); @endphp
                                <li>
                                    <a href="{{ route('formation.quiz.show', $quiz) }}" class="flex items-center justify-between gap-2 hover:text-institutionnel">
                                        <span>
                                            <span class="block font-medium text-gray-900">{{ $quiz->titre }}</span>
                                            <span class="block text-xs text-gray-500">{{ $quiz->questions_count }} {{ __('question(s)') }}</span>
                                        </span>
                                        @if ($meilleure !== null)
                                            <span class="text-xs font-semibold {{ $meilleure >= 10 ? 'text-emerald-700' : 'text-amber-700' }}" title="{{ __('Meilleure note') }}">{{ number_format($meilleure, 1, ',', ' ') }}/20</span>
                                        @else
                                            <span class="text-xs text-institutionnel">{{ __('Faire') }} →</span>
                                        @endif
                                    </a>
                                </li>
                            @empty
                                <li class="text-gray-500">{{ __('Aucun quiz pour ce module.') }}</li>
                            @endforelse
                        </ul>
                    </section>

                    @if ($module->annonces->isNotEmpty())
                        <section class="bg-white rounded-lg shadow-sm p-6">
                            <h3 class="font-medium text-gray-900 mb-3">{{ __('Annonces') }}</h3>
                            <ul class="space-y-3 text-sm">
                                @foreach ($module->annonces as $annonce)
                                    <li class="border-l-2 border-institutionnel ps-3">
                                        <p class="font-medium text-gray-900">{{ $annonce->titre }}</p>
                                        <p class="text-gray-600 whitespace-pre-line">{{ $annonce->contenu }}</p>
                                        <p class="text-xs text-gray-400 mt-1">{{ $annonce->created_at->diffForHumans() }}</p>
                                    </li>
                                @endforeach
                            </ul>
                        </section>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
