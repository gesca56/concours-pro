<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Gestion pédagogique') }}</h2>
                <p class="text-sm text-gray-500">
                    {{ auth()->user()->role === \App\Enums\Role::Enseignant
                        ? __('Vos modules, vos cours en ligne et les copies de vos élèves-professeurs')
                        : __('Tous les modules de préparation et de formation de l\'IPNETP') }}
                </p>
            </div>
            <a href="{{ route('pedagogie.modules.create') }}" class="px-4 py-2 bg-institutionnel text-white text-sm font-semibold rounded-md hover:bg-institutionnel-hover">
                + {{ __('Nouveau module') }}
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <x-flash />

            {{-- Indicateurs --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach ([
                    ['label' => 'Modules', 'valeur' => $modules->count(), 'detail' => $modules->where('publie', true)->count().' publié(s)'],
                    ['label' => 'Leçons', 'valeur' => $modules->sum('lecons_count'), 'detail' => 'tous modules'],
                    ['label' => 'Quiz', 'valeur' => $modules->sum('quiz_count'), 'detail' => 'corrigés automatiquement'],
                    ['label' => 'Copies à corriger', 'valeur' => $rendusACorriger->count(), 'detail' => 'devoirs rendus'],
                ] as $tuile)
                    <div class="bg-white rounded-lg shadow-sm p-5">
                        <p class="text-xs text-gray-500 uppercase tracking-wide">{{ $tuile['label'] }}</p>
                        <p class="text-2xl font-bold text-marine mt-1">{{ $tuile['valeur'] }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $tuile['detail'] }}</p>
                    </div>
                @endforeach
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Modules --}}
                <section class="lg:col-span-2 bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="p-6 pb-3">
                        <h3 class="font-medium text-gray-900">{{ __('Modules') }}</h3>
                        <p class="text-xs text-gray-500">{{ __('Préparation au concours (candidats) et formation (élèves-professeurs)') }}</p>
                    </div>
                    <ul class="divide-y divide-gray-100">
                        @forelse ($modules as $module)
                            <li>
                                <a href="{{ route('pedagogie.modules.show', $module) }}" class="flex items-center justify-between gap-4 px-6 py-4 hover:bg-gray-50">
                                    <div class="min-w-0">
                                        <p class="font-medium text-gray-900 truncate">
                                            @if ($module->code)<span class="font-mono text-xs text-gray-500">{{ $module->code }}</span>@endif
                                            {{ $module->titre }}
                                        </p>
                                        <p class="text-xs text-gray-500 mt-0.5">
                                            {{ $module->libellePublic() }}
                                            @if ($module->enseignant) · {{ $module->enseignant->name }} @endif
                                        </p>
                                    </div>
                                    <div class="flex items-center gap-3 shrink-0 text-xs text-gray-500">
                                        <span title="{{ __('Leçons') }}">{{ $module->lecons_count }} {{ __('leç.') }}</span>
                                        <span title="{{ __('Quiz') }}">{{ $module->quiz_count }} {{ __('quiz') }}</span>
                                        @unless ($module->estPreparation())
                                            <span title="{{ __('Devoirs') }}">{{ $module->devoirs_count }} {{ __('dev.') }}</span>
                                        @endunless
                                        <span class="px-2 py-0.5 rounded-full font-medium {{ $module->publie ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-600' }}">
                                            {{ $module->publie ? __('Publié') : __('Brouillon') }}
                                        </span>
                                    </div>
                                </a>
                            </li>
                        @empty
                            <li class="px-6 py-8 text-sm text-gray-500">
                                {{ __('Aucun module pour le moment.') }}
                                <a href="{{ route('pedagogie.modules.create') }}" class="text-institutionnel underline">{{ __('Créer le premier module') }}</a>.
                            </li>
                        @endforelse
                    </ul>
                </section>

                <div class="space-y-6">
                    {{-- Copies à corriger --}}
                    <section class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="font-medium text-gray-900 mb-3">{{ __('Copies à corriger') }}</h3>
                        <ul class="space-y-3 text-sm">
                            @forelse ($rendusACorriger->take(6) as $rendu)
                                <li>
                                    <a href="{{ route('pedagogie.devoirs.show', $rendu->devoir) }}#eleve-{{ $rendu->user_id }}" class="block hover:text-institutionnel">
                                        <span class="font-medium text-gray-900">{{ $rendu->eleve->name }}</span>
                                        @if ($rendu->en_retard)<span class="text-xs text-amber-700">· {{ __('en retard') }}</span>@endif
                                        <span class="block text-xs text-gray-500">{{ $rendu->devoir->titre }} · {{ $rendu->rendu_le->format('d/m H:i') }}</span>
                                    </a>
                                </li>
                            @empty
                                <li class="text-gray-500">{{ __('Aucune copie en attente.') }}</li>
                            @endforelse
                        </ul>
                    </section>

                    {{-- Prochaines séances --}}
                    <section class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="font-medium text-gray-900 mb-3">{{ __('Prochaines séances') }}</h3>
                        <ul class="space-y-3 text-sm">
                            @forelse ($seances as $seance)
                                <li>
                                    <p class="font-medium text-gray-900">{{ $seance->module?->titre ?? $seance->libelleType() }}</p>
                                    <p class="text-xs text-gray-500">
                                        {{ $seance->debut->translatedFormat('D d/m') }} · {{ $seance->debut->format('H\hi') }}–{{ $seance->fin->format('H\hi') }}
                                        · {{ $seance->promotion->code }} @if ($seance->salle) · {{ $seance->salle }} @endif
                                    </p>
                                </li>
                            @empty
                                <li class="text-gray-500">{{ __('Aucune séance programmée.') }}</li>
                            @endforelse
                        </ul>
                    </section>

                    @if ($promotions->isNotEmpty())
                        <section class="bg-white rounded-lg shadow-sm p-6">
                            <h3 class="font-medium text-gray-900 mb-3">{{ __('Promotions en cours') }}</h3>
                            <ul class="space-y-2 text-sm">
                                @foreach ($promotions as $promotion)
                                    <li class="flex justify-between">
                                        <a href="{{ route('administration.promotions.show', $promotion) }}" class="text-institutionnel hover:underline">{{ $promotion->nom }}</a>
                                        <span class="text-gray-500">{{ $promotion->eleves_count }} {{ __('élève(s)') }}</span>
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
