<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Bonjour') }} {{ Str::before(auth()->user()->name, ' ') ?: auth()->user()->name }}
            </h2>
            <p class="text-sm text-gray-500">{{ __('Votre espace candidat aux concours directs de l\'IPNETP') }}</p>
        </div>
    </x-slot>

    @php
        $tons = [
            'action' => ['cadre' => 'border-institutionnel/30 bg-institutionnel/5', 'pastille' => 'bg-institutionnel', 'libelle' => 'À faire maintenant'],
            'attente' => ['cadre' => 'border-amber-200 bg-amber-50', 'pastille' => 'bg-amber-500', 'libelle' => 'En attente'],
            'succes' => ['cadre' => 'border-emerald-200 bg-emerald-50', 'pastille' => 'bg-emerald-500', 'libelle' => 'Résultat'],
            'echec' => ['cadre' => 'border-red-200 bg-red-50', 'pastille' => 'bg-red-500', 'libelle' => 'Résultat'],
        ];
    @endphp

    <div class="py-10">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 grid lg:grid-cols-3 gap-6">

            <div class="lg:col-span-2 space-y-6">
                @forelse ($candidatures as $candidature)
                    @php
                        $etape = $candidature->prochaineEtape();
                        $ton = $tons[$etape['ton']];
                        $etapes = $candidature->etapesParcours();
                        $cycle = config('ipnetp.cycles.'.$candidature->concours->cycle);
                    @endphp

                    <section class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                        <div class="p-6 flex items-start justify-between gap-4">
                            <div class="min-w-0">
                                <p class="text-xs font-semibold text-institutionnel flex items-center gap-1.5">
                                    @if ($cycle)<span class="w-2.5 h-2.5 rounded-sm {{ $cycle['chemise']['classe'] }}"></span>@endif
                                    {{ $candidature->concours->cycle }} · {{ __('Dossier n°') }} {{ str_pad($candidature->id, 5, '0', STR_PAD_LEFT) }}
                                </p>
                                <h3 class="font-semibold text-gray-900 mt-1">{{ $candidature->concours->nom }}</h3>
                            </div>
                            <x-statut-badge :statut="$candidature->statut" />
                        </div>

                        {{-- Prochaine étape --}}
                        <div class="mx-6 p-4 rounded-lg border {{ $ton['cadre'] }}">
                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full {{ $ton['pastille'] }}"></span>{{ $ton['libelle'] }}
                            </p>
                            <p class="font-semibold text-marine mt-1">{{ $etape['titre'] }}</p>
                            <p class="text-sm text-gray-600 mt-1">{{ $etape['texte'] }}</p>
                            @isset($etape['action'])
                                <a href="{{ $etape['lien'] }}" class="inline-block mt-3 px-4 py-2 bg-institutionnel text-white text-sm font-semibold rounded-md hover:bg-institutionnel-hover">{{ $etape['action'] }}</a>
                            @endisset
                        </div>

                        {{-- Parcours --}}
                        <ol class="px-6 pt-6 pb-2 grid grid-cols-6 gap-1">
                            @foreach ($etapes as $i => $e)
                                <li class="text-center">
                                    <div class="h-1.5 rounded-full {{ $e['faite'] ? 'bg-emerald-500' : 'bg-gray-200' }}"></div>
                                    <p class="mt-2 text-[11px] leading-tight {{ $e['faite'] ? 'text-gray-700' : 'text-gray-400' }}">{{ $e['libelle'] }}</p>
                                </li>
                            @endforeach
                        </ol>

                        {{-- Dates clés --}}
                        <dl class="px-6 py-4 grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm border-t border-gray-100 mt-4">
                            <div>
                                <dt class="text-xs text-gray-500">{{ __('Clôture') }}</dt>
                                <dd class="font-medium text-gray-900">{{ $candidature->concours->date_cloture->translatedFormat('j M Y') }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs text-gray-500">{{ __('Écrits') }}</dt>
                                <dd class="font-medium text-gray-900">{{ $candidature->concours->date_concours?->translatedFormat('j M Y') ?? __('À fixer') }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs text-gray-500">{{ __('Visite médicale') }}</dt>
                                <dd class="font-medium text-gray-900">{{ $candidature->visite_medicale_programmee_le?->translatedFormat('j M Y') ?? '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs text-gray-500">{{ __('N° d\'anonymat') }}</dt>
                                <dd class="font-medium text-gray-900">{{ $candidature->numero_anonymat ?? '—' }}</dd>
                            </div>
                        </dl>

                        <div class="px-6 py-3 bg-gray-50 flex flex-wrap gap-x-5 gap-y-2 text-sm">
                            <a href="{{ route('candidatures.show', $candidature) }}" class="font-medium text-institutionnel hover:text-institutionnel-hover">{{ __('Ouvrir le dossier') }} →</a>
                            <a href="{{ route('candidatures.fiche', $candidature) }}" target="_blank" class="text-gray-600 hover:text-gray-900">{{ __('Fiche d\'inscription (PDF)') }}</a>
                            @if ($candidature->jeton_convocation)
                                <a href="{{ route('candidatures.convocation', $candidature) }}" target="_blank" class="text-gray-600 hover:text-gray-900">{{ __('Convocation (PDF)') }}</a>
                            @endif
                        </div>
                    </section>
                @empty
                    <section class="bg-white shadow-sm sm:rounded-lg p-8 text-center">
                        <p class="font-semibold text-marine">{{ __("Vous n'avez pas encore de candidature.") }}</p>
                        <p class="text-sm text-gray-500 mt-1 mb-5">{{ __('Choisissez le concours qui correspond à votre diplôme le plus élevé dans votre spécialité.') }}</p>
                        <a href="{{ route('candidatures.create') }}"
                           class="inline-flex items-center px-4 py-2 bg-institutionnel rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-institutionnel-hover">
                            {{ __("S'inscrire à un concours") }}
                        </a>
                    </section>
                @endforelse

                @if ($concoursOuverts->isNotEmpty())
                    <section>
                        <h3 class="font-semibold text-gray-900 mb-3">{{ $candidatures->isEmpty() ? __('Concours ouverts') : __('Autres concours ouverts') }}</h3>
                        <div class="grid sm:grid-cols-2 gap-4">
                            @foreach ($concoursOuverts->take(4) as $c)
                                @include('pages.partials.carte-concours', ['c' => $c])
                            @endforeach
                        </div>
                    </section>
                @endif
            </div>

            {{-- Colonne d'aide --}}
            <aside class="space-y-6">
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <h3 class="font-semibold text-gray-900 mb-3">{{ __('Bon à savoir') }}</h3>
                    <ul class="space-y-3 text-sm text-gray-600">
                        <li>{{ __('La préinscription en ligne ne suffit pas : le dossier physique doit aussi être déposé au secrétariat.') }}</li>
                        <li>{{ __('Le casier judiciaire doit dater de moins de 3 mois au moment du dépôt.') }}</li>
                        <li>{{ __('Les copies sont corrigées sous numéro d\'anonymat : ne signez jamais votre copie.') }}</li>
                    </ul>
                </div>
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <h3 class="font-semibold text-gray-900 mb-3">{{ __('Ressources') }}</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('pages.guide') }}#dossier" class="text-institutionnel hover:underline">{{ __('Liste des pièces à fournir') }}</a></li>
                        <li><a href="{{ route('pages.preparation') }}" class="text-institutionnel hover:underline">{{ __('Préparer les épreuves') }}</a></li>
                        <li><a href="{{ route('pages.concours') }}" class="text-institutionnel hover:underline">{{ __('Les quatre concours') }}</a></li>
                        <li><a href="{{ route('pages.guide') }}#faq" class="text-institutionnel hover:underline">{{ __('Questions fréquentes') }}</a></li>
                    </ul>
                </div>
                <div class="rounded-lg bg-marine text-slate-300 p-6 text-sm">
                    <h3 class="font-semibold text-white mb-2">{{ __('Secrétariat des concours') }}</h3>
                    <p>{{ config('ipnetp.institut.secretariat') }}</p>
                    <p class="mt-2">
                        @foreach (config('ipnetp.institut.telephones') as $tel)
                            <a href="tel:{{ str_replace(' ', '', $tel) }}" class="block hover:text-white">{{ $tel }}</a>
                        @endforeach
                    </p>
                    <p class="mt-2 text-slate-400">{{ config('ipnetp.institut.horaires') }}</p>
                </div>
            </aside>
        </div>
    </div>
</x-app-layout>
