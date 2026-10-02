<x-app-layout>
    <x-slot name="header">
        <p class="text-xs font-semibold text-institutionnel">{{ $module->titre }}</p>
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Modifier le quiz') }}</h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <form method="POST" action="{{ route('pedagogie.quiz.update', $quiz) }}" class="bg-white shadow-sm sm:rounded-lg p-6 space-y-6">
                @csrf
                @method('PUT')
                @include('pedagogie.quiz._form')
                <div class="flex justify-end gap-3">
                    <a href="{{ route('pedagogie.quiz.show', $quiz) }}" class="px-4 py-2 text-sm text-gray-600">{{ __('Annuler') }}</a>
                    <x-primary-button>{{ __('Enregistrer') }}</x-primary-button>
                </div>
            </form>

            <form method="POST" action="{{ route('pedagogie.quiz.destroy', $quiz) }}" onsubmit="return confirm('Supprimer ce quiz et toutes les tentatives ?')"
                  class="bg-white shadow-sm sm:rounded-lg p-6 flex items-center justify-between gap-4">
                @csrf
                @method('DELETE')
                <p class="text-sm text-gray-600">{{ __('La suppression efface aussi les résultats des apprenants.') }}</p>
                <x-danger-button>{{ __('Supprimer') }}</x-danger-button>
            </form>
        </div>
    </div>
</x-app-layout>
