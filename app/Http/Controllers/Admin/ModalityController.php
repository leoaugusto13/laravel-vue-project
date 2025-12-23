<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

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
        
        \App\Services\LoggerService::log('MODALITY_CREATE', "Modalidade '{$modality->description}' criada");

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
        
        \App\Services\LoggerService::log('MODALITY_UPDATE', "Modalidade '{$modality->description}' atualizada");

        return response()->json($modality);
    }

    public function destroy(Modality $modality)
    {
        $name = $modality->description;
        $modality->delete();
        
        \App\Services\LoggerService::log('MODALITY_DELETE', "Modalidade '{$name}' excluída");

        return response()->json(null, 204);
    }
}
