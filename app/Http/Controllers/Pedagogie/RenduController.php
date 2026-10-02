<?php

namespace App\Http\Controllers\Pedagogie;

use App\Http\Controllers\Controller;
use App\Models\RenduDevoir;
use Illuminate\Http\Request;

class RenduController extends Controller
{
    /**
     * Correction d'une copie : note sur 20 et appréciation.
     */
    public function __invoke(Request $request, RenduDevoir $rendu)
    {
        abort_unless($rendu->devoir->module->estGerablePar($request->user()), 403, 'Ce module est géré par un autre enseignant.');

        $donnees = $request->validate([
            'note' => ['required', 'numeric', 'between:0,20'],
            'appreciation' => ['nullable', 'string', 'max:2000'],
        ]);

        $rendu->update($donnees + ['corrige_le' => now()]);

        return back()->with('status', "Copie de {$rendu->eleve->name} notée {$rendu->note}/20.");
    }
}
