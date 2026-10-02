<?php

namespace App\Http\Controllers\Formation;

use App\Http\Controllers\Controller;
use App\Models\Devoir;
use Illuminate\Http\Request;

class DevoirController extends Controller
{
    public function show(Devoir $devoir)
    {
        $this->autoriser($devoir);

        return view('formation.devoirs.show', [
            'devoir' => $devoir->load('module'),
            'rendu' => $devoir->rendus()->where('user_id', request()->user()->id)->first(),
        ]);
    }

    /**
     * Dépôt (ou modification, tant que la copie n'est pas corrigée) d'un rendu.
     * Un dépôt après la date limite est accepté mais signalé en retard.
     */
    public function store(Request $request, Devoir $devoir)
    {
        $user = $request->user();
        $this->autoriser($devoir);

        $rendu = $devoir->rendus()->where('user_id', $user->id)->first();
        abort_if($rendu?->estCorrige(), 422, 'Cette copie a déjà été corrigée : elle ne peut plus être modifiée.');

        $donnees = $request->validate([
            'contenu' => ['required', 'string', 'min:20', 'max:60000'],
            'lien' => ['nullable', 'url:http,https', 'max:255'],
        ], [
            'contenu.min' => 'Votre réponse est trop courte (20 caractères au minimum).',
        ]);

        $devoir->rendus()->updateOrCreate(
            ['user_id' => $user->id],
            $donnees + ['rendu_le' => now(), 'en_retard' => $devoir->estEchu()]
        );

        return redirect()->route('formation.devoirs.show', $devoir)
            ->with('status', $devoir->estEchu() ? 'Copie déposée (en retard).' : 'Copie déposée. Vous pouvez la modifier jusqu\'à sa correction.');
    }

    private function autoriser(Devoir $devoir): void
    {
        abort_unless($devoir->publie && $devoir->module->estAccessiblePar(request()->user()), 403, "Ce devoir n'est pas ouvert à votre compte.");
        abort_if($devoir->module->estPreparation(), 404);
    }
}
