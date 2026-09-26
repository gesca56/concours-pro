<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Paiement extends Model
{
    protected $fillable = [
        'candidature_id',
        'type',
        'montant',
        'mode_paiement',
        'reference_transaction',
        'statut',
        'date_paiement',
    ];

    protected function casts(): array
    {
        return [
            'montant' => 'decimal:2',
            'date_paiement' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Candidature, $this>
     */
    public function candidature(): BelongsTo
    {
        return $this->belongsTo(Candidature::class);
    }

    /**
     * @return HasMany<SignalementPaiement, $this>
     */
    public function signalements(): HasMany
    {
        return $this->hasMany(SignalementPaiement::class);
    }
}
