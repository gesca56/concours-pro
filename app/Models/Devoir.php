<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Devoir noté sur 20. L'élève rend un texte et, s'il le souhaite, un lien
 * (Google Drive, OneDrive…) vers son fichier : rien n'est stocké sur le serveur.
 */
class Devoir extends Model
{
    protected $fillable = [
        'module_id',
        'titre',
        'consignes',
        'date_limite',
        'publie',
    ];

    protected function casts(): array
    {
        return [
            'date_limite' => 'datetime',
            'publie' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<Module, $this>
     */
    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    /**
     * @return HasMany<RenduDevoir, $this>
     */
    public function rendus(): HasMany
    {
        return $this->hasMany(RenduDevoir::class);
    }

    public function estEchu(): bool
    {
        return $this->date_limite->isPast();
    }
}
