<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Crée les comptes de démonstration (un par rôle) sans dépendre de Faker,
 * afin de pouvoir tourner en production (composer install --no-dev).
 * Idempotent : un compte déjà existant n'est pas modifié.
 */
class ComptesDemoSeeder extends Seeder
{
    public function run(): void
    {
        $comptes = [
            ['role' => Role::Candidat, 'name' => 'Candidat Démo', 'email' => 'candidat@sigec.test', 'date_naissance' => now()->subYears(20)],
            ['role' => Role::Receptionniste, 'name' => 'Réceptionniste Démo', 'email' => 'receptionniste@sigec.test'],
            ['role' => Role::Medecin, 'name' => 'Médecin Démo', 'email' => 'medecin@sigec.test'],
            ['role' => Role::Enseignant, 'name' => 'Enseignant Démo', 'email' => 'enseignant@sigec.test'],
            ['role' => Role::Administration, 'name' => 'Administration Démo', 'email' => 'admin@sigec.test'],
        ];

        foreach ($comptes as $compte) {
            $user = User::firstOrCreate(
                ['email' => $compte['email']],
                array_merge($compte, ['password' => 'password'])
            );

            if (! $user->email_verified_at) {
                $user->forceFill(['email_verified_at' => now()])->save();
            }
        }
    }
}
