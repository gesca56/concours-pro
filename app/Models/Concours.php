<?php

namespace App\Models;

use App\Enums\StatutConcours;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Concours extends Model
{
    protected $table = 'concours';

    protected $fillable = [
        'nom',
        'code',
        'cycle',
        'filiere',
        'description',
        'age_min',
        'age_max',
        'diplome_requis',
        'frais_inscription',
        'frais_visite_medicale',
        'seuil_admission',
        'date_ouverture',
        'date_cloture',
        'date_concours',
        'statut',
    ];

    protected function casts(): array
    {
        return [
            'date_ouverture' => 'date',
            'date_cloture' => 'date',
            'date_concours' => 'date',
            'frais_inscription' => 'decimal:2',
            'frais_visite_medicale' => 'decimal:2',
            'seuil_admission' => 'decimal:2',
            'statut' => StatutConcours::class,
        ];
    }

    /**
     * Diplômes admis, le champ pouvant en lister plusieurs séparés par
     * une virgule, un point-virgule ou « ou » (ex. « Licence professionnelle, BTS, DUT »).
     *
     * @return list<string>
     */
    public function diplomesAdmis(): array
    {
        return array_values(array_filter(array_map(
            'trim',
            preg_split('/\s*[,;]\s*|\s+ou\s+/iu', (string) $this->diplome_requis)
        )));
    }

    /**
     * Date à laquelle s'apprécie l'âge des candidats : le 1er janvier de
     * l'année du concours, comme dans les communiqués de l'IPNETP.
     */
    public function dateReferenceAge(): Carbon
    {
        $annee = ($this->date_concours ?? $this->date_ouverture ?? now())->year;

        return Carbon::create($annee, 1, 1);
    }

    /**
     * @return HasMany<Candidature, $this>
     */
    public function candidatures(): HasMany
    {
        return $this->hasMany(Candidature::class);
    }
}
