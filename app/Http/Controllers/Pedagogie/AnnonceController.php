<?php

namespace App\Http\Controllers\Pedagogie;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Models\Annonce;
use App\Models\Module;
use Illuminate\Http\Request;

class AnnonceController extends Controller
{
    public function store(Request $request, Module $module)
    {
        abort_unless($module->estGerablePar($request->user()), 403, 'Ce module est géré par un autre enseignant.');

        $module->annonces()->create($request->validate([
            'titre' => ['required', 'string', 'max:255'],
            'contenu' => ['required', 'string', 'max:5000'],
        ]) + ['auteur_id' => $request->user()->id]);

        return back()->with('status', 'Annonce publiée.');
    }

    public function destroy(Request $request, Annonce $annonce)
    {
        abort_unless(
            $request->user()->role === Role::Administration || $annonce->auteur_id === $request->user()->id,
            403,
            "Seul l'auteur de l'annonce peut la retirer."
        );

        $annonce->delete();

        return back()->with('status', 'Annonce retirée.');
    }
}
