<?php

namespace App\Services;

use Illuminate\Support\Str;

/**
 * Passerelle Mobile Money simulée pour l'environnement de développement.
 * Un numéro de test terminant par "0000" simule un échec de transaction,
 * ce qui permet de tester le rollback des paiements en local.
 */
class SimulatedMobileMoneyGateway implements MobileMoneyGateway
{
    public function charger(string $numeroTelephone, float $montant): string
    {
        if (str_ends_with($numeroTelephone, '0000')) {
            throw new PaiementEchoueException('La transaction Mobile Money a été refusée par l\'opérateur.');
        }

        return 'MM-'.strtoupper(Str::random(10));
    }
}
