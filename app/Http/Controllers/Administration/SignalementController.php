<?php

namespace App\Http\Controllers\Administration;

use App\Http\Controllers\Controller;
use App\Http\Requests\TraiterSignalementRequest;
use App\Models\SignalementPaiement;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * Traitement par le service des concours des paiements contestés par les candidats.
 */
class SignalementController extends Controller
{
    public function index(): View
    {
        $signalements = SignalementPaiement::with('candidat', 'paiement.candidature.concours')
            ->latest()
            ->get();

        return view('administration.signalements.index', [
            'enAttente' => $signalements->where('statut', 'en_attente'),
            'traites' => $signalements->where('statut', 'traite')->take(20),
        ]);
    }

    public function update(TraiterSignalementRequest $request, SignalementPaiement $signalement): RedirectResponse
    {
        abort_if($signalement->statut === 'traite', 422, 'Ce signalement a déjà été traité.');

        $signalement->update([
            'statut' => 'traite',
            'reponse_administration' => $request->validated('reponse_administration'),
        ]);

        return back()->with('status', 'Réponse envoyée au candidat.');
    }
}
