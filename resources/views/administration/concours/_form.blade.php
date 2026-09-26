@php
    $c = $concours ?? null;
@endphp

<div class="grid grid-cols-2 gap-4">
    <div class="col-span-2">
        <x-input-label for="nom" value="Nom du concours" />
        <x-text-input id="nom" name="nom" type="text" class="mt-1 block w-full" :value="old('nom', $c?->nom)" required />
        <x-input-error :messages="$errors->get('nom')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="code" value="Code" />
        <x-text-input id="code" name="code" type="text" class="mt-1 block w-full" :value="old('code', $c?->code)" required />
        <x-input-error :messages="$errors->get('code')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="cycle" value="Cycle" />
        <select id="cycle" name="cycle" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
            @foreach (['CAP/PL', 'CAP/PC', 'CAP/IFPB', 'CAP/IAFPB'] as $cycle)
                <option value="{{ $cycle }}" @selected(old('cycle', $c?->cycle) === $cycle)>{{ $cycle }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('cycle')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="filiere" value="Filière" />
        <select id="filiere" name="filiere" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
            @foreach (['Tertiaire', 'Industriel', 'Agricole'] as $filiere)
                <option value="{{ $filiere }}" @selected(old('filiere', $c?->filiere) === $filiere)>{{ $filiere }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('filiere')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="diplome_requis" value="Diplôme requis" />
        <x-text-input id="diplome_requis" name="diplome_requis" type="text" class="mt-1 block w-full" :value="old('diplome_requis', $c?->diplome_requis)" required />
        <x-input-error :messages="$errors->get('diplome_requis')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="age_min" value="Âge minimum" />
        <x-text-input id="age_min" name="age_min" type="number" class="mt-1 block w-full" :value="old('age_min', $c?->age_min)" />
    </div>

    <div>
        <x-input-label for="age_max" value="Âge maximum" />
        <x-text-input id="age_max" name="age_max" type="number" class="mt-1 block w-full" :value="old('age_max', $c?->age_max)" />
        <x-input-error :messages="$errors->get('age_max')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="frais_inscription" value="Frais d'inscription (FCFA)" />
        <x-text-input id="frais_inscription" name="frais_inscription" type="number" step="0.01" class="mt-1 block w-full" :value="old('frais_inscription', $c?->frais_inscription ?? 25000)" required />
        <x-input-error :messages="$errors->get('frais_inscription')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="frais_visite_medicale" value="Frais de visite médicale (FCFA)" />
        <x-text-input id="frais_visite_medicale" name="frais_visite_medicale" type="number" step="0.01" class="mt-1 block w-full" :value="old('frais_visite_medicale', $c?->frais_visite_medicale ?? 10000)" required />
        <x-input-error :messages="$errors->get('frais_visite_medicale')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="date_ouverture" value="Date d'ouverture" />
        <x-text-input id="date_ouverture" name="date_ouverture" type="date" class="mt-1 block w-full" :value="old('date_ouverture', $c?->date_ouverture?->toDateString())" required />
        <x-input-error :messages="$errors->get('date_ouverture')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="date_cloture" value="Date de clôture" />
        <x-text-input id="date_cloture" name="date_cloture" type="date" class="mt-1 block w-full" :value="old('date_cloture', $c?->date_cloture?->toDateString())" required />
        <x-input-error :messages="$errors->get('date_cloture')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="date_concours" value="Date du concours (optionnel)" />
        <x-text-input id="date_concours" name="date_concours" type="date" class="mt-1 block w-full" :value="old('date_concours', $c?->date_concours?->toDateString())" />
        <x-input-error :messages="$errors->get('date_concours')" class="mt-2" />
    </div>

    <div class="col-span-2">
        <x-input-label for="description" value="Description (optionnel)" />
        <textarea id="description" name="description" rows="3"
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('description', $c?->description) }}</textarea>
    </div>
</div>
