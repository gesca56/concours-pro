<?php

namespace App\Http\Controllers;

use App\Models\Candidature;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ConvocationController extends Controller
{
    public function show(Candidature $candidature)
    {
        abort_unless($candidature->user_id === request()->user()->id, 403);
        abort_unless($candidature->jeton_convocation !== null, 404, "La convocation n'est pas encore disponible : les paiements doivent être validés.");

        $qrCode = base64_encode(
            QrCode::format('svg')->size(180)->margin(1)->generate($candidature->jeton_convocation)
        );

        $pdf = Pdf::loadView('pdf.convocation', compact('candidature', 'qrCode'));

        return $pdf->stream("convocation-{$candidature->id}.pdf");
    }
}
