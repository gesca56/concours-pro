<?php

namespace App\Rules;

use App\Models\Concours;
use App\Models\User;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

/**
 * Valide qu'un candidat respecte les critères d'éligibilité stricts d'un
 * concours : tranche d'âge autorisée (appréciée au 1er janvier de l'année du
 * concours) et adéquation avec l'un des diplômes admis.
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

        // L'âge s'apprécie au 1er janvier de l'année du concours (règle IPNETP).
        $reference = $this->concours->dateReferenceAge();
        $age = (int) $this->candidat->date_naissance->diffInYears($reference);
        $auDate = 'au '.$reference->translatedFormat('j F Y');

        if ($this->concours->age_min !== null && $age < $this->concours->age_min) {
            $fail("L'âge minimum requis pour ce concours est de {$this->concours->age_min} ans {$auDate}.");
        }

        if ($this->concours->age_max !== null && $age > $this->concours->age_max) {
            $fail("L'âge maximum autorisé pour ce concours est de {$this->concours->age_max} ans {$auDate}.");
        }

        $diplomesAdmis = array_map('mb_strtolower', $this->concours->diplomesAdmis());

        if (! in_array(mb_strtolower(trim((string) $value)), $diplomesAdmis, true)) {
            $fail("Le diplôme requis pour ce concours est : {$this->concours->diplome_requis}.");
        }
    }
}
