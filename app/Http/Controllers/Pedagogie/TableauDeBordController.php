<?php

namespace App\Http\Controllers\Pedagogie;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\Promotion;
use App\Models\RenduDevoir;
use App\Models\Seance;
use Illuminate\Database\Eloquent\Builder;

class TableauDeBordController extends Controller
{
    public function __invoke()
    {
        $user = request()->user();
        $estEnseignant = $user->role === Role::Enseignant;
        $mesModules = fn (Builder $q) => $q->when($estEnseignant, fn ($q) => $q->where('enseignant_id', $user->id));

        $modules = Module::with('promotion', 'enseignant')
            ->withCount(['lecons', 'quiz', 'devoirs'])
            ->tap($mesModules)
            ->orderBy('public')
            ->orderBy('titre')
            ->get();

        $rendusACorriger = RenduDevoir::whereNull('corrige_le')
            ->whereHas('devoir.module', $mesModules)
            ->with('devoir.module', 'eleve')
            ->oldest('rendu_le')
            ->get();

        $seances = Seance::with('module', 'promotion')
            ->where('fin', '>=', now())
            ->when($estEnseignant, fn ($q) => $q->whereHas('module', $mesModules))
            ->orderBy('debut')
            ->limit(6)
            ->get();

        $promotions = $estEnseignant ? collect() : Promotion::withCount('eleves')->where('statut', 'en_cours')->get();

        return view('pedagogie.dashboard', compact('modules', 'rendusACorriger', 'seances', 'promotions'));
    }
}
