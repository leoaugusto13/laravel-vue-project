<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Place;
use App\Models\Modality;

class PlaceController extends Controller
{
    public function index(Modality $modality)
    {
        return $modality->places()->with(['city', 'regional'])->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'modality_id' => 'required|exists:modalities,id',
            'city_id' => 'nullable|exists:cities,id',
            'regional_id' => 'nullable|exists:regionals,id',
            'uaitec' => 'boolean',
            'ead' => 'boolean',
        ]);

        // Simple validation to ensure at least one place type is selected? 
        // For now allowing any combination as per flexible requirements.

        $place = Place::create($validated);
        return response()->json($place->load(['city', 'regional']), 201);
    }

    public function destroy(Place $place)
    {
        $place->delete();
        return response()->json(null, 204);
    }
}
