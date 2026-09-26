<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCandidatureRequest;
use App\Models\Candidature;
use Illuminate\Support\Facades\DB;

class CandidatureController extends Controller
{
    public function index()
    {
        $candidatures = request()->user()
            ->candidatures()
            ->with('concours', 'documents', 'paiements')
            ->latest()
            ->get();

        return response()->json($candidatures);
    }

    public function store(StoreCandidatureRequest $request)
    {
        $candidature = DB::transaction(function () use ($request) {
            return Candidature::create([
                'user_id' => $request->user()->id,
                'concours_id' => $request->validated('concours_id'),
                'diplome_candidat' => $request->validated('diplome_candidat'),
                'statut' => 'en_attente',
                'date_soumission' => now(),
            ]);
        });

        return response()->json($candidature->load('concours'), 201);
    }

    public function show(Candidature $candidature)
    {
        abort_unless($candidature->user_id === request()->user()->id, 403);

        return response()->json($candidature->load('concours', 'documents', 'paiements.signalements'));
    }
}
