<x-app-layout>
    <x-slot name="header">
        <p class="text-xs font-semibold text-institutionnel">{{ $module->titre }}</p>
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Nouveau devoir') }}</h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('pedagogie.devoirs.store', $module) }}" class="bg-white shadow-sm sm:rounded-lg p-6 space-y-6">
                @csrf
                @include('pedagogie.devoirs._form')
                <div class="flex justify-end gap-3">
                    <a href="{{ route('pedagogie.modules.show', $module) }}" class="px-4 py-2 text-sm text-gray-600">{{ __('Annuler') }}</a>
                    <x-primary-button>{{ __('Publier le devoir') }}</x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
