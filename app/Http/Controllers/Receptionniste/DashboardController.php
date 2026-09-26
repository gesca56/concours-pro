<?php

namespace App\Http\Controllers\Receptionniste;

use App\Http\Controllers\Controller;
use App\Models\Candidature;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $candidatures = Candidature::with(['candidat', 'concours', 'documents'])
            ->whereIn('statut', ['eligible'])
            ->latest()
            ->get();

        return view('receptionniste.dashboard', compact('candidatures'));
    }
}
