<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SignalementPaiement extends Model
{
    protected $fillable = [
        'paiement_id',
        'user_id',
        'message',
        'statut',
        'reponse_administration',
    ];

    /**
     * @return BelongsTo<Paiement, $this>
     */
    public function paiement(): BelongsTo
    {
        return $this->belongsTo(Paiement::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function candidat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
