<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\State;
use Illuminate\Http\Request;

class StateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(State::all());
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

        $state->update($validated);

        return response()->json($state);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(State $state)
    {
        $state->delete();
        return response()->json(null, 204);
    }
}
