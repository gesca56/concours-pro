<?php

namespace App\Http\Controllers\Formation;

use App\Http\Controllers\Controller;
use App\Models\Module;

class ModuleController extends Controller
{
    public function show(Module $module)
    {
        $user = request()->user();
        abort_unless($module->estAccessiblePar($user), 403, "Ce module n'est pas ouvert à votre compte.");

        $module->load([
            'enseignant',
            'promotion',
            'lecons' => fn ($q) => $q->where('publiee', true),
            'quiz' => fn ($q) => $q->where('publie', true)->withCount('questions')->with(['tentatives' => fn ($t) => $t->where('user_id', $user->id)]),
            'devoirs' => fn ($q) => $q->where('publie', true)->with(['rendus' => fn ($r) => $r->where('user_id', $user->id)]),
            'annonces.auteur',
        ]);

        $leconsTerminees = $user->leconsTerminees()->whereIn('lecons.id', $module->lecons->pluck('id'))->pluck('lecons.id');

        return view('formation.modules.show', compact('module', 'leconsTerminees'));
    }
}
