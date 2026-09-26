<?php

namespace App\Console\Commands;

use App\Enums\StatutConcours;
use App\Models\Concours;
use Illuminate\Console\Command;

/**
 * Automatise la transition Ouvert → Clôturé une fois la date de clôture
 * dépassée (Partie C : automatisation du cycle de vie des concours).
 */
class ClotureConcoursExpires extends Command
{
    protected $signature = 'app:cloture-concours-expires';

    protected $description = "Clôture automatiquement les concours ouverts dont la date de clôture est dépassée";

    public function handle(): void
    {
        $concours = Concours::where('statut', StatutConcours::Ouvert)
            ->whereDate('date_cloture', '<', now())
            ->get();

        foreach ($concours as $c) {
            $c->update(['statut' => StatutConcours::Cloture]);
            $this->info("Concours #{$c->id} ({$c->nom}) clôturé automatiquement.");
        }

        if ($concours->isEmpty()) {
            $this->info('Aucun concours à clôturer.');
        }
    }
}
