<?php

namespace App\Services;

use App\Models\Candidature;
use App\Models\Document;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

/**
 * Enregistre une pièce justificative (site web et API mobile) :
 * les images sont compressées en JPEG, les PDF stockés tels quels.
 */
class DocumentService
{
    public function __construct(
        private readonly ImageCompressionService $compression,
    ) {}

    public function deposer(Candidature $candidature, UploadedFile $fichier, string $type): Document
    {
        abort_if(
            $candidature->visite_medicale_programmee_le !== null,
            422,
            'Votre dossier a déjà été vérifié : les pièces ne peuvent plus être modifiées.'
        );

        $extension = strtolower($fichier->getClientOriginalExtension() ?: (string) $fichier->extension());
        $contenu = null;

        if (in_array($extension, ['jpg', 'jpeg', 'png'])) {
            try {
                $contenu = $this->compression->compresser($fichier);
                $extension = 'jpg';
            } catch (Throwable $e) {
                // Image illisible par GD : on conserve le fichier d'origine plutôt que d'échouer.
                report($e);
            }
        }

        $contenu ??= file_get_contents($fichier->getRealPath());
        $chemin = "documents/{$candidature->id}/".Str::uuid().".{$extension}";

        Storage::disk('local')->put($chemin, $contenu);

        return Document::create([
            'candidature_id' => $candidature->id,
            'type' => $type,
            'chemin_fichier' => $chemin,
            'nom_original' => $fichier->getClientOriginalName(),
            'taille_octets' => strlen($contenu),
            'statut_verification' => 'en_attente',
        ]);
    }
}
