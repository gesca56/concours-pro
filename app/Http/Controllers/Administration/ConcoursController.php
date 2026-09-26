<?php

namespace App\Http\Controllers\Administration;

use App\Enums\StatutConcours;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreConcoursRequest;
use App\Models\Concours;
use Illuminate\Validation\Rule;

class ConcoursController extends Controller
{
    public function index()
    {
        $concours = Concours::withCount('candidatures')->latest()->get();

        return view('administration.concours.index', compact('concours'));
    }

    public function create()
    {
        return view('administration.concours.create');
    }

    public function store(StoreConcoursRequest $request)
    {
        $concours = Concours::create($request->validated() + ['statut' => StatutConcours::Brouillon]);

        return redirect()->route('administration.concours.show', $concours)
            ->with('status', 'Concours créé en brouillon.');
    }

    public function show(Concours $concours)
    {
        $concours->load(['candidatures' => fn ($q) => $q->with('candidat')]);

        return view('administration.concours.show', compact('concours'));
    }

    public function edit(Concours $concours)
    {
        return view('administration.concours.edit', compact('concours'));
    }

    public function update(StoreConcoursRequest $request, Concours $concours)
    {
        $concours->update($request->validated());

        return redirect()->route('administration.concours.show', $concours)
            ->with('status', 'Concours mis à jour.');
    }

    public function changerStatut(Concours $concours)
    {
        $transitions = [
            StatutConcours::Brouillon->value => StatutConcours::Ouvert,
            StatutConcours::Ouvert->value => StatutConcours::Cloture,
            StatutConcours::Cloture->value => StatutConcours::Deliberation,
            StatutConcours::Deliberation->value => StatutConcours::Termine,
        ];

        request()->validate([
            'statut' => [Rule::in(array_keys($transitions))],
        ]);

        $suivant = $transitions[$concours->statut->value] ?? null;

        abort_if($suivant === null, 422, 'Aucune transition possible depuis ce statut.');

        $concours->update(['statut' => $suivant]);

        return back()->with('status', "Concours passé au statut « {$suivant->value} ».");
    }
}
