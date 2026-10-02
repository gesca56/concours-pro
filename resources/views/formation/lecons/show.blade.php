<x-app-layout>
    <x-slot name="header">
        <a href="{{ route('formation.modules.show', $module) }}" class="text-xs font-semibold text-institutionnel hover:underline">← {{ $module->titre }}</a>
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $lecon->titre }}</h2>
        <p class="text-sm text-gray-500">
            {{ __('Leçon') }} {{ $lecons->search(fn ($l) => $l->id === $lecon->id) + 1 }}/{{ $lecons->count() }}
            @if ($lecon->duree_minutes) · {{ $lecon->duree_minutes }} min @endif
        </p>
    </x-slot>

    <div class="py-10">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-4 gap-6">
            {{-- Sommaire du module --}}
            <nav class="hidden lg:block">
                <ol class="bg-white rounded-lg shadow-sm p-4 space-y-1 text-sm sticky top-4">
                    @foreach ($lecons as $l)
                        <li>
                            <a href="{{ route('formation.lecons.show', $l) }}"
                               class="flex items-start gap-2 px-2 py-1.5 rounded-md {{ $l->id === $lecon->id ? 'bg-institutionnel/10 text-institutionnel font-medium' : 'text-gray-600 hover:bg-gray-50' }}">
                                <span class="w-4 shrink-0 {{ $leconsTerminees->contains($l->id) ? 'text-emerald-600' : 'text-gray-400' }}">{{ $leconsTerminees->contains($l->id) ? '✓' : $loop->iteration }}</span>
                                <span>{{ $l->titre }}</span>
                            </a>
                        </li>
                    @endforeach
                </ol>
            </nav>

            <article class="lg:col-span-3 space-y-6">
                <x-flash />

                @if ($lecon->videoEmbed())
                    <div class="aspect-video rounded-lg overflow-hidden bg-black shadow-sm">
                        <iframe src="{{ $lecon->videoEmbed() }}" title="{{ $lecon->titre }}" class="w-full h-full" loading="lazy"
                                allow="accelerometer; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                    </div>
                @endif

                <div class="bg-white rounded-lg shadow-sm p-6 sm:p-8">
                    @if ($lecon->resume)
                        <p class="text-gray-500 italic mb-6">{{ $lecon->resume }}</p>
                    @endif
                    <div class="contenu-cours">{!! $lecon->contenuHtml() !!}</div>

                    @if ($lecon->lien_ressource)
                        <a href="{{ $lecon->lien_ressource }}" target="_blank" rel="noopener noreferrer"
                           class="mt-8 flex items-center gap-3 p-4 rounded-lg border border-gray-200 hover:border-institutionnel text-sm">
                            <span class="w-10 h-10 rounded-md bg-institutionnel/10 text-institutionnel flex items-center justify-center font-bold">↓</span>
                            <span>
                                <span class="block font-medium text-gray-900">{{ $lecon->libelle_ressource ?: __('Support de cours') }}</span>
                                <span class="block text-xs text-gray-500">{{ __('S\'ouvre dans un nouvel onglet') }}</span>
                            </span>
                        </a>
                    @endif
                </div>

                <div class="flex flex-wrap items-center justify-between gap-3">
                    @if ($precedente)
                        <a href="{{ route('formation.lecons.show', $precedente) }}" class="text-sm text-gray-600 hover:text-institutionnel">← {{ $precedente->titre }}</a>
                    @else
                        <span></span>
                    @endif

                    <form method="POST" action="{{ route('formation.lecons.terminer', $lecon) }}">
                        @csrf
                        <button class="px-5 py-2.5 rounded-md text-sm font-semibold {{ $terminee ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-institutionnel text-white hover:bg-institutionnel-hover' }}">
                            @if ($terminee)
                                ✓ {{ __('Terminée') }}{{ $suivante ? ' — '.__('leçon suivante').' →' : '' }}
                            @else
                                {{ $suivante ? __('J\'ai terminé, leçon suivante') : __('J\'ai terminé ce module') }} →
                            @endif
                        </button>
                    </form>
                </div>
            </article>
        </div>
    </div>
</x-app-layout>
