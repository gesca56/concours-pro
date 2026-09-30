<?php

namespace App\Http\Controllers;

use App\Enums\StatutConcours;
use App\Models\Concours;
use Illuminate\View\View;

/**
 * Pages publiques d'information sur l'IPNETP et ses concours directs.
 * Le contenu institutionnel provient du référentiel config/ipnetp.php.
 */
class PageController extends Controller
{
    public function accueil(): View
    {
        return view('welcome', [
            'concoursOuverts' => $this->concoursOuverts(),
        ]);
    }

    public function institut(): View
    {
        return view('pages.institut');
    }

    public function concours(): View
    {
        return view('pages.concours', [
            'concoursOuverts' => $this->concoursOuverts(),
        ]);
    }

    public function guide(): View
    {
        return view('pages.guide');
    }

    /**
     * Résultats définitifs des concours terminés : seuls le numéro de dossier
     * et un nom abrégé sont publiés (ni note, ni rang).
     */
    public function resultats(): View
    {
        $concoursTermines = Concours::where('statut', StatutConcours::Termine)
            ->withCount([
                'candidatures as notees_count' => fn ($q) => $q->whereNotNull('note_totale'),
            ])
            ->with(['candidatures' => fn ($q) => $q->where('statut', 'admise')->with('candidat')->orderBy('id')])
            ->orderByDesc('date_concours')
            ->get();

        return view('pages.resultats', compact('concoursTermines'));
    }

    public function preparation(): View
    {
        return view('pages.preparation');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, Concours>
     */
    private function concoursOuverts()
    {
        return Concours::where('statut', StatutConcours::Ouvert)
            ->orderBy('date_cloture')
            ->get();
    }
}
