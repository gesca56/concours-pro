<?php

namespace App\Http\Controllers\Receptionniste;

use App\Http\Controllers\Controller;
use App\Http\Requests\VerifierDocumentRequest;
use App\Models\Document;

class DocumentController extends Controller
{
    public function __invoke(VerifierDocumentRequest $request, Document $document)
    {
        $document->update([
            'statut_verification' => $request->validated('statut_verification'),
            'motif_rejet' => $request->validated('statut_verification') === 'rejete'
                ? $request->validated('motif_rejet')
                : null,
        ]);

        return back()->with('status', 'Document mis à jour.');
    }
}
