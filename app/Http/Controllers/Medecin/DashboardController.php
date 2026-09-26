<?php

namespace App\Http\Controllers\Medecin;

use App\Http\Controllers\Controller;
use App\Models\Candidature;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $candidatures = Candidature::with(['candidat', 'concours'])
            ->whereNotNull('visite_medicale_programmee_le')
            ->where('aptitude_medicale', 'en_attente')
            ->latest('visite_medicale_programmee_le')
            ->get();

        return view('medecin.dashboard', compact('candidatures'));
    }
}
