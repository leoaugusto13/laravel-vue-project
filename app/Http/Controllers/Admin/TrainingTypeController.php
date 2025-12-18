<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TrainingType;
use Illuminate\Http\Request;

class TrainingTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return TrainingType::latest()->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive'
        ]);

        $trainingType = TrainingType::create($validated);

        \App\Services\LoggerService::log('TRAINING_TYPE_CREATE', "Training Type '{$trainingType->name}' created");

        return response()->json($trainingType, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(TrainingType $trainingType)
    {
        return $trainingType;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TrainingType $trainingType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive'
        ]);

        $trainingType->update($validated);

        \App\Services\LoggerService::log('TRAINING_TYPE_UPDATE', "Training Type '{$trainingType->name}' updated");

        return response()->json($trainingType);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TrainingType $trainingType)
    {
        $name = $trainingType->name;
        $trainingType->delete();

        \App\Services\LoggerService::log('TRAINING_TYPE_DELETE', "Training Type '{$name}' deleted");

        return response()->json(null, 204);
    }
}
