<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Nouvelle promotion') }}</h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('administration.promotions.store') }}" class="bg-white shadow-sm sm:rounded-lg p-6 space-y-6">
                @csrf
                @include('administration.promotions._form')
                <div class="flex justify-end gap-3">
                    <a href="{{ route('administration.promotions.index') }}" class="px-4 py-2 text-sm text-gray-600">{{ __('Annuler') }}</a>
                    <x-primary-button>{{ __('Créer la promotion') }}</x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
