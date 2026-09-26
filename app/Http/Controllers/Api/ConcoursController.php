<?php

namespace App\Http\Controllers\Api;

use App\Enums\StatutConcours;
use App\Http\Controllers\Controller;
use App\Models\Concours;

class ConcoursController extends Controller
{
    public function index()
    {
        return response()->json(
            Concours::where('statut', StatutConcours::Ouvert)->get()
        );
    }
}
