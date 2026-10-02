@php $p = $promotion ?? null; @endphp

<div class="grid sm:grid-cols-2 gap-4">
    <div class="sm:col-span-2">
        <x-input-label for="nom" value="Nom de la promotion" />
        <x-text-input id="nom" name="nom" type="text" class="mt-1 block w-full" :value="old('nom', $p?->nom)" required placeholder="Ex. CAP/PL Informatique de gestion — Promotion 2026-2027" />
        <x-input-error :messages="$errors->get('nom')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="code" value="Code (préfixe des matricules)" />
        <x-text-input id="code" name="code" type="text" class="mt-1 block w-full uppercase" :value="old('code', $p?->code)" required placeholder="PL26-INFO" />
        <x-input-error :messages="$errors->get('code')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="annee_academique" value="Année académique" />
        <x-text-input id="annee_academique" name="annee_academique" type="text" class="mt-1 block w-full" :value="old('annee_academique', $p?->annee_academique ?? now()->year.'-'.(now()->year + 1))" required />
        <x-input-error :messages="$errors->get('annee_academique')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="cycle" value="Cycle de formation" />
        <select id="cycle" name="cycle" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
            @foreach (config('ipnetp.cycles') as $code => $cycle)
                <option value="{{ $code }}" @selected(old('cycle', $p?->cycle) === $code)>{{ $code }} — {{ $cycle['intitule'] }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('cycle')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="specialite" value="Spécialité" />
        <x-text-input id="specialite" name="specialite" type="text" class="mt-1 block w-full" :value="old('specialite', $p?->specialite)" />
        <x-input-error :messages="$errors->get('specialite')" class="mt-2" />
    </div>

    <div class="sm:col-span-2">
        <x-input-label for="concours_id" value="Concours d'origine (facultatif)" />
        <select id="concours_id" name="concours_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            <option value="">— Aucun —</option>
            @foreach ($concours as $c)
                <option value="{{ $c->id }}" @selected((int) old('concours_id', $p?->concours_id) === $c->id)>{{ $c->nom }} ({{ $c->admis_count }} admis)</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('concours_id')" class="mt-2" />
        @unless ($p)
            <label class="mt-2 flex items-center gap-2 text-sm text-gray-700">
                <input type="checkbox" name="inscrire_admis" value="1" @checked(old('inscrire_admis', true)) class="rounded border-gray-300 text-institutionnel">
                Inscrire automatiquement les admis de ce concours
            </label>
        @endunless
    </div>

    <div>
        <x-input-label for="date_debut" value="Début de la formation" />
        <x-text-input id="date_debut" name="date_debut" type="date" class="mt-1 block w-full" :value="old('date_debut', $p?->date_debut?->toDateString())" />
        <x-input-error :messages="$errors->get('date_debut')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="date_fin" value="Fin de la formation" />
        <x-text-input id="date_fin" name="date_fin" type="date" class="mt-1 block w-full" :value="old('date_fin', $p?->date_fin?->toDateString())" />
        <x-input-error :messages="$errors->get('date_fin')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="statut" value="Statut" />
        <select id="statut" name="statut" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            <option value="en_cours" @selected(old('statut', $p?->statut ?? 'en_cours') === 'en_cours')>En cours</option>
            <option value="terminee" @selected(old('statut', $p?->statut) === 'terminee')>Terminée</option>
        </select>
    </div>
</div>
