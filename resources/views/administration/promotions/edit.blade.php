<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Modifier la promotion') }}</h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <form method="POST" action="{{ route('administration.promotions.update', $promotion) }}" class="bg-white shadow-sm sm:rounded-lg p-6 space-y-6">
                @csrf
                @method('PUT')
                @include('administration.promotions._form')
                <div class="flex justify-end gap-3">
                    <a href="{{ route('administration.promotions.show', $promotion) }}" class="px-4 py-2 text-sm text-gray-600">{{ __('Annuler') }}</a>
                    <x-primary-button>{{ __('Enregistrer') }}</x-primary-button>
                </div>
            </form>

            <form method="POST" action="{{ route('administration.promotions.destroy', $promotion) }}" onsubmit="return confirm('Supprimer cette promotion ?')"
                  class="bg-white shadow-sm sm:rounded-lg p-6 flex items-center justify-between gap-4">
                @csrf
                @method('DELETE')
                <p class="text-sm text-gray-600">{{ __('Possible uniquement si la promotion n\'a plus aucun module.') }}</p>
                <x-danger-button>{{ __('Supprimer') }}</x-danger-button>
            </form>
        </div>
    </div>
</x-app-layout>
