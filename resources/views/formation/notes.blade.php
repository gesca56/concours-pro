<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <a href="{{ route('formation.index') }}" class="text-xs font-semibold text-institutionnel hover:underline">← {{ __('E-learning') }}</a>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Mes notes') }}</h2>
                <p class="text-sm text-gray-500">{{ $promotion->nom }}</p>
            </div>
            <a href="{{ route('formation.notes.bulletin') }}" target="_blank" class="px-4 py-2 bg-institutionnel text-white text-sm font-semibold rounded-md hover:bg-institutionnel-hover">{{ __('Bulletin PDF') }}</a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <section class="bg-white rounded-lg shadow-sm p-6 flex flex-wrap items-center justify-between gap-4">
                <div>
                    <p class="text-xs uppercase tracking-wide text-gray-500">{{ __('Moyenne générale') }}</p>
                    <p class="text-4xl font-bold {{ $releve['moyenne'] === null ? 'text-gray-300' : ($releve['moyenne'] >= 10 ? 'text-emerald-700' : 'text-red-700') }}">
                        {{ $releve['moyenne'] !== null ? number_format($releve['moyenne'], 2, ',', ' ') : '—' }}<span class="text-xl">/20</span>
                    </p>
                </div>
                @if ($releve['mention'])
                    <p class="text-lg font-semibold text-marine">{{ __('Mention') }} : {{ $releve['mention'] }}</p>
                @endif
            </section>

            <div class="bg-white rounded-lg shadow-sm overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wide">
                        <tr>
                            <th class="text-left font-medium px-6 py-2">{{ __('Module') }}</th>
                            <th class="text-right font-medium px-3 py-2">{{ __('Coef.') }}</th>
                            <th class="text-right font-medium px-3 py-2">{{ __('Notes') }}</th>
                            <th class="text-right font-medium px-6 py-2">{{ __('Moyenne') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($releve['modules'] as $ligne)
                            <tr>
                                <td class="px-6 py-3">
                                    <a href="{{ route('formation.modules.show', $ligne['module']) }}" class="font-medium text-gray-900 hover:text-institutionnel">{{ $ligne['module']->titre }}</a>
                                </td>
                                <td class="px-3 py-3 text-right tabular-nums">{{ $ligne['module']->coefficient }}</td>
                                <td class="px-3 py-3 text-right tabular-nums text-gray-500">{{ $ligne['notes'] }}</td>
                                <td class="px-6 py-3 text-right tabular-nums font-semibold {{ $ligne['moyenne'] === null ? 'text-gray-300' : ($ligne['moyenne'] >= 10 ? 'text-emerald-700' : 'text-red-700') }}">
                                    {{ $ligne['moyenne'] !== null ? number_format($ligne['moyenne'], 2, ',', ' ') : '—' }}
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-6 py-6 text-gray-500">{{ __('Aucun module publié pour le moment.') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <p class="text-xs text-gray-500">
                {{ __('La moyenne d\'un module est celle de ses devoirs corrigés ; un devoir non rendu après la date limite compte 0. La moyenne générale est pondérée par les coefficients. Les quiz servent à l\'entraînement et ne sont pas comptés.') }}
            </p>
        </div>
    </div>
</x-app-layout>
