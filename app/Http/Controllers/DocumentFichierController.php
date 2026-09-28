<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Models\Document;
use Illuminate\Support\Facades\Storage;

/**
 * Affiche une pièce justificative : au candidat propriétaire, à la réception et à l'administration.
 */
class DocumentFichierController extends Controller
{
    public function __invoke(Document $document)
    {
        $user = request()->user();

        abort_if(
            $user->role === Role::Candidat && $document->candidature->user_id !== $user->id,
            403
        );

        if (! Storage::disk('local')->exists($document->chemin_fichier)) {
            return back()->with('error', "Le fichier « {$document->nom_original} » n'est plus disponible sur le serveur. Le candidat doit le déposer à nouveau.");
        }

        return Storage::disk('local')->response($document->chemin_fichier, $document->nom_original, [
            'Content-Disposition' => 'inline; filename="'.addslashes($document->nom_original).'"',
        ]);
    }
}
