@php $l = $lecon ?? null; @endphp

<div class="space-y-5">
    <div class="grid sm:grid-cols-4 gap-4">
        <div class="sm:col-span-3">
            <x-input-label for="titre" value="Titre de la leçon" />
            <x-text-input id="titre" name="titre" type="text" class="mt-1 block w-full" :value="old('titre', $l?->titre)" required />
            <x-input-error :messages="$errors->get('titre')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="duree_minutes" value="Durée (min)" />
            <x-text-input id="duree_minutes" name="duree_minutes" type="number" min="1" class="mt-1 block w-full" :value="old('duree_minutes', $l?->duree_minutes)" />
            <x-input-error :messages="$errors->get('duree_minutes')" class="mt-2" />
        </div>
    </div>

    <div>
        <x-input-label for="resume" value="Résumé en une phrase (facultatif)" />
        <x-text-input id="resume" name="resume" type="text" class="mt-1 block w-full" :value="old('resume', $l?->resume)" />
        <x-input-error :messages="$errors->get('resume')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="contenu" value="Contenu du cours" />
        <textarea id="contenu" name="contenu" rows="18" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm font-mono text-sm">{{ old('contenu', $l?->contenu) }}</textarea>
        <x-input-error :messages="$errors->get('contenu')" class="mt-2" />
        <details class="mt-2 text-xs text-gray-500">
            <summary class="cursor-pointer text-institutionnel">Mise en forme (Markdown)</summary>
            <div class="mt-2 grid sm:grid-cols-2 gap-x-6 gap-y-1 font-mono">
                <span>## Titre de partie</span><span>### Sous-partie</span>
                <span>**texte en gras**</span><span>*texte en italique*</span>
                <span>- élément de liste</span><span>1. liste numérotée</span>
                <span>&gt; encadré / citation</span><span>[texte du lien](https://…)</span>
            </div>
            <p class="mt-2">Laissez une ligne vide entre deux paragraphes.</p>
        </details>
    </div>

    <div>
        <x-input-label for="video_url" value="Vidéo YouTube (facultatif)" />
        <x-text-input id="video_url" name="video_url" type="url" class="mt-1 block w-full" :value="old('video_url', $l?->video_url)" placeholder="https://www.youtube.com/watch?v=…" />
        <x-input-error :messages="$errors->get('video_url')" class="mt-2" />
    </div>

    <div class="grid sm:grid-cols-3 gap-4">
        <div class="sm:col-span-2">
            <x-input-label for="lien_ressource" value="Lien vers un support (PDF sur Google Drive…)" />
            <x-text-input id="lien_ressource" name="lien_ressource" type="url" class="mt-1 block w-full" :value="old('lien_ressource', $l?->lien_ressource)" placeholder="https://drive.google.com/…" />
            <x-input-error :messages="$errors->get('lien_ressource')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="libelle_ressource" value="Nom du support" />
            <x-text-input id="libelle_ressource" name="libelle_ressource" type="text" class="mt-1 block w-full" :value="old('libelle_ressource', $l?->libelle_ressource)" placeholder="Fiche de synthèse" />
        </div>
    </div>
    <p class="text-xs text-gray-500 -mt-3">Les fichiers ne sont pas stockés sur le serveur : déposez-les sur Google Drive ou OneDrive en « accès par lien », puis collez le lien ici.</p>

    <div class="flex flex-wrap items-center gap-6">
        <div class="w-32">
            <x-input-label for="ordre" value="Position" />
            <x-text-input id="ordre" name="ordre" type="number" min="0" class="mt-1 block w-full" :value="old('ordre', $l?->ordre)" placeholder="Auto" />
        </div>
        <label class="flex items-center gap-2 text-sm text-gray-700 mt-5">
            <input type="hidden" name="publiee" value="0">
            <input type="checkbox" name="publiee" value="1" @checked(old('publiee', $l?->publiee ?? true)) class="rounded border-gray-300 text-institutionnel">
            Leçon visible par les apprenants
        </label>
    </div>
</div>
