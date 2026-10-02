<?php

namespace App\Http\Controllers\Pedagogie;

use App\Http\Controllers\Controller;
use App\Models\Lecon;
use App\Models\Module;
use Illuminate\Http\Request;

class LeconController extends Controller
{
    public function create(Module $module)
    {
        $this->autoriser($module);

        return view('pedagogie.lecons.create', compact('module'));
    }

    public function store(Request $request, Module $module)
    {
        $this->autoriser($module);

        $donnees = $this->valider($request);
        $donnees['ordre'] ??= (int) $module->lecons()->max('ordre') + 1;
        $module->lecons()->create($donnees);

        return redirect()->route('pedagogie.modules.show', $module)->with('status', 'Leçon ajoutée.');
    }

    public function edit(Lecon $lecon)
    {
        $this->autoriser($lecon->module);

        return view('pedagogie.lecons.edit', ['lecon' => $lecon, 'module' => $lecon->module]);
    }

    public function update(Request $request, Lecon $lecon)
    {
        $this->autoriser($lecon->module);

        $donnees = $this->valider($request);
        $donnees['ordre'] ??= $lecon->ordre;
        $lecon->update($donnees);

        return redirect()->route('pedagogie.modules.show', $lecon->module)->with('status', 'Leçon mise à jour.');
    }

    public function destroy(Lecon $lecon)
    {
        $this->autoriser($lecon->module);

        $lecon->delete();

        return back()->with('status', 'Leçon supprimée.');
    }

    private function autoriser(Module $module): void
    {
        abort_unless($module->estGerablePar(request()->user()), 403, 'Ce module est géré par un autre enseignant.');
    }

    /**
     * @return array<string, mixed>
     */
    private function valider(Request $request): array
    {
        $donnees = $request->validate([
            'titre' => ['required', 'string', 'max:255'],
            'resume' => ['nullable', 'string', 'max:255'],
            'contenu' => ['required', 'string', 'max:60000'],
            'video_url' => ['nullable', 'url:http,https', 'max:255', 'regex:~(youtube\.com|youtu\.be)/~i'],
            'lien_ressource' => ['nullable', 'url:http,https', 'max:255'],
            'libelle_ressource' => ['nullable', 'string', 'max:120'],
            'duree_minutes' => ['nullable', 'integer', 'between:1,600'],
            'ordre' => ['nullable', 'integer', 'between:0,999'],
            'publiee' => ['boolean'],
        ], [
            'video_url.regex' => 'Seules les vidéos YouTube peuvent être intégrées (lien youtube.com ou youtu.be).',
        ]);

        $donnees['publiee'] = $request->boolean('publiee');

        return $donnees;
    }
}
