<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('E-learning') }}</h2>
                <p class="text-sm text-gray-500">
                    {{ $promotion
                        ? __('Votre formation d\'élève-professeur et vos cours en ligne')
                        : __('Cours et quiz gratuits pour préparer les concours directs de l\'IPNETP') }}
                </p>
            </div>
            @if ($promotion)
                <div class="flex gap-2 text-sm">
                    <a href="{{ route('formation.emploi-du-temps') }}" class="px-4 py-2 border border-gray-300 rounded-md hover:bg-white">{{ __('Emploi du temps') }}</a>
                    <a href="{{ route('formation.notes') }}" class="px-4 py-2 bg-institutionnel text-white font-semibold rounded-md hover:bg-institutionnel-hover">{{ __('Mes notes') }}</a>
                </div>
            @endif
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            <x-flash />

            @if ($promotion)
                {{-- Bandeau promotion --}}
                <section class="rounded-xl bg-gradient-to-r from-marine to-marine-light text-white p-6 flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <p class="text-xs uppercase tracking-wider text-blue-200">{{ __('Élève-professeur') }} · {{ $promotion->annee_academique }}</p>
                        <p class="text-lg font-semibold mt-1">{{ $promotion->nom }}</p>
                        <p class="text-sm text-blue-100">{{ __('Matricule') }} <span class="font-mono">{{ auth()->user()->promotions->firstWhere('id', $promotion->id)?->pivot->matricule }}</span></p>
                    </div>
                    <div class="text-right">
                        <p class="text-3xl font-bold">{{ $modulesFormation->count() }}</p>
                        <p class="text-xs text-blue-200">{{ __('module(s) de formation') }}</p>
                    </div>
                </section>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    {{-- Modules de formation --}}
                    <section class="lg:col-span-2 space-y-4">
                        <h3 class="font-semibold text-gray-900">{{ __('Mes modules de formation') }}</h3>
                        <div class="grid sm:grid-cols-2 gap-4">
                            @forelse ($modulesFormation as $module)
                                @php
                                    $total = $module->lecons->where('publiee', true)->count();
                                    $faites = $module->lecons->whereIn('id', $leconsTerminees)->count();
                                    $pourcentage = $total ? round($faites / $total * 100) : 0;
                                @endphp
                                <a href="{{ route('formation.modules.show', $module) }}" class="bg-white rounded-lg shadow-sm p-5 hover:ring-2 hover:ring-institutionnel/40 flex flex-col">
                                    <p class="text-xs text-gray-500">@if ($module->code)<span class="font-mono">{{ $module->code }}</span> · @endif{{ __('coef.') }} {{ $module->coefficient }}</p>
                                    <p class="font-semibold text-marine mt-1">{{ $module->titre }}</p>
                                    <p class="text-xs text-gray-500 mt-0.5">{{ $module->enseignant?->name }}</p>
                                    <div class="mt-auto pt-4">
                                        <div class="flex justify-between text-xs text-gray-500 mb-1"><span>{{ $faites }}/{{ $total }} {{ __('leçon(s)') }}</span><span>{{ $pourcentage }} %</span></div>
                                        <div class="h-1.5 rounded-full bg-gray-100"><div class="h-1.5 rounded-full bg-emerald-500" style="width: {{ $pourcentage }}%"></div></div>
                                    </div>
                                </a>
                            @empty
                                <p class="text-sm text-gray-500">{{ __('Vos modules seront publiés ici par vos enseignants.') }}</p>
                            @endforelse
                        </div>
                    </section>

                    <div class="space-y-6">
                        {{-- Devoirs --}}
                        <section class="bg-white rounded-lg shadow-sm p-6">
                            <h3 class="font-medium text-gray-900 mb-3">{{ __('Devoirs à rendre') }}</h3>
                            <ul class="space-y-3 text-sm">
                                @forelse ($devoirs as $devoir)
                                    <li>
                                        <a href="{{ route('formation.devoirs.show', $devoir) }}" class="block hover:text-institutionnel">
                                            <span class="font-medium text-gray-900">{{ $devoir->titre }}</span>
                                            <span class="block text-xs {{ $devoir->estEchu() ? 'text-red-600' : ($devoir->date_limite->diffInDays() < 3 ? 'text-amber-700' : 'text-gray-500') }}">
                                                {{ $devoir->module->titre }} · {{ $devoir->estEchu() ? __('en retard depuis le') : __('avant le') }} {{ $devoir->date_limite->format('d/m à H\hi') }}
                                            </span>
                                        </a>
                                    </li>
                                @empty
                                    <li class="text-gray-500">{{ __('Rien à rendre pour le moment.') }}</li>
                                @endforelse
                            </ul>
                        </section>

                        {{-- Séances --}}
                        <section class="bg-white rounded-lg shadow-sm p-6">
                            <h3 class="font-medium text-gray-900 mb-3">{{ __('Prochaines séances') }}</h3>
                            <ul class="space-y-3 text-sm">
                                @forelse ($seances as $seance)
                                    <li class="flex gap-3">
                                        <div class="w-12 shrink-0 text-center rounded-md bg-institutionnel/10 py-1">
                                            <p class="text-[10px] uppercase text-institutionnel">{{ $seance->debut->translatedFormat('D') }}</p>
                                            <p class="font-bold text-marine leading-none">{{ $seance->debut->format('d') }}</p>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="font-medium text-gray-900 truncate">{{ $seance->module?->titre ?? $seance->libelleType() }}</p>
                                            <p class="text-xs text-gray-500">{{ $seance->debut->format('H\hi') }}–{{ $seance->fin->format('H\hi') }} · {{ $seance->libelleType() }}@if ($seance->salle) · {{ $seance->salle }}@endif</p>
                                            @if ($seance->lien_visio)<a href="{{ $seance->lien_visio }}" target="_blank" rel="noopener noreferrer" class="text-xs text-institutionnel underline">{{ __('Rejoindre la classe virtuelle') }}</a>@endif
                                        </div>
                                    </li>
                                @empty
                                    <li class="text-gray-500">{{ __('Aucune séance à venir.') }}</li>
                                @endforelse
                            </ul>
                        </section>

                        {{-- Annonces --}}
                        @if ($annonces->isNotEmpty())
                            <section class="bg-white rounded-lg shadow-sm p-6">
                                <h3 class="font-medium text-gray-900 mb-3">{{ __('Annonces') }}</h3>
                                <ul class="space-y-3 text-sm">
                                    @foreach ($annonces as $annonce)
                                        <li class="border-l-2 border-institutionnel ps-3">
                                            <p class="font-medium text-gray-900">{{ $annonce->titre }}</p>
                                            <p class="text-gray-600 whitespace-pre-line">{{ $annonce->contenu }}</p>
                                            <p class="text-xs text-gray-400 mt-1">{{ $annonce->auteur->name }}@if ($annonce->module) · {{ $annonce->module->titre }}@endif · {{ $annonce->created_at->diffForHumans() }}</p>
                                        </li>
                                    @endforeach
                                </ul>
                            </section>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Préparation au concours --}}
            <section class="space-y-4">
                <div>
                    <h3 class="font-semibold text-gray-900">{{ __('Préparation aux concours') }}</h3>
                    <p class="text-sm text-gray-500">{{ __('Cours de méthode, de culture générale et de pédagogie, avec quiz d\'entraînement corrigés.') }}</p>
                </div>
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @forelse ($modulesPreparation as $module)
                        @php
                            $total = $module->lecons->where('publiee', true)->count();
                            $faites = $module->lecons->whereIn('id', $leconsTerminees)->count();
                            $pourcentage = $total ? round($faites / $total * 100) : 0;
                        @endphp
                        <a href="{{ route('formation.modules.show', $module) }}" class="bg-white rounded-lg shadow-sm p-5 hover:ring-2 hover:ring-institutionnel/40 flex flex-col">
                            <p class="text-xs font-semibold text-institutionnel">{{ $module->cycle ?? __('Tous concours') }}</p>
                            <p class="font-semibold text-marine mt-1">{{ $module->titre }}</p>
                            @if ($module->description)<p class="text-sm text-gray-600 mt-1 line-clamp-2">{{ $module->description }}</p>@endif
                            <div class="mt-auto pt-4">
                                <div class="flex justify-between text-xs text-gray-500 mb-1">
                                    <span>{{ $total }} {{ __('leçon(s)') }} · {{ $module->quiz_count }} {{ __('quiz') }}</span>
                                    <span>{{ $pourcentage }} %</span>
                                </div>
                                <div class="h-1.5 rounded-full bg-gray-100"><div class="h-1.5 rounded-full bg-institutionnel" style="width: {{ $pourcentage }}%"></div></div>
                            </div>
                        </a>
                    @empty
                        <p class="text-sm text-gray-500">{{ __('Les premiers cours de préparation arrivent bientôt.') }}</p>
                    @endforelse
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
