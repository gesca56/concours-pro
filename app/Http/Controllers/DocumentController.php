<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDocumentRequest;
use App\Models\Candidature;
use App\Services\DocumentService;

class DocumentController extends Controller
{
    public function __construct(
        private readonly DocumentService $documents,
    ) {}

    public function store(StoreDocumentRequest $request, Candidature $candidature)
    {
        abort_unless($candidature->user_id === $request->user()->id, 403);

        $this->documents->deposer($candidature, $request->file('fichier'), $request->validated('type'));

        return redirect()
            ->route('candidatures.show', $candidature)
            ->with('status', 'Document déposé avec succès.');
    }
}
