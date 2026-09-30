<?php

namespace App\Http\Controllers\Administration;

use App\Enums\StatutConcours;
use App\Http\Controllers\Controller;
use App\Models\Candidature;
use App\Models\Concours;
use App\Models\Paiement;
use App\Models\SignalementPaiement;

class DashboardController extends Controller
{
    public function __invoke()
    {
        // Compteurs par étape du parcours, calculés en base pour chaque concours.
        $concours = Concours::withCount([
            'candidatures',
            'candidatures as eligibles_count' => fn ($q) => $q->whereIn('statut', ['eligible', 'validee', 'admise', 'recalee']),
            'candidatures as validees_count' => fn ($q) => $q->where('aptitude_medicale', 'apte'),
            'candidatures as notees_count' => fn ($q) => $q->whereNotNull('note_totale'),
            'candidatures as admises_count' => fn ($q) => $q->where('statut', 'admise'),
        ])->latest()->get();

        $stats = [
            'concours' => $concours->count(),
            'concours_ouverts' => $concours->where('statut', StatutConcours::Ouvert)->count(),
            'candidatures' => Candidature::count(),
            'admises' => Candidature::where('statut', 'admise')->count(),
            'recettes' => (float) Paiement::where('statut', 'valide')->sum('montant'),
            'signalements' => SignalementPaiement::where('statut', 'en_attente')->count(),
        ];

        // Répartition des candidatures par corps (CAP/PL, PC, IFPB, IAFPB).
        $parCycle = Candidature::join('concours', 'concours.id', '=', 'candidatures.concours_id')
            ->selectRaw('concours.cycle as cycle, count(*) as total')
            ->groupBy('concours.cycle')
            ->pluck('total', 'cycle');

        return view('administration.dashboard', compact('concours', 'stats', 'parCycle'));
    }
}
