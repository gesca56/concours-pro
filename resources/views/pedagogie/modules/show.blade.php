<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-xs font-semibold text-institutionnel">{{ $module->libellePublic() }}</p>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    @if ($module->code)<span class="font-mono text-base text-gray-500">{{ $module->code }}</span>@endif
                    {{ $module->titre }}
                </h2>
                <p class="text-sm text-gray-500">
                    {{ $module->enseignant?->name ?? __('Aucun enseignant désigné') }}
                    @unless ($module->estPreparation()) · {{ __('coefficient') }} {{ $module->coefficient }} @if ($module->volume_horaire) · {{ $module->volume_horaire }} h @endif @endunless
                </p>
            </div>
            <div class="flex items-center gap-3">
                <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $module->publie ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-600' }}">
                    {{ $module->publie ? __('Publié') : __('Brouillon — invisible des apprenants') }}
                </span>
                <a href="{{ route('pedagogie.modules.edit', $module) }}" class="px-4 py-2 border border-gray-300 text-sm rounded-md hover:bg-gray-50">{{ __('Modifier') }}</a>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <x-flash />

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 space-y-6">
                    {{-- Leçons --}}
                    <section class="bg-white rounded-lg shadow-sm overflow-hidden">
                        <div class="p-6 pb-3 flex items-center justify-between">
                            <div>
                                <h3 class="font-medium text-gray-900">{{ __('Leçons') }}</h3>
                                <p class="text-xs text-gray-500">{{ __('Rédigées sur le site, avec vidéo YouTube et lien de ressource facultatifs') }}</p>
                            </div>
                            <a href="{{ route('pedagogie.lecons.create', $module) }}" class="text-sm font-semibold text-institutionnel hover:underline">+ {{ __('Ajouter') }}</a>
                        </div>
                        <ol class="divide-y divide-gray-100">
                            @forelse ($module->lecons as $lecon)
                                <li class="px-6 py-3 flex items-center justify-between gap-4">
                                    <div class="min-w-0 flex items-start gap-3">
                                        <span class="w-6 h-6 shrink-0 rounded-full bg-gray-100 text-xs font-semibold text-gray-600 flex items-center justify-center">{{ $loop->iteration }}</span>
                                        <div class="min-w-0">
                                            <p class="font-medium text-gray-900 truncate">{{ $lecon->titre }}</p>
                                            <p class="text-xs text-gray-500">
                                                @if ($lecon->duree_minutes){{ $lecon->duree_minutes }} min · @endif
                                                @if ($lecon->video_url){{ __('vidéo') }} · @endif
                                                @if ($lecon->lien_ressource){{ __('ressource') }} · @endif
                                                {{ $lecon->publiee ? __('publiée') : __('masquée') }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3 text-sm shrink-0">
                                        <a href="{{ route('pedagogie.lecons.edit', $lecon) }}" class="text-institutionnel hover:underline">{{ __('Modifier') }}</a>
                                        <form method="POST" action="{{ route('pedagogie.lecons.destroy', $lecon) }}" onsubmit="return confirm('Supprimer cette leçon ?')">
                                            @csrf @method('DELETE')
                                            <button class="text-red-600 hover:underline">{{ __('Supprimer') }}</button>
                                        </form>
                                    </div>
                                </li>
                            @empty
                                <li class="px-6 py-6 text-sm text-gray-500">{{ __('Aucune leçon. Commencez par rédiger la première.') }}</li>
                            @endforelse
                        </ol>
                    </section>

                    {{-- Quiz --}}
                    <section class="bg-white rounded-lg shadow-sm overflow-hidden">
                        <div class="p-6 pb-3 flex items-center justify-between">
                            <div>
                                <h3 class="font-medium text-gray-900">{{ __('Quiz d\'entraînement') }}</h3>
                                <p class="text-xs text-gray-500">{{ __('QCM corrigés automatiquement, avec explications') }}</p>
                            </div>
                            <a href="{{ route('pedagogie.quiz.create', $module) }}" class="text-sm font-semibold text-institutionnel hover:underline">+ {{ __('Ajouter') }}</a>
                        </div>
                        <ul class="divide-y divide-gray-100">
                            @forelse ($module->quiz as $quiz)
                                <li>
                                    <a href="{{ route('pedagogie.quiz.show', $quiz) }}" class="px-6 py-3 flex items-center justify-between gap-4 hover:bg-gray-50">
                                        <span class="font-medium text-gray-900">{{ $quiz->titre }}</span>
                                        <span class="text-xs text-gray-500">
                                            {{ $quiz->questions_count }} {{ __('question(s)') }} · {{ $quiz->tentatives_count }} {{ __('tentative(s)') }}
                                            · {{ $quiz->publie ? __('publié') : __('brouillon') }}
                                        </span>
                                    </a>
                                </li>
                            @empty
                                <li class="px-6 py-6 text-sm text-gray-500">{{ __('Aucun quiz.') }}</li>
                            @endforelse
                        </ul>
                    </section>

                    {{-- Devoirs (formation uniquement) --}}
                    @unless ($module->estPreparation())
                        <section class="bg-white rounded-lg shadow-sm overflow-hidden">
                            <div class="p-6 pb-3 flex items-center justify-between">
                                <div>
                                    <h3 class="font-medium text-gray-900">{{ __('Devoirs notés') }}</h3>
                                    <p class="text-xs text-gray-500">{{ __('Notés sur 20, ils forment la moyenne du module') }}</p>
                                </div>
                                <a href="{{ route('pedagogie.devoirs.create', $module) }}" class="text-sm font-semibold text-institutionnel hover:underline">+ {{ __('Ajouter') }}</a>
                            </div>
                            <ul class="divide-y divide-gray-100">
                                @forelse ($module->devoirs as $devoir)
                                    <li>
                                        <a href="{{ route('pedagogie.devoirs.show', $devoir) }}" class="px-6 py-3 flex items-center justify-between gap-4 hover:bg-gray-50">
                                            <span>
                                                <span class="font-medium text-gray-900">{{ $devoir->titre }}</span>
                                                <span class="block text-xs text-gray-500">{{ __('À rendre le') }} {{ $devoir->date_limite->format('d/m/Y à H\hi') }}</span>
                                            </span>
                                            <span class="text-xs text-gray-500 text-right">
                                                {{ $devoir->rendus_count }} {{ __('copie(s)') }}
                                                @if ($devoir->rendus_a_corriger_count)
                                                    <span class="block text-amber-700 font-medium">{{ $devoir->rendus_a_corriger_count }} {{ __('à corriger') }}</span>
                                                @endif
                                            </span>
                                        </a>
                                    </li>
                                @empty
                                    <li class="px-6 py-6 text-sm text-gray-500">{{ __('Aucun devoir.') }}</li>
                                @endforelse
                            </ul>
                        </section>
                    @endunless
                </div>

                <div class="space-y-6">
                    @if ($module->description || $module->objectifs)
                        <section class="bg-white rounded-lg shadow-sm p-6 text-sm space-y-3">
                            @if ($module->description)<p class="text-gray-700">{{ $module->description }}</p>@endif
                            @if ($module->objectifs)
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 mb-1">{{ __('Objectifs') }}</p>
                                    <ul class="list-disc ps-5 text-gray-700 space-y-1">
                                        @foreach (preg_split('/\R/', trim($module->objectifs)) as $objectif)
                                            @if (trim($objectif))<li>{{ ltrim($objectif, '-• ') }}</li>@endif
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </section>
                    @endif

                    {{-- Annonces --}}
                    <section class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="font-medium text-gray-900 mb-3">{{ __('Annonces aux apprenants') }}</h3>
                        <form method="POST" action="{{ route('pedagogie.annonces.store', $module) }}" class="space-y-2">
                            @csrf
                            <x-text-input name="titre" class="block w-full text-sm" placeholder="Titre" required />
                            <textarea name="contenu" rows="3" required class="block w-full text-sm border-gray-300 rounded-md shadow-sm" placeholder="Message"></textarea>
                            <x-input-error :messages="$errors->get('contenu')" />
                            <button class="w-full px-4 py-2 bg-institutionnel text-white text-xs rounded-md font-semibold uppercase tracking-widest hover:bg-institutionnel-hover">{{ __('Publier') }}</button>
                        </form>
                        <ul class="mt-4 space-y-3 text-sm">
                            @foreach ($module->annonces as $annonce)
                                <li class="border-t border-gray-100 pt-3">
                                    <div class="flex justify-between gap-2">
                                        <p class="font-medium text-gray-900">{{ $annonce->titre }}</p>
                                        <form method="POST" action="{{ route('pedagogie.annonces.destroy', $annonce) }}">
                                            @csrf @method('DELETE')
                                            <button class="text-xs text-red-600 hover:underline" title="{{ __('Retirer') }}">✕</button>
                                        </form>
                                    </div>
                                    <p class="text-gray-600 whitespace-pre-line">{{ $annonce->contenu }}</p>
                                    <p class="text-xs text-gray-400 mt-1">{{ $annonce->auteur->name }} · {{ $annonce->created_at->diffForHumans() }}</p>
                                </li>
                            @endforeach
                        </ul>
                    </section>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
