@php $d = $devoir ?? null; @endphp

<div class="space-y-5">
    <div class="grid sm:grid-cols-3 gap-4">
        <div class="sm:col-span-2">
            <x-input-label for="titre" value="Intitulé du devoir" />
            <x-text-input id="titre" name="titre" type="text" class="mt-1 block w-full" :value="old('titre', $d?->titre)" required />
            <x-input-error :messages="$errors->get('titre')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="date_limite" value="À rendre avant le" />
            <x-text-input id="date_limite" name="date_limite" type="datetime-local" class="mt-1 block w-full"
                          :value="old('date_limite', $d?->date_limite?->format('Y-m-d\TH:i') ?? now()->addWeek()->setTime(18, 0)->format('Y-m-d\TH:i'))" required />
            <x-input-error :messages="$errors->get('date_limite')" class="mt-2" />
        </div>
    </div>

    <div>
        <x-input-label for="consignes" value="Consignes et barème" />
        <textarea id="consignes" name="consignes" rows="10" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('consignes', $d?->consignes) }}</textarea>
        <x-input-error :messages="$errors->get('consignes')" class="mt-2" />
        <p class="mt-1 text-xs text-gray-500">Les élèves répondent par écrit sur le site et peuvent joindre un lien (Google Drive, OneDrive) vers leur fichier.</p>
    </div>

    <label class="flex items-center gap-2 text-sm text-gray-700">
        <input type="hidden" name="publie" value="0">
        <input type="checkbox" name="publie" value="1" @checked(old('publie', $d?->publie ?? true)) class="rounded border-gray-300 text-institutionnel">
        Devoir visible par les élèves
    </label>
</div>
