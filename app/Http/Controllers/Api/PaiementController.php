<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePaiementRequest;
use App\Models\Candidature;
use App\Services\PaiementEchoueException;
use App\Services\PaiementService;

class PaiementController extends Controller
{
    public function __construct(
        private readonly PaiementService $paiementService,
    ) {}

    public function store(StorePaiementRequest $request, Candidature $candidature)
    {
        abort_unless($candidature->user_id === $request->user()->id, 403);

        $type = $request->validated('type');
        $montant = match ($type) {
            'inscription' => $candidature->concours->frais_inscription,
            'visite_medicale' => $candidature->concours->frais_visite_medicale,
        };

        try {
            $paiement = $this->paiementService->payer(
                $candidature,
                $type,
                (float) $montant,
                $request->validated('numero_telephone'),
            );
        } catch (PaiementEchoueException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json($paiement, 201);
    }
}
