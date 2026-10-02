<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Promotions d\'élèves-professeurs') }}</h2>
                <p class="text-sm text-gray-500">{{ __('Les admis aux concours suivent leur formation au sein d\'une promotion') }}</p>
            </div>
            <a href="{{ route('administration.promotions.create') }}" class="px-4 py-2 bg-institutionnel text-white text-sm font-semibold rounded-md hover:bg-institutionnel-hover">+ {{ __('Nouvelle promotion') }}</a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <x-flash />

            <div class="bg-white rounded-lg shadow-sm overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wide">
                        <tr>
                            <th class="text-left font-medium px-6 py-2">{{ __('Promotion') }}</th>
                            <th class="text-left font-medium px-3 py-2">{{ __('Année') }}</th>
                            <th class="text-right font-medium px-3 py-2">{{ __('Élèves') }}</th>
                            <th class="text-right font-medium px-3 py-2">{{ __('Modules') }}</th>
                            <th class="text-right font-medium px-6 py-2">{{ __('Statut') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($promotions as $promotion)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-3">
                                    <a href="{{ route('administration.promotions.show', $promotion) }}" class="font-medium text-gray-900 hover:text-institutionnel">{{ $promotion->nom }}</a>
                                    <p class="text-xs text-gray-500"><span class="font-mono">{{ $promotion->code }}</span> · {{ $promotion->cycle }}@if ($promotion->concours) · {{ __('issue de') }} {{ $promotion->concours->nom }}@endif</p>
                                </td>
                                <td class="px-3 py-3 text-gray-600">{{ $promotion->annee_academique }}</td>
                                <td class="px-3 py-3 text-right tabular-nums">{{ $promotion->eleves_count }}</td>
                                <td class="px-3 py-3 text-right tabular-nums">{{ $promotion->modules_count }}</td>
                                <td class="px-6 py-3 text-right">
                                    <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $promotion->estEnCours() ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-600' }}">
                                        {{ $promotion->estEnCours() ? __('En cours') : __('Terminée') }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-6 py-8 text-gray-500">{{ __('Aucune promotion. Créez-en une après la délibération d\'un concours pour y inscrire les admis.') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
