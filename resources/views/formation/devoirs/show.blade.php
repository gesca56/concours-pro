<x-app-layout>
    <x-slot name="header">
        <a href="{{ route('formation.modules.show', $devoir->module) }}" class="text-xs font-semibold text-institutionnel hover:underline">← {{ $devoir->module->titre }}</a>
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $devoir->titre }}</h2>
        <p class="text-sm {{ $devoir->estEchu() ? 'text-red-600' : 'text-gray-500' }}">
            {{ __('À rendre avant le') }} {{ $devoir->date_limite->translatedFormat('l d F Y à H\hi') }}
            @unless ($devoir->estEchu()) ({{ $devoir->date_limite->diffForHumans() }}) @endunless
        </p>
    </x-slot>

    <div class="py-10">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <x-flash />

            <section class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-xs font-semibold uppercase tracking-wide text-gray-500 mb-2">{{ __('Consignes') }}</h3>
                <p class="text-sm text-gray-800 whitespace-pre-line">{{ $devoir->consignes }}</p>
            </section>

            @if ($rendu?->estCorrige())
                <section class="rounded-lg p-6 {{ $rendu->note >= 10 ? 'bg-emerald-50 border border-emerald-200' : 'bg-red-50 border border-red-200' }}">
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Note obtenue') }}</p>
                    <p class="text-3xl font-bold {{ $rendu->note >= 10 ? 'text-emerald-700' : 'text-red-700' }}">{{ number_format($rendu->note, 2, ',', ' ') }}<span class="text-lg">/20</span></p>
                    @if ($rendu->appreciation)
                        <p class="mt-2 text-sm text-gray-800 whitespace-pre-line"><strong>{{ __('Appréciation') }} :</strong> {{ $rendu->appreciation }}</p>
                    @endif
                    <p class="mt-2 text-xs text-gray-500">{{ __('Corrigé le') }} {{ $rendu->corrige_le->format('d/m/Y') }}</p>
                </section>

                <section class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-xs font-semibold uppercase tracking-wide text-gray-500 mb-2">{{ __('Votre copie') }}</h3>
                    <div class="text-sm text-gray-800 whitespace-pre-line">{{ $rendu->contenu }}</div>
                    @if ($rendu->lien)<a href="{{ $rendu->lien }}" target="_blank" rel="noopener noreferrer" class="inline-block mt-3 text-sm text-institutionnel underline">{{ __('Fichier joint') }} ↗</a>@endif
                </section>
            @else
                <form method="POST" action="{{ route('formation.devoirs.store', $devoir) }}" class="bg-white rounded-lg shadow-sm p-6 space-y-4">
                    @csrf
                    @if ($rendu)
                        <p class="text-sm p-3 rounded-md bg-amber-50 text-amber-900">
                            {{ __('Copie déposée le') }} {{ $rendu->rendu_le->format('d/m/Y à H\hi') }}@if ($rendu->en_retard) ({{ __('en retard') }})@endif.
                            {{ __('Vous pouvez encore la modifier tant qu\'elle n\'est pas corrigée.') }}
                        </p>
                    @elseif ($devoir->estEchu())
                        <p class="text-sm p-3 rounded-md bg-red-50 text-red-800">{{ __('La date limite est dépassée : votre copie sera acceptée mais signalée en retard.') }}</p>
                    @endif

                    <div>
                        <x-input-label for="contenu" value="Votre réponse" />
                        <textarea id="contenu" name="contenu" rows="14" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm">{{ old('contenu', $rendu?->contenu) }}</textarea>
                        <x-input-error :messages="$errors->get('contenu')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="lien" value="Lien vers un fichier (facultatif)" />
                        <x-text-input id="lien" name="lien" type="url" class="mt-1 block w-full" :value="old('lien', $rendu?->lien)" placeholder="https://drive.google.com/…" />
                        <x-input-error :messages="$errors->get('lien')" class="mt-2" />
                        <p class="mt-1 text-xs text-gray-500">{{ __('Partagez votre document (Word, PDF…) sur Google Drive ou OneDrive en « accès à toute personne disposant du lien ».') }}</p>
                    </div>
                    <div class="flex justify-end">
                        <x-primary-button>{{ $rendu ? __('Mettre à jour ma copie') : __('Déposer ma copie') }}</x-primary-button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</x-app-layout>
