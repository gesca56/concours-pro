<?php

namespace App\Http\Controllers\Pedagogie;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\Promotion;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ModuleController extends Controller
{
    public function create()
    {
        return view('pedagogie.modules.create', $this->listes());
    }

    public function store(Request $request)
    {
        $module = Module::create($this->valider($request));

        return redirect()->route('pedagogie.modules.show', $module)
            ->with('status', 'Module créé. Ajoutez maintenant ses leçons.');
    }

    public function show(Module $module)
    {
        $this->autoriser($module);

        $module->load([
            'promotion',
            'enseignant',
            'lecons',
            'quiz' => fn ($q) => $q->withCount(['questions', 'tentatives']),
            'devoirs' => fn ($q) => $q->withCount(['rendus', 'rendus as rendus_a_corriger_count' => fn ($r) => $r->whereNull('corrige_le')]),
            'annonces.auteur',
        ]);

        return view('pedagogie.modules.show', compact('module'));
    }

    public function edit(Module $module)
    {
        $this->autoriser($module);

        return view('pedagogie.modules.edit', ['module' => $module] + $this->listes());
    }

    public function update(Request $request, Module $module)
    {
        $this->autoriser($module);

        $module->update($this->valider($request, $module));

        return redirect()->route('pedagogie.modules.show', $module)->with('status', 'Module mis à jour.');
    }

    public function destroy(Module $module)
    {
        $this->autoriser($module);

        $module->delete();

        return redirect()->route('pedagogie.dashboard')->with('status', "Module « {$module->titre} » supprimé.");
    }

    private function autoriser(Module $module): void
    {
        abort_unless($module->estGerablePar(request()->user()), 403, "Ce module est géré par un autre enseignant.");
    }

    /**
     * @return array<string, mixed>
     */
    private function listes(): array
    {
        return [
            'promotions' => Promotion::where('statut', 'en_cours')->orderBy('nom')->get(),
            'enseignants' => User::where('role', Role::Enseignant)->orderBy('name')->get(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function valider(Request $request, ?Module $module = null): array
    {
        $estAdmin = $request->user()->role === Role::Administration;

        $donnees = $request->validate([
            'titre' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:30'],
            'description' => ['nullable', 'string', 'max:2000'],
            'objectifs' => ['nullable', 'string', 'max:2000'],
            'public' => ['required', Rule::in([Module::PUBLIC_PREPARATION, Module::PUBLIC_FORMATION])],
            'promotion_id' => ['nullable', 'required_if:public,'.Module::PUBLIC_FORMATION, 'exists:promotions,id'],
            'cycle' => ['nullable', Rule::in(array_keys(config('ipnetp.cycles')))],
            'enseignant_id' => [$estAdmin ? 'nullable' : 'prohibited', Rule::exists('users', 'id')->where('role', Role::Enseignant->value)],
            'coefficient' => ['required', 'integer', 'between:1,10'],
            'volume_horaire' => ['nullable', 'integer', 'between:1,500'],
            'publie' => ['boolean'],
        ], [
            'promotion_id.required_if' => 'Choisissez la promotion qui suit ce module de formation.',
        ]);

        $donnees['publie'] = $request->boolean('publie');

        if ($donnees['public'] === Module::PUBLIC_PREPARATION) {
            $donnees['promotion_id'] = null;
            $donnees['coefficient'] = 1;
        } else {
            $donnees['cycle'] = null;
        }

        if (! $estAdmin) {
            $donnees['enseignant_id'] = $module->enseignant_id ?? $request->user()->id;
        }

        return $donnees;
    }
}
