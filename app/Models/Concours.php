<?php

namespace App\Models;

use App\Enums\StatutConcours;
use Illuminate\Database\Eloquent\Model;
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
            'statut' => StatutConcours::class,
        ];
    }

    /**
     * @return HasMany<Candidature, $this>
     */
    public function candidatures(): HasMany
    {
        return $this->hasMany(Candidature::class);
    }
}
