<?php

namespace Database\Seeders;

use App\Enums\StatutConcours;
use App\Models\Concours;
use Illuminate\Database\Seeder;

/**
 * Sessions de démonstration calquées sur les concours directs de l'IPNETP
 * (un concours par corps, diplômes admis et conditions d'âge du communiqué).
 * Sans Faker et idempotent : un concours dont le code existe déjà n'est pas
 * modifié, ce qui permet de le lancer en production sans risque.
 */
class ConcoursIpnetpSeeder extends Seeder
{
    public function run(): void
    {
        $annee = now()->year;
        $commun = [
            'age_min' => config('ipnetp.age_min'),
            'age_max' => config('ipnetp.age_max'),
            'frais_inscription' => config('ipnetp.frais_inscription'),
            'frais_visite_medicale' => 10000,
            'seuil_admission' => 10,
            'date_ouverture' => now()->subDays(3),
            'date_cloture' => now()->addWeeks(5),
            'date_concours' => now()->addWeeks(9),
            'statut' => StatutConcours::Ouvert,
        ];

        $sessions = [
            [
                'code' => "CAPPL-INFO-{$annee}",
                'nom' => "Concours direct CAP/PL {$annee} — Informatique de gestion",
                'cycle' => 'CAP/PL',
                'filiere' => 'Tertiaire',
                'diplome_requis' => "Diplôme d'ingénieur, Master",
                'description' => "Recrutement d'élèves-professeurs de lycée professionnel en informatique de gestion. Épreuves : composition française (coef. 3), spécialité 4 h (coef. 5), entretien oral (coef. 1).",
            ],
            [
                'code' => "CAPPL-GCB-{$annee}",
                'nom' => "Concours direct CAP/PL {$annee} — Génie civil bâtiment",
                'cycle' => 'CAP/PL',
                'filiere' => 'Industriel',
                'diplome_requis' => "Diplôme d'ingénieur, Master",
                'description' => "Recrutement d'élèves-professeurs de lycée professionnel en génie civil. Épreuve de spécialité de 4 h.",
            ],
            [
                'code' => "CAPPC-TLT-{$annee}",
                'nom' => "Concours direct CAP/PC {$annee} — Transport, logistique et transit",
                'cycle' => 'CAP/PC',
                'filiere' => 'Tertiaire',
                'diplome_requis' => 'Licence professionnelle, BTS, DUT',
                'description' => "Recrutement d'élèves-professeurs de collège en transport, logistique et transit. Épreuve de spécialité de 4 h.",
            ],
            [
                'code' => "CAPPC-ELEC-{$annee}",
                'nom' => "Concours direct CAP/PC {$annee} — Électrotechnique",
                'cycle' => 'CAP/PC',
                'filiere' => 'Industriel',
                'diplome_requis' => 'Licence professionnelle, BTS, DUT',
                'description' => "Recrutement d'élèves-professeurs de collège en électrotechnique.",
            ],
            [
                'code' => "CAPIFPB-MECA-{$annee}",
                'nom' => "Concours direct CAP/IFPB {$annee} — Mécanique automobile",
                'cycle' => 'CAP/IFPB',
                'filiere' => 'Industriel',
                'diplome_requis' => 'Brevet de Technicien (BT), Baccalauréat',
                'description' => "Recrutement d'instructeurs de formation professionnelle de base. Épreuve de spécialité de 3 h.",
            ],
            [
                'code' => "CAPIAFPB-AGRO-{$annee}",
                'nom' => "Concours direct CAP/IAFPB {$annee} — Agroéquipement",
                'cycle' => 'CAP/IAFPB',
                'filiere' => 'Agricole',
                'diplome_requis' => "Certificat d'Aptitude Professionnelle (CAP), BEPC",
                'description' => "Recrutement d'instructeurs adjoints. Une expérience professionnelle dans la spécialité est appréciée.",
            ],
        ];

        foreach ($sessions as $session) {
            Concours::firstOrCreate(['code' => $session['code']], $session + $commun);
        }
    }
}
