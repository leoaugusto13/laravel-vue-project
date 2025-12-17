<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Modality;

class ModalityController extends Controller
{
    public function index()
    {
        return Modality::all();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'description' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);

        $modality = Modality::create($validated);
        return response()->json($modality, 201);
    }

    public function show(Modality $modality)
    {
        return $modality;
    }

    public function update(Request $request, Modality $modality)
    {
        $validated = $request->validate([
            'description' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);

        $modality->update($validated);
        return response()->json($modality);
    }

    public function destroy(Modality $modality)
    {
        $modality->delete();
        return response()->json(null, 204);
    }
}
