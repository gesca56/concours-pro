<?php

namespace App\Http\Controllers\Administration;

use App\Enums\StatutConcours;
use App\Http\Controllers\Controller;
use App\Models\Concours;
use Illuminate\Support\Str;

class AnonymatController extends Controller
{
    public function __invoke(Concours $concours)
    {
        abort_unless(
            $concours->statut === StatutConcours::Cloture,
            422,
            'Le concours doit être clôturé avant l\'attribution des numéros d\'anonymat.'
        );

        $candidatures = $concours->candidatures()
            ->where('statut', 'validee')
            ->whereNull('numero_anonymat')
            ->get();

        foreach ($candidatures as $candidature) {
            $candidature->update(['numero_anonymat' => strtoupper('ANO-'.Str::random(8))]);
        }

        return back()->with('status', "{$candidatures->count()} numéro(s) d'anonymat attribué(s).");
    }
}
