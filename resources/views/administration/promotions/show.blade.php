<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <a href="{{ route('administration.promotions.index') }}" class="text-xs font-semibold text-institutionnel hover:underline">← {{ __('Promotions') }}</a>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $promotion->nom }}</h2>
                <p class="text-sm text-gray-500">
                    <span class="font-mono">{{ $promotion->code }}</span> · {{ $promotion->cycle }} · {{ $promotion->annee_academique }}
                    @if ($promotion->date_debut) · {{ __('du') }} {{ $promotion->date_debut->format('d/m/Y') }} @endif
                    @if ($promotion->date_fin) {{ __('au') }} {{ $promotion->date_fin->format('d/m/Y') }} @endif
                </p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('pedagogie.modules.create') }}" class="px-4 py-2 border border-gray-300 text-sm rounded-md hover:bg-gray-50">+ {{ __('Module') }}</a>
                <a href="{{ route('administration.promotions.edit', $promotion) }}" class="px-4 py-2 border border-gray-300 text-sm rounded-md hover:bg-gray-50">{{ __('Modifier') }}</a>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <x-flash />
            @if ($errors->any())
                <div class="p-4 rounded-md bg-red-50 text-sm text-red-700">{{ $errors->first() }}</div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 space-y-6">
                    {{-- Modules --}}
                    <section class="bg-white rounded-lg shadow-sm overflow-hidden">
                        <h3 class="font-medium text-gray-900 p-6 pb-3">{{ __('Modules de formation') }}</h3>
                        <ul class="divide-y divide-gray-100">
                            @forelse ($promotion->modules as $module)
                                <li>
                                    <a href="{{ route('pedagogie.modules.show', $module) }}" class="px-6 py-3 flex items-center justify-between gap-4 hover:bg-gray-50 text-sm">
                                        <span>
                                            <span class="font-medium text-gray-900">{{ $module->titre }}</span>
                                            <span class="block text-xs text-gray-500">{{ $module->enseignant?->name ?? __('Enseignant à désigner') }} · coef. {{ $module->coefficient }}@if ($module->volume_horaire) · {{ $module->volume_horaire }} h @endif</span>
                                        </span>
                                        <span class="text-xs text-gray-500">{{ $module->lecons_count }} {{ __('leçon(s)') }} · {{ $module->devoirs_count }} {{ __('devoir(s)') }}</span>
                                    </a>
                                </li>
                            @empty
                                <li class="px-6 py-6 text-sm text-gray-500">{{ __('Aucun module : créez-en depuis la gestion pédagogique.') }}</li>
                            @endforelse
                        </ul>
                    </section>

                    {{-- Emploi du temps --}}
                    <section class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="font-medium text-gray-900">{{ __('Emploi du temps') }}</h3>
                        <p class="text-xs text-gray-500 mb-4">{{ __('Séances à partir de la semaine en cours') }}</p>

                        <form method="POST" action="{{ route('administration.seances.store', $promotion) }}" class="grid sm:grid-cols-6 gap-3 p-4 rounded-lg bg-gray-50 text-sm">
                            @csrf
                            <select name="module_id" class="sm:col-span-3 border-gray-300 rounded-md text-sm">
                                <option value="">— {{ __('Sans module (réunion, examen…)') }} —</option>
                                @foreach ($promotion->modules as $module)
                                    <option value="{{ $module->id }}">{{ $module->titre }}</option>
                                @endforeach
                            </select>
                            <select name="type" class="sm:col-span-3 border-gray-300 rounded-md text-sm">
                                @foreach ($typesSeance as $valeur => $libelle)
                                    <option value="{{ $valeur }}">{{ $libelle }}</option>
                                @endforeach
                            </select>
                            <input type="date" name="jour" required value="{{ old('jour', now()->toDateString()) }}" class="sm:col-span-2 border-gray-300 rounded-md text-sm">
                            <input type="time" name="heure_debut" required value="{{ old('heure_debut', '08:00') }}" class="border-gray-300 rounded-md text-sm">
                            <input type="time" name="heure_fin" required value="{{ old('heure_fin', '10:00') }}" class="border-gray-300 rounded-md text-sm">
                            <input type="text" name="salle" placeholder="{{ __('Salle') }}" class="sm:col-span-2 border-gray-300 rounded-md text-sm">
                            <input type="url" name="lien_visio" placeholder="{{ __('Lien de classe virtuelle (Meet, Zoom…)') }}" class="sm:col-span-4 border-gray-300 rounded-md text-sm">
                            <label class="sm:col-span-2 flex items-center gap-2 text-xs text-gray-600">
                                {{ __('Répéter') }}
                                <input type="number" name="repetitions" min="1" max="20" value="1" class="w-16 border-gray-300 rounded-md text-sm">
                                {{ __('semaine(s)') }}
                            </label>
                            <button class="sm:col-span-6 px-4 py-2 bg-institutionnel text-white text-xs rounded-md font-semibold uppercase tracking-widest hover:bg-institutionnel-hover">{{ __('Ajouter la séance') }}</button>
                        </form>

                        <ul class="mt-4 divide-y divide-gray-100 text-sm">
                            @forelse ($promotion->seances->take(20) as $seance)
                                <li class="py-2 flex items-center justify-between gap-4">
                                    <span>
                                        <span class="font-medium text-gray-900">{{ ucfirst($seance->debut->translatedFormat('l d/m')) }}</span>
                                        <span class="text-gray-500">{{ $seance->debut->format('H\hi') }}–{{ $seance->fin->format('H\hi') }}</span>
                                        <span class="block text-xs text-gray-500">{{ $seance->module?->titre ?? '—' }} · {{ $seance->libelleType() }}@if ($seance->salle) · {{ $seance->salle }}@endif</span>
                                    </span>
                                    <form method="POST" action="{{ route('administration.seances.destroy', $seance) }}">
                                        @csrf @method('DELETE')
                                        <button class="text-xs text-red-600 hover:underline">{{ __('Retirer') }}</button>
                                    </form>
                                </li>
                            @empty
                                <li class="py-3 text-gray-500">{{ __('Aucune séance programmée.') }}</li>
                            @endforelse
                        </ul>
                    </section>
                </div>

                <div class="space-y-6">
                    {{-- Élèves --}}
                    <section class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="font-medium text-gray-900">{{ __('Élèves-professeurs') }} <span class="text-gray-400 font-normal">({{ $promotion->eleves->count() }})</span></h3>

                        @if ($concours->isNotEmpty())
                            <form method="POST" action="{{ route('administration.promotions.inscrire', $promotion) }}" class="mt-3 flex gap-2">
                                @csrf
                                <select name="concours_id" class="flex-1 min-w-0 border-gray-300 rounded-md text-sm">
                                    @foreach ($concours as $c)
                                        <option value="{{ $c->id }}" @selected($promotion->concours_id === $c->id)>{{ $c->nom }} ({{ $c->admis_count }})</option>
                                    @endforeach
                                </select>
                                <button class="px-3 py-2 bg-institutionnel text-white text-xs rounded-md font-semibold hover:bg-institutionnel-hover">{{ __('Inscrire les admis') }}</button>
                            </form>
                        @else
                            <p class="mt-2 text-xs text-gray-500">{{ __('Aucun concours n\'a encore d\'admis à inscrire.') }}</p>
                        @endif

                        <ul class="mt-4 divide-y divide-gray-100 text-sm">
                            @forelse ($promotion->eleves as $eleve)
                                <li class="py-2 flex items-center justify-between gap-2">
                                    <span class="min-w-0">
                                        <span class="block font-medium text-gray-900 truncate">{{ $eleve->name }}</span>
                                        <span class="block text-xs text-gray-500 font-mono">{{ $eleve->pivot->matricule }}</span>
                                    </span>
                                    <form method="POST" action="{{ route('administration.promotions.retirer', [$promotion, $eleve]) }}" onsubmit="return confirm('Retirer cet élève de la promotion ?')">
                                        @csrf @method('DELETE')
                                        <button class="text-xs text-red-600 hover:underline">{{ __('Retirer') }}</button>
                                    </form>
                                </li>
                            @empty
                                <li class="py-3 text-gray-500">{{ __('Aucun élève inscrit.') }}</li>
                            @endforelse
                        </ul>
                    </section>

                    {{-- Annonces --}}
                    <section class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="font-medium text-gray-900 mb-3">{{ __('Annonce à la promotion') }}</h3>
                        <form method="POST" action="{{ route('administration.promotions.annoncer', $promotion) }}" class="space-y-2">
                            @csrf
                            <x-text-input name="titre" class="block w-full text-sm" placeholder="Titre" required />
                            <textarea name="contenu" rows="3" required class="block w-full text-sm border-gray-300 rounded-md shadow-sm" placeholder="Message"></textarea>
                            <button class="w-full px-4 py-2 bg-institutionnel text-white text-xs rounded-md font-semibold uppercase tracking-widest hover:bg-institutionnel-hover">{{ __('Envoyer') }}</button>
                        </form>
                        <ul class="mt-4 space-y-3 text-sm">
                            @foreach ($promotion->annonces as $annonce)
                                <li class="border-t border-gray-100 pt-3">
                                    <div class="flex justify-between gap-2">
                                        <p class="font-medium text-gray-900">{{ $annonce->titre }}</p>
                                        <form method="POST" action="{{ route('pedagogie.annonces.destroy', $annonce) }}">
                                            @csrf @method('DELETE')
                                            <button class="text-xs text-red-600" title="{{ __('Retirer') }}">✕</button>
                                        </form>
                                    </div>
                                    <p class="text-gray-600 whitespace-pre-line">{{ $annonce->contenu }}</p>
                                    <p class="text-xs text-gray-400 mt-1">{{ $annonce->created_at->diffForHumans() }}</p>
                                </li>
                            @endforeach
                        </ul>
                    </section>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
