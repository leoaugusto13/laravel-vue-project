<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Directorate;

class DirectorateController extends Controller
{
    public function index()
    {
        return Directorate::all();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'description' => 'nullable|string',
            'acronym' => 'required|string|max:10|unique:directorates',
            'status' => 'required|in:active,inactive',
        ]);

        $directorate = Directorate::create($validated);
        
        \App\Services\LoggerService::log('DIRECTORATE_CREATE', "Diretoria '{$directorate->acronym}' criada");

        return response()->json($directorate, 201);
    }

    public function show(Directorate $directorate)
    {
        return $directorate;
    }

    public function update(Request $request, Directorate $directorate)
    {
        $validated = $request->validate([
            'description' => 'nullable|string',
            'acronym' => 'required|string|max:10|unique:directorates,acronym,' . $directorate->id,
            'status' => 'required|in:active,inactive',
        ]);

        $directorate->update($validated);
        
        \App\Services\LoggerService::log('DIRECTORATE_UPDATE', "Diretoria '{$directorate->acronym}' atualizada");

        return response()->json($directorate);
    }

    public function destroy(Directorate $directorate)
    {
        $name = $directorate->acronym;
        $directorate->delete();
        
        \App\Services\LoggerService::log('DIRECTORATE_DELETE', "Diretoria '{$name}' excluída");

        return response()->json(null, 204);
    }
}
