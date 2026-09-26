<?php

namespace App\Http\Controllers;

use App\Models\Candidature;
use Illuminate\Http\Request;

class VerificationQrController extends Controller
{
    public function __invoke(Request $request)
    {
        $candidature = null;
        $recherche = false;

        if ($request->filled('jeton')) {
            $recherche = true;
            $candidature = Candidature::with(['candidat', 'concours'])
                ->where('jeton_convocation', trim($request->string('jeton')))
                ->first();
        }

        return view('verification-qr', compact('candidature', 'recherche'));
    }
}
