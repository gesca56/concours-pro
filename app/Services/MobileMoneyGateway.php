<?php

namespace App\Services;

interface MobileMoneyGateway
{
    /**
     * Initie une transaction Mobile Money et retourne sa référence si acceptée.
     *
     * @throws \App\Services\PaiementEchoueException
     */
    public function charger(string $numeroTelephone, float $montant): string;
}
