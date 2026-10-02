<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Créneau de l'emploi du temps d'une promotion.
 */
class Seance extends Model
{
    public const TYPES = [
        'cours' => 'Cours magistral',
        'td' => 'Travaux dirigés',
        'tp' => 'Travaux pratiques',
        'stage' => 'Stage de pratique',
        'evaluation' => 'Évaluation',
        'en_ligne' => 'Classe virtuelle',
    ];

    protected $fillable = [
        'promotion_id',
        'module_id',
        'debut',
        'fin',
        'type',
        'salle',
        'lien_visio',
        'observations',
    ];

    protected function casts(): array
    {
        return [
            'debut' => 'datetime',
            'fin' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Promotion, $this>
     */
    public function promotion(): BelongsTo
    {
        return $this->belongsTo(Promotion::class);
    }

    /**
     * @return BelongsTo<Module, $this>
     */
    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    public function libelleType(): string
    {
        return self::TYPES[$this->type] ?? ucfirst($this->type);
    }
}
