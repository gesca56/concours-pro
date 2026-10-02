<?php

namespace App\Http\Controllers\Formation;

use App\Http\Controllers\Controller;
use App\Models\Lecon;

class LeconController extends Controller
{
    public function show(Lecon $lecon)
    {
        $user = request()->user();
        $this->autoriser($lecon);

        $lecons = $lecon->module->lecons->where('publiee', true)->values();
        $rang = $lecons->search(fn ($l) => $l->id === $lecon->id);

        return view('formation.lecons.show', [
            'lecon' => $lecon,
            'module' => $lecon->module,
            'lecons' => $lecons,
            'precedente' => $rang > 0 ? $lecons[$rang - 1] : null,
            'suivante' => $lecons[$rang + 1] ?? null,
            'terminee' => $user->leconsTerminees()->whereKey($lecon->id)->exists(),
            'leconsTerminees' => $user->leconsTerminees()->whereIn('lecons.id', $lecons->pluck('id'))->pluck('lecons.id'),
        ]);
    }

    /**
     * Marque la leçon comme terminée et passe à la suivante.
     */
    public function terminer(Lecon $lecon)
    {
        $user = request()->user();
        $this->autoriser($lecon);

        $user->leconsTerminees()->syncWithoutDetaching([$lecon->id]);

        $suivante = $lecon->module->lecons
            ->where('publiee', true)
            ->first(fn ($l) => $l->ordre > $lecon->ordre || ($l->ordre === $lecon->ordre && $l->id > $lecon->id));

        return $suivante
            ? redirect()->route('formation.lecons.show', $suivante)->with('status', 'Leçon terminée, passons à la suivante.')
            : redirect()->route('formation.modules.show', $lecon->module)->with('status', 'Bravo, vous avez terminé toutes les leçons de ce module !');
    }

    private function autoriser(Lecon $lecon): void
    {
        abort_unless($lecon->publiee && $lecon->module->estAccessiblePar(request()->user()), 403, "Cette leçon n'est pas ouverte à votre compte.");
    }
}
