<?php

namespace App\Http\Controllers;

use App\Models\Paiement;
use Barryvdh\DomPDF\Facade\Pdf;

class RecuController extends Controller
{
    public function show(Paiement $paiement)
    {
        abort_unless($paiement->candidature->user_id === request()->user()->id, 403);
        abort_unless($paiement->statut === 'valide', 404, 'Reçu disponible uniquement pour un paiement validé.');

        $pdf = Pdf::loadView('pdf.recu', ['paiement' => $paiement->load('candidature.concours', 'candidature.candidat')]);

        return $pdf->stream("recu-paiement-{$paiement->id}.pdf");
    }
}
