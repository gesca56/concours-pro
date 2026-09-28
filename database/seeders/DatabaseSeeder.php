<?php

namespace Database\Seeders;

use App\Enums\StatutConcours;
use App\Models\Concours;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(ComptesDemoSeeder::class);

        Concours::create([
            'nom' => 'Concours CAP/PL 2026 — Secrétariat Bureautique',
            'code' => 'CAPPL-SEC-2026',
            'cycle' => 'CAP/PL',
            'filiere' => 'Tertiaire',
            'diplome_requis' => 'BEPC',
            'age_min' => 17,
            'age_max' => 25,
            'statut' => StatutConcours::Ouvert,
            'date_ouverture' => now()->subDays(5),
            'date_cloture' => now()->addMonth(),
            'date_concours' => now()->addMonths(2),
        ]);

        Concours::create([
            'nom' => 'Concours CAP/PC 2026 — Électrotechnique',
            'code' => 'CAPPC-ELEC-2026',
            'cycle' => 'CAP/PC',
            'filiere' => 'Industriel',
            'diplome_requis' => 'CAP',
            'age_min' => 18,
            'age_max' => 30,
            'statut' => StatutConcours::Ouvert,
            'date_ouverture' => now()->subDays(2),
            'date_cloture' => now()->addWeeks(3),
            'date_concours' => now()->addMonths(2),
        ]);

        Concours::create([
            'nom' => 'Concours CAP/IFPB 2026 — Agroéquipement',
            'code' => 'CAPIFPB-AGRO-2026',
            'cycle' => 'CAP/IFPB',
            'filiere' => 'Agricole',
            'diplome_requis' => 'BEPC',
            'age_min' => 17,
            'age_max' => 28,
            'statut' => StatutConcours::Brouillon,
            'date_ouverture' => now()->addWeek(),
            'date_cloture' => now()->addMonths(2),
        ]);
    }
}
