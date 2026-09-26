<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Vérification de convocation') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <p class="text-sm text-gray-500 mb-4">
                    {{ __('Scannez le QR Code de la convocation ou saisissez manuellement le jeton pour vérifier l\'identité du candidat.') }}
                </p>
                <form method="GET" action="{{ route('verification-qr') }}" class="flex gap-3">
                    <x-text-input name="jeton" type="text" class="flex-1" placeholder="{{ __('Jeton de convocation') }}"
                                  :value="request('jeton')" autofocus />
                    <x-primary-button>{{ __('Vérifier') }}</x-primary-button>
                </form>
            </div>

            @if ($recherche)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    @if ($candidature)
                        <div class="flex items-center gap-3 mb-4">
                            <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                            <span class="font-semibold text-emerald-700">{{ __('Identité vérifiée') }}</span>
                        </div>
                        <dl class="grid grid-cols-2 gap-4 text-sm">
                            <div><dt class="text-gray-500">{{ __('Candidat') }}</dt><dd class="font-medium text-gray-900">{{ $candidature->candidat->name }}</dd></div>
                            <div><dt class="text-gray-500">{{ __('Concours') }}</dt><dd class="text-gray-900">{{ $candidature->concours->nom }}</dd></div>
                            <div><dt class="text-gray-500">{{ __('Numéro de dossier') }}</dt><dd class="text-gray-900">{{ $candidature->id }}</dd></div>
                            <div><dt class="text-gray-500">{{ __('Statut') }}</dt><dd><x-statut-badge :statut="$candidature->statut" /></dd></div>
                        </dl>
                    @else
                        <div class="flex items-center gap-3">
                            <span class="w-3 h-3 rounded-full bg-red-500"></span>
                            <span class="font-semibold text-red-700">{{ __('Jeton invalide — aucune convocation trouvée.') }}</span>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
