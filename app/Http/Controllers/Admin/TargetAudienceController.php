<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TargetAudience;
use Illuminate\Http\Request;

class TargetAudienceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $audiences = TargetAudience::latest()->get();
        return response()->json($audiences);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'boolean'
        ]);

        $audience = TargetAudience::create($validated);

        \App\Services\LoggerService::log('TARGET_AUDIENCE_CREATE', "Público Alvo '{$audience->name}' criado");

        return response()->json($audience, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TargetAudience $targetAudience)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'boolean'
        ]);

        $targetAudience->update($validated);

        \App\Services\LoggerService::log('TARGET_AUDIENCE_UPDATE', "Público Alvo '{$targetAudience->name}' atualizado");

        return response()->json($targetAudience);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TargetAudience $targetAudience)
    {
        $name = $targetAudience->name;
        $targetAudience->delete();
        
        \App\Services\LoggerService::log('TARGET_AUDIENCE_DELETE', "Público Alvo '{$name}' excluído");

        return response()->json(null, 204);
    }
}
