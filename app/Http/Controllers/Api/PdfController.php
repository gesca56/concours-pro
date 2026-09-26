<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Candidature;
use App\Models\Paiement;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PdfController extends Controller
{
    public function convocation(Candidature $candidature)
    {
        abort_unless($candidature->user_id === request()->user()->id, 403);
        abort_unless($candidature->jeton_convocation !== null, 404, "La convocation n'est pas encore disponible.");

        $qrCode = base64_encode(
            QrCode::format('svg')->size(180)->margin(1)->generate($candidature->jeton_convocation)
        );

        return response(
            Pdf::loadView('pdf.convocation', compact('candidature', 'qrCode'))->output(),
            200,
            ['Content-Type' => 'application/pdf']
        );
    }

    public function fiche(Candidature $candidature)
    {
        abort_unless($candidature->user_id === request()->user()->id, 403);

        $candidature->load('concours', 'candidat');

        return response(
            Pdf::loadView('pdf.fiche-candidature', compact('candidature'))->output(),
            200,
            ['Content-Type' => 'application/pdf']
        );
    }

    public function recu(Paiement $paiement)
    {
        abort_unless($paiement->candidature->user_id === request()->user()->id, 403);
        abort_unless($paiement->statut === 'valide', 404, 'Reçu disponible uniquement pour un paiement validé.');

        return response(
            Pdf::loadView('pdf.recu', ['paiement' => $paiement->load('candidature.concours', 'candidature.candidat')])->output(),
            200,
            ['Content-Type' => 'application/pdf']
        );
    }
}
