<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RenduDevoir extends Model
{
    protected $table = 'rendus_devoir';

    protected $fillable = [
        'devoir_id',
        'user_id',
        'contenu',
        'lien',
        'rendu_le',
        'en_retard',
        'note',
        'appreciation',
        'corrige_le',
    ];

    protected function casts(): array
    {
        return [
            'rendu_le' => 'datetime',
            'corrige_le' => 'datetime',
            'en_retard' => 'boolean',
            'note' => 'decimal:2',
        ];
    }

    /**
     * @return BelongsTo<Devoir, $this>
     */
    public function devoir(): BelongsTo
    {
        return $this->belongsTo(Devoir::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function eleve(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function estCorrige(): bool
    {
        return $this->corrige_le !== null;
    }
}
