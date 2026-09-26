<?php

namespace App\Console\Commands;

use App\Enums\StatutConcours;
use App\Models\Concours;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

/**
 * Attribue un numéro d'anonymat à chaque candidature éligible d'un concours
 * clôturé, avant la phase de correction (Partie E : saisie anonyme des notes).
 */
class AttribuerNumerosAnonymat extends Command
{
    protected $signature = 'app:attribuer-numeros-anonymat {concours : ID du concours}';

    protected $description = "Attribue les numéros d'anonymat aux candidatures éligibles d'un concours clôturé";

    public function handle(): int
    {
        $concours = Concours::find($this->argument('concours'));

        if ($concours === null) {
            $this->error('Concours introuvable.');

            return self::FAILURE;
        }

        if ($concours->statut !== StatutConcours::Cloture) {
            $this->error('Le concours doit être clôturé avant l\'attribution des numéros d\'anonymat.');

            return self::FAILURE;
        }

        $candidatures = $concours->candidatures()
            ->where('statut', 'eligible')
            ->whereNull('numero_anonymat')
            ->get();

        foreach ($candidatures as $candidature) {
            $candidature->update([
                'numero_anonymat' => strtoupper('ANO-'.Str::random(8)),
            ]);
        }

        $this->info("{$candidatures->count()} numéro(s) d'anonymat attribué(s).");

        return self::SUCCESS;
    }
}
