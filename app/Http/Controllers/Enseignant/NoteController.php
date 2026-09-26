<?php

namespace App\Http\Controllers\Enseignant;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaisirNoteRequest;
use App\Models\Candidature;

class NoteController extends Controller
{
    public function __invoke(SaisirNoteRequest $request, Candidature $candidature)
    {
        abort_if($candidature->note_totale !== null, 422, 'Une note a déjà été saisie pour cette copie.');

        $candidature->update(['note_totale' => $request->validated('note_totale')]);

        return back()->with('status', "Note enregistrée pour la copie {$candidature->numero_anonymat}.");
    }
}
