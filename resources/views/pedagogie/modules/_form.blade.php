@php
    $m = $module ?? null;
    $estAdmin = auth()->user()->role === \App\Enums\Role::Administration;
@endphp

<div x-data="{ public: '{{ old('public', $m?->public ?? 'preparation') }}' }" class="space-y-5">
    <div>
        <x-input-label value="Public visé" />
        <div class="mt-2 grid sm:grid-cols-2 gap-3">
            <label class="flex gap-3 p-4 rounded-lg border cursor-pointer" :class="public === 'preparation' ? 'border-institutionnel bg-institutionnel/5' : 'border-gray-200'">
                <input type="radio" name="public" value="preparation" x-model="public" class="mt-1 text-institutionnel">
                <span>
                    <span class="block font-medium text-gray-900">Préparation au concours</span>
                    <span class="block text-xs text-gray-500">Ouvert à tous les candidats inscrits sur la plateforme.</span>
                </span>
            </label>
            <label class="flex gap-3 p-4 rounded-lg border cursor-pointer" :class="public === 'formation' ? 'border-institutionnel bg-institutionnel/5' : 'border-gray-200'">
                <input type="radio" name="public" value="formation" x-model="public" class="mt-1 text-institutionnel">
                <span>
                    <span class="block font-medium text-gray-900">Formation des élèves-professeurs</span>
                    <span class="block text-xs text-gray-500">Réservé aux admis d'une promotion ; devoirs notés et bulletin.</span>
                </span>
            </label>
        </div>
        <x-input-error :messages="$errors->get('public')" class="mt-2" />
    </div>

    <div class="grid sm:grid-cols-3 gap-4">
        <div class="sm:col-span-2">
            <x-input-label for="titre" value="Intitulé du module" />
            <x-text-input id="titre" name="titre" type="text" class="mt-1 block w-full" :value="old('titre', $m?->titre)" required placeholder="Ex. Didactique de l'informatique" />
            <x-input-error :messages="$errors->get('titre')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="code" value="Code (facultatif)" />
            <x-text-input id="code" name="code" type="text" class="mt-1 block w-full" :value="old('code', $m?->code)" placeholder="Ex. DID-101" />
            <x-input-error :messages="$errors->get('code')" class="mt-2" />
        </div>
    </div>

    <div x-show="public === 'preparation'" x-cloak>
        <x-input-label for="cycle" value="Concours concerné" />
        <select id="cycle" name="cycle" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            <option value="">Tous les concours</option>
            @foreach (config('ipnetp.cycles') as $code => $cycle)
                <option value="{{ $code }}" @selected(old('cycle', $m?->cycle) === $code)>{{ $code }} — {{ $cycle['intitule'] }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('cycle')" class="mt-2" />
    </div>

    <div x-show="public === 'formation'" x-cloak class="grid sm:grid-cols-3 gap-4">
        <div>
            <x-input-label for="promotion_id" value="Promotion" />
            <select id="promotion_id" name="promotion_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                <option value="">— Choisir —</option>
                @foreach ($promotions as $promotion)
                    <option value="{{ $promotion->id }}" @selected((int) old('promotion_id', $m?->promotion_id) === $promotion->id)>{{ $promotion->nom }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('promotion_id')" class="mt-2" />
            @if ($promotions->isEmpty())
                <p class="mt-1 text-xs text-amber-700">Aucune promotion en cours : l'administration doit d'abord en créer une.</p>
            @endif
        </div>
        <div>
            <x-input-label for="coefficient" value="Coefficient" />
            <x-text-input id="coefficient" name="coefficient" type="number" min="1" max="10" class="mt-1 block w-full" :value="old('coefficient', $m?->coefficient ?? 1)" />
            <x-input-error :messages="$errors->get('coefficient')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="volume_horaire" value="Volume horaire (h)" />
            <x-text-input id="volume_horaire" name="volume_horaire" type="number" min="1" class="mt-1 block w-full" :value="old('volume_horaire', $m?->volume_horaire)" />
            <x-input-error :messages="$errors->get('volume_horaire')" class="mt-2" />
        </div>
    </div>

    @if ($estAdmin)
        <div>
            <x-input-label for="enseignant_id" value="Enseignant responsable" />
            <select id="enseignant_id" name="enseignant_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                <option value="">— Aucun pour l'instant —</option>
                @foreach ($enseignants as $enseignant)
                    <option value="{{ $enseignant->id }}" @selected((int) old('enseignant_id', $m?->enseignant_id) === $enseignant->id)>{{ $enseignant->name }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('enseignant_id')" class="mt-2" />
        </div>
    @endif

    <div>
        <x-input-label for="description" value="Présentation" />
        <textarea id="description" name="description" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" placeholder="À quoi sert ce module ?">{{ old('description', $m?->description) }}</textarea>
        <x-input-error :messages="$errors->get('description')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="objectifs" value="Objectifs pédagogiques (un par ligne)" />
        <textarea id="objectifs" name="objectifs" rows="4" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" placeholder="Être capable de…">{{ old('objectifs', $m?->objectifs) }}</textarea>
        <x-input-error :messages="$errors->get('objectifs')" class="mt-2" />
    </div>

    <label class="flex items-center gap-2 text-sm text-gray-700">
        <input type="hidden" name="publie" value="0">
        <input type="checkbox" name="publie" value="1" @checked(old('publie', $m?->publie)) class="rounded border-gray-300 text-institutionnel">
        Publier le module (visible par les apprenants)
    </label>
</div>
