<?php

namespace App\Http\Controllers\Enseignant;

use App\Models\Candidature;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function __invoke()
    {
        // Volontairement sans la relation "candidat" : la correction reste anonyme,
        // seul le numéro d'anonymat identifie la copie.
        $candidatures = Candidature::with('concours')
            ->whereIn('statut', ['validee'])
            ->whereNotNull('numero_anonymat')
            ->whereNull('note_totale')
            ->latest()
            ->get();

        return view('enseignant.dashboard', compact('candidatures'));
    }
}
