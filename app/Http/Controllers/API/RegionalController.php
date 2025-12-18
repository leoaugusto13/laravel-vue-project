<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Regional;
use Illuminate\Http\Request;

class RegionalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Regional::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'state_id' => 'required|exists:states,id',
            'active' => 'boolean'
        ]);

        $regional = Regional::create($validated);

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
            'name' => 'sometimes|required|string|max:255',
            'state_id' => 'sometimes|required|exists:states,id',
            'active' => 'boolean'
        ]);

        $regional->update($validated);

        return response()->json($regional);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Regional $regional)
    {
        $regional->delete();
        return response()->json(null, 204);
    }
}
