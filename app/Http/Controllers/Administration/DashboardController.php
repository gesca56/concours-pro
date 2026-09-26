<?php

namespace App\Http\Controllers\Administration;

use App\Enums\StatutConcours;
use App\Http\Controllers\Controller;
use App\Models\Candidature;
use App\Models\Concours;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $concours = Concours::withCount('candidatures')->latest()->get();

        $stats = [
            'concours' => $concours->count(),
            'concours_ouverts' => $concours->where('statut', StatutConcours::Ouvert)->count(),
            'candidatures' => Candidature::count(),
            'admises' => Candidature::where('statut', 'admise')->count(),
        ];

        return view('administration.dashboard', compact('concours', 'stats'));
    }
}
