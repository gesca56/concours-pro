<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Document extends Model
{
    protected $fillable = [
        'candidature_id',
        'type',
        'chemin_fichier',
        'nom_original',
        'taille_octets',
        'statut_verification',
        'motif_rejet',
    ];

    /**
     * @return BelongsTo<Candidature, $this>
     */
    public function candidature(): BelongsTo
    {
        return $this->belongsTo(Candidature::class);
    }
}
