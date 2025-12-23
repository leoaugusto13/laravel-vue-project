<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\State;
use Illuminate\Http\Request;

use App\Models\SystemLog;
use Illuminate\Support\Facades\Auth;

class StateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(State::orderBy('uf')->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'uf' => 'required|string|size:2|unique:states,uf',
            'active' => 'boolean'
        ]);

        $state = State::create($validated);

        SystemLog::create([
            'user_id' => Auth::id(),
            'action' => 'CREATE_STATE',
            'description' => "Usuário criou o estado {$state->name} ({$state->uf})",
            'ip_address' => $request->ip()
        ]);

        return response()->json($state, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(State $state)
    {
        return response()->json($state);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, State $state)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'uf' => 'sometimes|required|string|size:2|unique:states,uf,' . $state->id,
            'active' => 'boolean'
        ]);

        $oldName = $state->name;
        $state->update($validated);

        SystemLog::create([
            'user_id' => Auth::id(),
            'action' => 'UPDATE_STATE',
            'description' => "Usuário atualizou o estado {$oldName} para {$state->name} (UF: {$state->uf}) (ID: {$state->id})",
            'ip_address' => $request->ip()
        ]);

        return response()->json($state);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, State $state)
    {
        $stateName = $state->name;
        $state->delete();

        SystemLog::create([
            'user_id' => Auth::id(),
            'action' => 'DELETE_STATE',
            'description' => "Usuário excluiu o estado {$stateName}",
            'ip_address' => $request->ip()
        ]);

        return response()->json(null, 204);
    }
}
