<?php

namespace App\Http\Controllers;

use App\Enums\StatutConcours;
use App\Http\Requests\StoreCandidatureRequest;
use App\Models\Candidature;
use App\Models\Concours;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class CandidatureController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $candidatures = request()->user()
            ->candidatures()
            ->with('concours')
            ->latest()
            ->get();

        return view('candidatures.index', compact('candidatures'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $concoursOuverts = Concours::where('statut', StatutConcours::Ouvert)->get();

        return view('candidatures.create', compact('concoursOuverts'));
    }

    /**
     * Store a newly created resource in storage.
     */
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

        return redirect()
            ->route('candidatures.show', $candidature)
            ->with('status', 'Candidature enregistrée avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Candidature $candidature)
    {
        $this->authorizeCandidat($candidature);

        $candidature->load('concours', 'documents', 'paiements.signalements');

        return view('candidatures.show', compact('candidature'));
    }

    public function fiche(Candidature $candidature)
    {
        $this->authorizeCandidat($candidature);

        $candidature->load('concours', 'candidat');

        $pdf = Pdf::loadView('pdf.fiche-candidature', compact('candidature'));

        return $pdf->stream("fiche-candidature-{$candidature->id}.pdf");
    }

    private function authorizeCandidat(Candidature $candidature): void
    {
        abort_unless($candidature->user_id === request()->user()->id, 403);
    }
}
