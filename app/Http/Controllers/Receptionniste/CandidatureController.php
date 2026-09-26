<?php

namespace App\Http\Controllers\Receptionniste;

use App\Http\Controllers\Controller;
use App\Models\Candidature;

class CandidatureController extends Controller
{
    public function show(Candidature $candidature)
    {
        $candidature->load(['candidat', 'concours', 'documents', 'paiements']);

        return view('receptionniste.candidatures.show', compact('candidature'));
    }
}
