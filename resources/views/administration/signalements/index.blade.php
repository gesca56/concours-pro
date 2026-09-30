<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Paiements contestés') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-emerald-50 text-emerald-800 text-sm p-4 rounded-md">{{ session('status') }}</div>
            @endif

            <a href="{{ route('administration.dashboard') }}" class="text-sm text-gray-500 hover:text-gray-700">← {{ __('Retour au tableau de bord') }}</a>

            <section>
                <h3 class="font-medium text-gray-900 mb-3">{{ __('À traiter') }} ({{ $enAttente->count() }})</h3>
                <div class="space-y-4">
                    @forelse ($enAttente as $signalement)
                        @php $paiement = $signalement->paiement; @endphp
                        <article class="bg-white shadow-sm sm:rounded-lg p-6">
                            <div class="flex flex-wrap items-start justify-between gap-3">
                                <div>
                                    <p class="font-medium text-gray-900">{{ $signalement->candidat->name }}</p>
                                    <p class="text-xs text-gray-500">
                                        {{ __('Dossier n°') }} {{ str_pad($paiement->candidature_id, 5, '0', STR_PAD_LEFT) }} · {{ $paiement->candidature->concours->nom }}
                                    </p>
                                </div>
                                <span class="text-xs text-gray-500">{{ $signalement->created_at->diffForHumans() }}</span>
                            </div>

                            <dl class="mt-4 grid grid-cols-2 sm:grid-cols-4 gap-3 text-sm bg-gray-50 rounded-md p-3">
                                <div><dt class="text-xs text-gray-500">{{ __('Objet') }}</dt><dd>{{ $paiement->type === 'visite_medicale' ? __('Visite médicale') : __('Inscription') }}</dd></div>
                                <div><dt class="text-xs text-gray-500">{{ __('Montant') }}</dt><dd>{{ number_format($paiement->montant, 0, ',', ' ') }} FCFA</dd></div>
                                <div><dt class="text-xs text-gray-500">{{ __('Référence') }}</dt><dd class="font-mono text-xs break-all">{{ $paiement->reference_transaction }}</dd></div>
                                <div><dt class="text-xs text-gray-500">{{ __('Date') }}</dt><dd>{{ $paiement->date_paiement?->format('d/m/Y H:i') }}</dd></div>
                            </dl>

                            <blockquote class="mt-4 text-sm text-gray-700 border-l-4 border-amber-300 pl-3">{{ $signalement->message }}</blockquote>

                            <form method="POST" action="{{ route('administration.signalements.update', $signalement) }}" class="mt-4 space-y-2">
                                @csrf
                                @method('PATCH')
                                <textarea name="reponse_administration" rows="3" required
                                          placeholder="{{ __('Ex. : Vérification faite auprès de l\'opérateur, le paiement est bien encaissé. Aucun second prélèvement n\'a été constaté.') }}"
                                          class="block w-full text-sm border-gray-300 rounded-md shadow-sm"></textarea>
                                <x-input-error :messages="$errors->get('reponse_administration')" />
                                <x-primary-button>{{ __('Répondre et clore') }}</x-primary-button>
                            </form>
                        </article>
                    @empty
                        <p class="bg-white shadow-sm sm:rounded-lg p-6 text-sm text-gray-500">{{ __('Aucun paiement contesté en attente.') }}</p>
                    @endforelse
                </div>
            </section>

            @if ($traites->isNotEmpty())
                <section>
                    <h3 class="font-medium text-gray-900 mb-3">{{ __('Derniers signalements traités') }}</h3>
                    <div class="bg-white shadow-sm sm:rounded-lg divide-y divide-gray-100">
                        @foreach ($traites as $signalement)
                            <div class="p-4 text-sm">
                                <p class="text-gray-900">{{ $signalement->candidat->name }} <span class="text-gray-400">· {{ $signalement->updated_at->format('d/m/Y') }}</span></p>
                                <p class="text-gray-500 mt-1">{{ Str::limit($signalement->reponse_administration, 140) }}</p>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif
        </div>
    </div>
</x-app-layout>
