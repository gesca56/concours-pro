<?php

namespace App\Http\Controllers\Pedagogie;

use App\Http\Controllers\Controller;
use App\Models\Devoir;
use App\Models\Module;
use Illuminate\Http\Request;

class DevoirController extends Controller
{
    public function create(Module $module)
    {
        $this->autoriser($module);

        return view('pedagogie.devoirs.create', compact('module'));
    }

    public function store(Request $request, Module $module)
    {
        $this->autoriser($module);

        $devoir = $module->devoirs()->create($this->valider($request));

        return redirect()->route('pedagogie.devoirs.show', $devoir)->with('status', 'Devoir publié.');
    }

    /**
     * Copies rendues par les élèves de la promotion, avec le formulaire de correction.
     */
    public function show(Devoir $devoir)
    {
        $this->autoriser($devoir->module);

        $devoir->load('module.promotion');
        $rendus = $devoir->rendus()->with('eleve')->get()->keyBy('user_id');
        $eleves = $devoir->module->promotion->eleves;

        return view('pedagogie.devoirs.show', compact('devoir', 'rendus', 'eleves'));
    }

    public function edit(Devoir $devoir)
    {
        $this->autoriser($devoir->module);

        return view('pedagogie.devoirs.edit', ['devoir' => $devoir, 'module' => $devoir->module]);
    }

    public function update(Request $request, Devoir $devoir)
    {
        $this->autoriser($devoir->module);

        $devoir->update($this->valider($request));

        return redirect()->route('pedagogie.devoirs.show', $devoir)->with('status', 'Devoir mis à jour.');
    }

    public function destroy(Devoir $devoir)
    {
        $this->autoriser($devoir->module);

        $devoir->delete();

        return redirect()->route('pedagogie.modules.show', $devoir->module_id)->with('status', 'Devoir supprimé.');
    }

    /**
     * Les devoirs notés n'existent que dans les modules de formation (une promotion à évaluer).
     */
    private function autoriser(Module $module): void
    {
        abort_unless($module->estGerablePar(request()->user()), 403, 'Ce module est géré par un autre enseignant.');
        abort_if($module->estPreparation(), 404, 'Les modules de préparation au concours utilisent des quiz, pas des devoirs.');
    }

    /**
     * @return array<string, mixed>
     */
    private function valider(Request $request): array
    {
        $donnees = $request->validate([
            'titre' => ['required', 'string', 'max:255'],
            'consignes' => ['required', 'string', 'max:10000'],
            'date_limite' => ['required', 'date'],
            'publie' => ['boolean'],
        ]);

        $donnees['publie'] = $request->boolean('publie');

        return $donnees;
    }
}
