<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Concours') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="flex justify-end">
                <a href="{{ route('administration.concours.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-institutionnel text-white rounded-md font-semibold text-xs uppercase tracking-widest hover:bg-institutionnel-hover">
                    {{ __('Nouveau concours') }}
                </a>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg divide-y divide-gray-100">
                @forelse ($concours as $c)
                    <a href="{{ route('administration.concours.show', $c) }}" class="block p-6 hover:bg-gray-50">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-medium text-gray-900">{{ $c->nom }}</p>
                                <p class="text-sm text-gray-500">{{ $c->cycle }} · {{ $c->filiere }} — {{ $c->candidatures_count }} {{ __('candidature(s)') }}</p>
                            </div>
                            <x-statut-badge :statut="$c->statut->value" />
                        </div>
                    </a>
                @empty
                    <p class="p-6 text-gray-500">{{ __('Aucun concours créé.') }}</p>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
