<?php

namespace App\Http\Controllers\Receptionniste;

use App\Http\Controllers\Controller;
use App\Http\Requests\VerifierDocumentRequest;
use App\Models\Document;

class DocumentController extends Controller
{
    public function __invoke(VerifierDocumentRequest $request, Document $document)
    {
        abort_if(
            $document->candidature->visite_medicale_programmee_le !== null,
            422,
            'La visite médicale est déjà programmée : les pièces de ce dossier ne peuvent plus être modifiées.'
        );

        $document->update([
            'statut_verification' => $request->validated('statut_verification'),
            'motif_rejet' => $request->validated('statut_verification') === 'rejete'
                ? $request->validated('motif_rejet')
                : null,
        ]);

        return back()->with('status', 'Document mis à jour.');
    }
}
