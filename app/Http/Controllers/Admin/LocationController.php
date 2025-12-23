<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Location;
use App\Models\Modality;

class LocationController extends Controller
{
    public function indexAll()
    {
        return Location::with('modalities')->get();
    }

    // index(Modality $modality) removed or deprecated, assuming routes will be cleaned up.

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'modalities' => 'array',
            'modalities.*' => 'exists:modalities,id'
        ]);

        $location = Location::create(['name' => $validated['name']]);
        if (isset($validated['modalities'])) {
            $location->modalities()->sync($validated['modalities']);
        }
        
        \App\Services\LoggerService::log('LOCATION_CREATE', "Local '{$location->name}' criado");
        
        return response()->json($location->load('modalities'), 201);
    }

    public function update(Request $request, Location $location)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'modalities' => 'array',
            'modalities.*' => 'exists:modalities,id'
        ]);

        $location->update(['name' => $validated['name']]);
        if (isset($validated['modalities'])) {
            $location->modalities()->sync($validated['modalities']);
        }

        \App\Services\LoggerService::log('LOCATION_UPDATE', "Local '{$location->name}' atualizado");

        return response()->json($location->load('modalities'));
    }

    public function destroy(Request $request, Location $location)
    {
        $name = $location->name;
        $location->delete();
        
        \App\Services\LoggerService::log('LOCATION_DELETE', "Local '{$name}' excluído");

        return response()->json(null, 204);
    }
}
