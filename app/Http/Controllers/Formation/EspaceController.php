<?php

namespace App\Http\Controllers\Formation;

use App\Http\Controllers\Controller;
use App\Models\Annonce;
use App\Models\Devoir;
use App\Models\Module;

/**
 * Accueil de l'espace e-learning : formation de l'élève-professeur
 * (s'il est inscrit dans une promotion) et préparation au concours.
 */
class EspaceController extends Controller
{
    public function __invoke()
    {
        $user = request()->user();
        $promotion = $user->promotionActive();

        $modulesFormation = $promotion
            ? $promotion->modules()->where('publie', true)->with(['enseignant', 'lecons'])->orderBy('titre')->get()
            : collect();
        $idsModules = $modulesFormation->pluck('id');

        $seances = $promotion
            ? $promotion->seances()->with('module')->where('fin', '>=', now())->limit(5)->get()
            : collect();

        $devoirs = Devoir::with('module')
            ->whereIn('module_id', $idsModules)
            ->where('publie', true)
            ->where('date_limite', '>=', now()->subDays(7))
            ->whereDoesntHave('rendus', fn ($q) => $q->where('user_id', $user->id))
            ->orderBy('date_limite')
            ->get();

        $annonces = Annonce::with('auteur', 'module')
            ->where(fn ($q) => $q->whereIn('promotion_id', $promotion ? [$promotion->id] : [])->orWhereIn('module_id', $idsModules))
            ->latest()
            ->limit(5)
            ->get();

        $modulesPreparation = Module::preparation()
            ->where('publie', true)
            ->with(['lecons', 'enseignant'])
            ->withCount(['quiz' => fn ($q) => $q->where('publie', true)])
            ->orderBy('titre')
            ->get();

        $leconsTerminees = $user->leconsTerminees()->pluck('lecons.id');

        return view('formation.index', compact(
            'promotion', 'modulesFormation', 'seances', 'devoirs', 'annonces', 'modulesPreparation', 'leconsTerminees'
        ));
    }
}
