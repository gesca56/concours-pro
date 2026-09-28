<?php

namespace App\Rules;

use App\Models\Concours;
use App\Models\User;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

/**
 * Valide qu'un candidat respecte les critères d'éligibilité stricts d'un
 * concours : tranche d'âge autorisée et adéquation du diplôme requis.
 *
 * Appliquée au champ "diplome_candidat" du formulaire de candidature.
 */
class EligibiliteConcours implements ValidationRule
{
    public function __construct(
        private readonly Concours $concours,
        private readonly User $candidat,
    ) {}

    /**
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($this->candidat->date_naissance === null) {
            $fail('Renseignez votre date de naissance dans votre profil avant de postuler.');

            return;
        }

        $age = $this->candidat->date_naissance->age;

        if ($this->concours->age_min !== null && $age < $this->concours->age_min) {
            $fail("L'âge minimum requis pour ce concours est de {$this->concours->age_min} ans.");
        }

        if ($this->concours->age_max !== null && $age > $this->concours->age_max) {
            $fail("L'âge maximum autorisé pour ce concours est de {$this->concours->age_max} ans.");
        }

        if (mb_strtolower(trim((string) $value)) !== mb_strtolower(trim($this->concours->diplome_requis))) {
            $fail("Le diplôme requis pour ce concours est : {$this->concours->diplome_requis}.");
        }
    }
}
