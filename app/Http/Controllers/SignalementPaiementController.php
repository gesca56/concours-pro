<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSignalementPaiementRequest;
use App\Models\Paiement;

class SignalementPaiementController extends Controller
{
    public function store(StoreSignalementPaiementRequest $request, Paiement $paiement)
    {
        abort_unless($paiement->candidature->user_id === $request->user()->id, 403);

        abort_if(
            $paiement->signalements()->where('statut', 'en_attente')->exists(),
            422,
            'Un signalement est déjà en cours de traitement pour ce paiement.'
        );

        $paiement->signalements()->create([
            'user_id' => $request->user()->id,
            'message' => $request->validated('message'),
        ]);

        return back()->with('status', 'Votre signalement a été transmis. L\'administration vous répondra sous peu.');
    }
}
