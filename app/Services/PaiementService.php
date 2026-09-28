<?php

namespace App\Services;

use App\Models\Candidature;
use App\Models\Paiement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PaiementService
{
    public function __construct(
        private readonly MobileMoneyGateway $gateway,
    ) {}

    /**
     * Traite un paiement (inscription ou visite médicale). La demande auprès
     * de la passerelle Mobile Money se fait hors transaction (opération
     * externe non annulable) ; chaque écriture qui en découle est ensuite
     * appliquée atomiquement pour ne jamais laisser la base dans un état
     * incohérent en cas de coupure réseau côté serveur.
     */
    public function payer(Candidature $candidature, string $type, float $montant, string $numeroTelephone): Paiement
    {
        if ($candidature->paiements()->where('type', $type)->where('statut', 'valide')->exists()) {
            throw new PaiementEchoueException('Ce paiement a déjà été effectué pour cette candidature.');
        }

        $paiement = Paiement::create([
            'candidature_id' => $candidature->id,
            'type' => $type,
            'montant' => $montant,
            'mode_paiement' => 'mobile_money',
            'reference_transaction' => 'PENDING-'.uniqid(),
            'statut' => 'en_attente',
        ]);

        try {
            $reference = $this->gateway->charger($numeroTelephone, $montant);
        } catch (PaiementEchoueException $e) {
            $paiement->update(['statut' => 'echoue']);

            throw $e;
        }

        DB::transaction(function () use ($paiement, $candidature, $reference) {
            $paiement->update([
                'reference_transaction' => $reference,
                'statut' => 'valide',
                'date_paiement' => now(),
            ]);

            $paiementsValides = $candidature->paiements()
                ->where('statut', 'valide')
                ->pluck('type');

            if ($paiementsValides->contains('inscription') && $paiementsValides->contains('visite_medicale')) {
                $candidature->update([
                    'statut' => 'eligible',
                    'jeton_convocation' => $candidature->jeton_convocation ?? (string) Str::uuid(),
                ]);
            }
        });

        return $paiement->fresh();
    }
}
