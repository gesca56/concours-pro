<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Candidature extends Model
{
    protected $fillable = [
        'user_id',
        'concours_id',
        'diplome_candidat',
        'numero_anonymat',
        'jeton_convocation',
        'visite_medicale_programmee_le',
        'aptitude_medicale',
        'motif_inaptitude',
        'statut',
        'note_totale',
        'date_soumission',
    ];

    protected function casts(): array
    {
        return [
            'note_totale' => 'decimal:2',
            'date_soumission' => 'datetime',
            'visite_medicale_programmee_le' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function candidat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return BelongsTo<Concours, $this>
     */
    public function concours(): BelongsTo
    {
        return $this->belongsTo(Concours::class);
    }

    /**
     * @return HasMany<Document, $this>
     */
    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    /**
     * @return HasMany<Paiement, $this>
     */
    public function paiements(): HasMany
    {
        return $this->hasMany(Paiement::class);
    }
}
