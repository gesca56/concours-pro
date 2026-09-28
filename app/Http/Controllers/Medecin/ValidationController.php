<?php

namespace App\Http\Controllers\Medecin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ValiderAptitudeRequest;
use App\Models\Candidature;

class ValidationController extends Controller
{
    public function __invoke(ValiderAptitudeRequest $request, Candidature $candidature)
    {
        abort_if($candidature->visite_medicale_programmee_le === null, 422, "La visite médicale n'a pas encore été programmée pour ce candidat.");
        abort_unless($candidature->aptitude_medicale === 'en_attente', 422, "L'aptitude de ce candidat a déjà été déclarée.");

        $aptitude = $request->validated('aptitude_medicale');

        $candidature->update([
            'aptitude_medicale' => $aptitude,
            'motif_inaptitude' => $aptitude === 'inapte' ? $request->validated('motif_inaptitude') : null,
            'statut' => $aptitude === 'apte' ? 'validee' : 'recalee',
        ]);

        return back()->with('status', "Candidat déclaré {$aptitude}.");
    }
}
