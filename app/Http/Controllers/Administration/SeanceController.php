<?php

namespace App\Http\Controllers\Administration;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use App\Models\Seance;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class SeanceController extends Controller
{
    /**
     * Ajoute un créneau à l'emploi du temps, éventuellement répété chaque semaine.
     */
    public function store(Request $request, Promotion $promotion)
    {
        $donnees = $request->validate([
            'module_id' => ['nullable', Rule::exists('modules', 'id')->where('promotion_id', $promotion->id)],
            'jour' => ['required', 'date'],
            'heure_debut' => ['required', 'date_format:H:i'],
            'heure_fin' => ['required', 'date_format:H:i', 'after:heure_debut'],
            'type' => ['required', Rule::in(array_keys(Seance::TYPES))],
            'salle' => ['nullable', 'string', 'max:100'],
            'lien_visio' => ['nullable', 'url:http,https', 'max:255'],
            'observations' => ['nullable', 'string', 'max:255'],
            'repetitions' => ['nullable', 'integer', 'between:1,20'],
        ], [
            'heure_fin.after' => "L'heure de fin doit suivre l'heure de début.",
        ]);

        $jour = Carbon::parse($donnees['jour']);
        $semaines = $donnees['repetitions'] ?? 1;

        for ($i = 0; $i < $semaines; $i++) {
            $date = $jour->copy()->addWeeks($i)->toDateString();
            $promotion->seances()->create([
                'module_id' => $donnees['module_id'] ?? null,
                'debut' => "$date {$donnees['heure_debut']}",
                'fin' => "$date {$donnees['heure_fin']}",
                'type' => $donnees['type'],
                'salle' => $donnees['salle'] ?? null,
                'lien_visio' => $donnees['lien_visio'] ?? null,
                'observations' => $donnees['observations'] ?? null,
            ]);
        }

        return back()->with('status', $semaines > 1 ? "{$semaines} séances ajoutées à l'emploi du temps." : "Séance ajoutée à l'emploi du temps.");
    }

    public function destroy(Seance $seance)
    {
        $seance->delete();

        return back()->with('status', 'Séance retirée.');
    }
}
