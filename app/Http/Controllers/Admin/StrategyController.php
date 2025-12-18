<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Strategy;
use Illuminate\Http\Request;
use App\Services\LoggerService;

class StrategyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Strategy::latest()->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'description' => 'required|string',
            'status' => 'required|in:active,inactive'
        ]);

        $strategy = Strategy::create($validated);

        LoggerService::log('STRATEGY_CREATE', "Strategy created with description: " . substr($strategy->description, 0, 50));

        return response()->json($strategy, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Strategy $strategy)
    {
        return $strategy;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Strategy $strategy)
    {
        $validated = $request->validate([
            'description' => 'required|string',
            'status' => 'required|in:active,inactive'
        ]);

        $strategy->update($validated);

        LoggerService::log('STRATEGY_UPDATE', "Strategy updated (ID: {$strategy->id})");

        return response()->json($strategy);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Strategy $strategy)
    {
        $id = $strategy->id;
        $strategy->delete();

        LoggerService::log('STRATEGY_DELETE', "Strategy deleted (ID: {$id})");

        return response()->json(null, 204);
    }
}
