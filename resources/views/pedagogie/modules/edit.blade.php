<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Modifier le module') }}</h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <form method="POST" action="{{ route('pedagogie.modules.update', $module) }}" class="bg-white shadow-sm sm:rounded-lg p-6 space-y-6">
                @csrf
                @method('PUT')
                @include('pedagogie.modules._form')
                <div class="flex justify-end gap-3">
                    <a href="{{ route('pedagogie.modules.show', $module) }}" class="px-4 py-2 text-sm text-gray-600">{{ __('Annuler') }}</a>
                    <x-primary-button>{{ __('Enregistrer') }}</x-primary-button>
                </div>
            </form>

            <form method="POST" action="{{ route('pedagogie.modules.destroy', $module) }}"
                  onsubmit="return confirm('Supprimer définitivement ce module, ses leçons, quiz et devoirs ?')"
                  class="bg-white shadow-sm sm:rounded-lg p-6 flex items-center justify-between gap-4">
                @csrf
                @method('DELETE')
                <p class="text-sm text-gray-600">{{ __('La suppression efface aussi les leçons, quiz, devoirs et copies du module.') }}</p>
                <x-danger-button>{{ __('Supprimer') }}</x-danger-button>
            </form>
        </div>
    </div>
</x-app-layout>
