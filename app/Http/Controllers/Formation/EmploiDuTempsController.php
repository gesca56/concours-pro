<?php

namespace App\Http\Controllers\Formation;

use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;

class EmploiDuTempsController extends Controller
{
    /**
     * Emploi du temps d'une semaine (?semaine=-1, 0, 1… par rapport à la semaine en cours).
     */
    public function __invoke()
    {
        $promotion = request()->user()->promotionActive();
        abort_unless($promotion, 404, "L'emploi du temps est réservé aux élèves-professeurs inscrits dans une promotion.");

        $decalage = max(-52, min(52, request()->integer('semaine')));
        $lundi = Carbon::now()->startOfWeek()->addWeeks($decalage);

        $seances = $promotion->seances()
            ->with('module.enseignant')
            ->whereBetween('debut', [$lundi, $lundi->copy()->endOfWeek()])
            ->get()
            ->groupBy(fn ($s) => $s->debut->toDateString());

        return view('formation.emploi-du-temps', compact('promotion', 'seances', 'lundi', 'decalage'));
    }
}
