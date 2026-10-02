@php
    $q = $quiz ?? null;
    $questionsInitiales = old('questions', $q?->questions->map(fn ($question) => [
        'enonce' => $question->enonce,
        'choix' => $question->choix,
        'bonne_reponse' => $question->bonne_reponse,
        'explication' => $question->explication,
    ])->all() ?: [['enonce' => '', 'choix' => ['', ''], 'bonne_reponse' => 0, 'explication' => '']]);
@endphp

<div class="space-y-5">
    <div class="grid sm:grid-cols-4 gap-4">
        <div class="sm:col-span-2">
            <x-input-label for="titre" value="Titre du quiz" />
            <x-text-input id="titre" name="titre" type="text" class="mt-1 block w-full" :value="old('titre', $q?->titre)" required />
            <x-input-error :messages="$errors->get('titre')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="duree_minutes" value="Durée conseillée (min)" />
            <x-text-input id="duree_minutes" name="duree_minutes" type="number" min="1" class="mt-1 block w-full" :value="old('duree_minutes', $q?->duree_minutes)" />
        </div>
        <div>
            <x-input-label for="tentatives_max" value="Tentatives max." />
            <x-text-input id="tentatives_max" name="tentatives_max" type="number" min="1" class="mt-1 block w-full" :value="old('tentatives_max', $q?->tentatives_max)" placeholder="Illimitées" />
        </div>
    </div>

    <div>
        <x-input-label for="consignes" value="Consignes (facultatif)" />
        <textarea id="consignes" name="consignes" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('consignes', $q?->consignes) }}</textarea>
    </div>

    @if ($q?->tentatives()->exists())
        <p class="text-xs p-3 rounded-md bg-amber-50 text-amber-800">Des apprenants ont déjà répondu à ce quiz : leurs notes sont conservées, mais le détail de leurs anciennes copies ne sera plus affiché si vous modifiez les questions.</p>
    @endif

    @if ($errors->has('questions*'))
        <div class="text-sm p-3 rounded-md bg-red-50 text-red-700 space-y-1">
            @foreach ($errors->get('questions*') as $messages)
                @foreach ((array) $messages as $message)<p>{{ $message }}</p>@endforeach
            @endforeach
        </div>
    @endif

    <div x-data="{ questions: @js(array_values($questionsInitiales)) }" class="space-y-4">
        <template x-for="(question, qi) in questions" :key="qi">
            <fieldset class="border border-gray-200 rounded-lg p-4 space-y-3">
                <div class="flex items-center justify-between">
                    <legend class="text-sm font-semibold text-marine" x-text="'Question ' + (qi + 1)"></legend>
                    <button type="button" x-show="questions.length > 1" @click="questions.splice(qi, 1)" class="text-xs text-red-600 hover:underline">Retirer</button>
                </div>
                <textarea :name="`questions[${qi}][enonce]`" x-model="question.enonce" rows="2" required
                          class="block w-full border-gray-300 rounded-md shadow-sm text-sm" placeholder="Énoncé de la question"></textarea>

                <div class="space-y-2">
                    <p class="text-xs text-gray-500">Réponses proposées — cochez la bonne :</p>
                    <template x-for="(choix, ci) in question.choix" :key="ci">
                        <div class="flex items-center gap-2">
                            <input type="radio" :name="`questions[${qi}][bonne_reponse]`" :value="ci"
                                   :checked="Number(question.bonne_reponse) === ci" @change="question.bonne_reponse = ci"
                                   class="text-emerald-600" title="Bonne réponse">
                            <input type="text" :name="`questions[${qi}][choix][${ci}]`" x-model="question.choix[ci]" required
                                   class="flex-1 border-gray-300 rounded-md shadow-sm text-sm" :placeholder="'Réponse ' + String.fromCharCode(65 + ci)">
                            <button type="button" x-show="question.choix.length > 2"
                                    @click="question.choix.splice(ci, 1); if (question.bonne_reponse >= question.choix.length) question.bonne_reponse = 0"
                                    class="text-gray-400 hover:text-red-600 text-sm">✕</button>
                        </div>
                    </template>
                    <button type="button" x-show="question.choix.length < 6" @click="question.choix.push('')" class="text-xs text-institutionnel hover:underline">+ Ajouter une réponse</button>
                </div>

                <input type="text" :name="`questions[${qi}][explication]`" x-model="question.explication"
                       class="block w-full border-gray-300 rounded-md shadow-sm text-sm" placeholder="Explication affichée après la correction (facultatif)">
            </fieldset>
        </template>

        <button type="button" @click="questions.push({ enonce: '', choix: ['', ''], bonne_reponse: 0, explication: '' })"
                class="w-full py-3 border-2 border-dashed border-gray-300 rounded-lg text-sm text-gray-600 hover:border-institutionnel hover:text-institutionnel">
            + Ajouter une question
        </button>
    </div>

    <label class="flex items-center gap-2 text-sm text-gray-700">
        <input type="hidden" name="publie" value="0">
        <input type="checkbox" name="publie" value="1" @checked(old('publie', $q?->publie)) class="rounded border-gray-300 text-institutionnel">
        Publier le quiz
    </label>
</div>
