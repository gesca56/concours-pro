<?php

namespace App\Http\Controllers\Administration;

use App\Enums\StatutConcours;
use App\Http\Controllers\Controller;
use App\Models\Concours;
use Illuminate\Support\Facades\DB;

class DeliberationController extends Controller
{
    public function __invoke(Concours $concours)
    {
        abort_unless(
            in_array($concours->statut, [StatutConcours::Cloture, StatutConcours::Deliberation], true),
            422,
            'Le concours doit être clôturé avant de lancer la délibération.'
        );

        $candidatures = $concours->candidatures()
            ->whereNotNull('note_totale')
            ->whereIn('statut', ['validee'])
            ->get();

        abort_if($candidatures->isEmpty(), 422, 'Aucune copie notée pour ce concours.');

        DB::transaction(function () use ($concours, $candidatures) {
            foreach ($candidatures as $candidature) {
                $candidature->update([
                    'statut' => $candidature->note_totale >= $concours->seuil_admission ? 'admise' : 'recalee',
                ]);
            }

            $concours->update(['statut' => StatutConcours::Termine]);
        });

        return back()->with('status', "Délibération lancée : {$candidatures->count()} candidature(s) traitée(s).");
    }
}
