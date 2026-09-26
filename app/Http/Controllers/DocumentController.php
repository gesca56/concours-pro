<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDocumentRequest;
use App\Models\Candidature;
use App\Models\Document;
use App\Services\ImageCompressionService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentController extends Controller
{
    public function __construct(
        private readonly ImageCompressionService $compression,
    ) {}

    public function store(StoreDocumentRequest $request, Candidature $candidature)
    {
        abort_unless($candidature->user_id === $request->user()->id, 403);

        $fichier = $request->file('fichier');
        $estImage = in_array($fichier->getClientOriginalExtension(), ['jpg', 'jpeg', 'png']);

        $extension = $estImage ? 'jpg' : $fichier->getClientOriginalExtension();
        $chemin = "documents/{$candidature->id}/".Str::uuid().".{$extension}";

        $contenu = $estImage
            ? $this->compression->compresser($fichier)
            : file_get_contents($fichier->getRealPath());

        Storage::disk('local')->put($chemin, $contenu);

        Document::create([
            'candidature_id' => $candidature->id,
            'type' => $request->validated('type'),
            'chemin_fichier' => $chemin,
            'nom_original' => $fichier->getClientOriginalName(),
            'taille_octets' => strlen($contenu),
            'statut_verification' => 'en_attente',
        ]);

        return redirect()
            ->route('candidatures.show', $candidature)
            ->with('status', 'Document déposé avec succès.');
    }
}
