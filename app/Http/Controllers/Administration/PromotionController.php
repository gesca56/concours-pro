<?php

namespace App\Http\Controllers\Administration;

use App\Http\Controllers\Controller;
use App\Models\Concours;
use App\Models\Module;
use App\Models\Promotion;
use App\Models\Seance;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PromotionController extends Controller
{
    public function index()
    {
        $promotions = Promotion::with('concours')
            ->withCount(['eleves', 'modules'])
            ->latest()
            ->get();

        return view('administration.promotions.index', compact('promotions'));
    }

    public function create()
    {
        return view('administration.promotions.create', ['concours' => $this->concoursAvecAdmis()]);
    }

    public function store(Request $request)
    {
        $promotion = Promotion::create($this->valider($request));

        $message = 'Promotion créée.';
        if ($promotion->concours && $request->boolean('inscrire_admis')) {
            $n = $promotion->inscrireAdmis($promotion->concours);
            $message .= " {$n} admis inscrit(s) automatiquement.";
        }

        return redirect()->route('administration.promotions.show', $promotion)->with('status', $message);
    }

    public function show(Promotion $promotion)
    {
        $promotion->load([
            'concours',
            'eleves',
            'modules' => fn ($q) => $q->with('enseignant')->withCount(['lecons', 'devoirs'])->orderBy('titre'),
            'seances' => fn ($q) => $q->with('module')->where('fin', '>=', now()->startOfWeek()),
            'annonces.auteur',
        ]);

        return view('administration.promotions.show', [
            'promotion' => $promotion,
            'concours' => $this->concoursAvecAdmis(),
            'typesSeance' => Seance::TYPES,
        ]);
    }

    public function edit(Promotion $promotion)
    {
        return view('administration.promotions.edit', ['promotion' => $promotion, 'concours' => $this->concoursAvecAdmis()]);
    }

    public function update(Request $request, Promotion $promotion)
    {
        $promotion->update($this->valider($request, $promotion));

        return redirect()->route('administration.promotions.show', $promotion)->with('status', 'Promotion mise à jour.');
    }

    public function destroy(Promotion $promotion)
    {
        abort_if($promotion->modules()->exists(), 422, "Supprimez d'abord les modules de cette promotion.");

        $promotion->delete();

        return redirect()->route('administration.promotions.index')->with('status', 'Promotion supprimée.');
    }

    /**
     * Inscrit dans la promotion tous les admis d'un concours terminé.
     */
    public function inscrireAdmis(Request $request, Promotion $promotion)
    {
        $request->validate(['concours_id' => ['required', 'exists:concours,id']]);

        $n = $promotion->inscrireAdmis(Concours::findOrFail($request->integer('concours_id')));

        return back()->with('status', $n ? "{$n} élève(s)-professeur(s) inscrit(s)." : 'Tous les admis de ce concours étaient déjà inscrits.');
    }

    public function retirer(Promotion $promotion, User $eleve)
    {
        $promotion->eleves()->detach($eleve->id);

        return back()->with('status', "{$eleve->name} a été retiré(e) de la promotion.");
    }

    public function annoncer(Request $request, Promotion $promotion)
    {
        $promotion->annonces()->create($request->validate([
            'titre' => ['required', 'string', 'max:255'],
            'contenu' => ['required', 'string', 'max:5000'],
        ]) + ['auteur_id' => $request->user()->id]);

        return back()->with('status', 'Annonce envoyée à la promotion.');
    }

    /**
     * Concours ayant au moins un admis, sources possibles d'une promotion.
     */
    private function concoursAvecAdmis()
    {
        return Concours::whereHas('candidatures', fn ($q) => $q->where('statut', 'admise'))
            ->withCount(['candidatures as admis_count' => fn ($q) => $q->where('statut', 'admise')])
            ->latest('date_concours')
            ->get();
    }

    /**
     * @return array<string, mixed>
     */
    private function valider(Request $request, ?Promotion $promotion = null): array
    {
        return $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:20', 'alpha_dash', Rule::unique('promotions')->ignore($promotion)],
            'cycle' => ['required', Rule::in(array_keys(config('ipnetp.cycles')))],
            'specialite' => ['nullable', 'string', 'max:255'],
            'annee_academique' => ['required', 'string', 'regex:/^\d{4}-\d{4}$/'],
            'concours_id' => ['nullable', 'exists:concours,id'],
            'date_debut' => ['nullable', 'date'],
            'date_fin' => ['nullable', 'date', 'after:date_debut'],
            'statut' => ['required', Rule::in(['en_cours', 'terminee'])],
        ], [
            'annee_academique.regex' => "L'année académique s'écrit sous la forme 2026-2027.",
            'code.alpha_dash' => 'Le code ne contient que des lettres, chiffres et tirets (ex. PL26-INFO).',
        ]);
    }
}
