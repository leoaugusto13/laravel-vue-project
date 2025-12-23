<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Regional;
use Illuminate\Http\Request;

use App\Models\SystemLog;
use Illuminate\Support\Facades\Auth;

class RegionalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Regional::orderBy('name')->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:regionals,name',
            'state_id' => 'required|exists:states,id',
            'active' => 'boolean'
        ], [
            'name.unique' => 'A regional já está cadastrada.'
        ]);

        $regional = Regional::create($validated);

        SystemLog::create([
            'user_id' => Auth::id(),
            'action' => 'CREATE_REGIONAL',
            'description' => "Usuário criou a regional {$regional->name} ({$regional->id})",
            'ip_address' => $request->ip()
        ]);

        return response()->json($regional, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Regional $regional)
    {
        return response()->json($regional);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Regional $regional)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255|unique:regionals,name,' . $regional->id,
            'state_id' => 'sometimes|required|exists:states,id',
            'active' => 'boolean'
        ], [
            'name.unique' => 'A regional já está cadastrada.'
        ]);

        $oldName = $regional->name;
        $regional->update($validated);

        SystemLog::create([
            'user_id' => Auth::id(),
            'action' => 'UPDATE_REGIONAL',
            'description' => "Usuário atualizou a regional {$oldName} para {$regional->name} (ID: {$regional->id})",
            'ip_address' => $request->ip()
        ]);

        return response()->json($regional);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Regional $regional)
    {
        $regionalName = $regional->name;
        $regional->delete();

        SystemLog::create([
            'user_id' => Auth::id(),
            'action' => 'DELETE_REGIONAL',
            'description' => "Usuário excluiu a regional {$regionalName}",
            'ip_address' => $request->ip()
        ]);

        return response()->json(null, 204);
    }
}
