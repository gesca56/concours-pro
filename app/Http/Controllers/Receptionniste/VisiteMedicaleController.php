<?php

namespace App\Http\Controllers\Receptionniste;

use App\Http\Controllers\Controller;
use App\Models\Candidature;

class VisiteMedicaleController extends Controller
{
    public function __invoke(Candidature $candidature)
    {
        abort_if(
            $candidature->documents->isEmpty() || $candidature->documents->contains(fn ($d) => $d->statut_verification !== 'valide'),
            422,
            'Toutes les pièces justificatives doivent être validées avant de programmer la visite médicale.'
        );

        $candidature->update(['visite_medicale_programmee_le' => now()]);

        return back()->with('status', 'Visite médicale programmée. Le dossier est transmis au médecin.');
    }
}
