<?php

namespace App\Http\Controllers\Receptionniste;

use App\Http\Controllers\Controller;
use App\Models\Candidature;

class VisiteMedicaleController extends Controller
{
    public function __invoke(Candidature $candidature)
    {
        abort_unless($candidature->statut === 'eligible', 422, "Ce dossier n'est pas éligible : les deux paiements doivent être validés.");
        abort_if($candidature->visite_medicale_programmee_le !== null, 422, 'La visite médicale est déjà programmée.');

        abort_unless(
            $candidature->piecesVerifiees(),
            422,
            'Toutes les pièces justificatives doivent être vérifiées (et au moins une validée) avant de programmer la visite médicale.'
        );

        $candidature->update(['visite_medicale_programmee_le' => now()]);

        return back()->with('status', 'Visite médicale programmée. Le dossier est transmis au médecin.');
    }
}
