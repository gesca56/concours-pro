<?php

namespace App\Http\Controllers\Medecin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ValiderAptitudeRequest;
use App\Models\Candidature;

class ValidationController extends Controller
{
    public function __invoke(ValiderAptitudeRequest $request, Candidature $candidature)
    {
        $aptitude = $request->validated('aptitude_medicale');

        $candidature->update([
            'aptitude_medicale' => $aptitude,
            'motif_inaptitude' => $aptitude === 'inapte' ? $request->validated('motif_inaptitude') : null,
            'statut' => $aptitude === 'apte' ? 'validee' : 'recalee',
        ]);

        return back()->with('status', "Candidat déclaré {$aptitude}.");
    }
}
