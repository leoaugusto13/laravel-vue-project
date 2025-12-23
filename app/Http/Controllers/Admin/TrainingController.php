<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Training;

class TrainingController extends Controller
{
    public function index()
    {
        return Training::with(['directorate', 'location', 'modality', 'strategies', 'trainingType', 'targetAudience'])->orderBy('year', 'desc')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'action_name' => 'required|string|max:255',
            'year' => [
                'required',
                'integer',
                'exists:years,year',
                function ($attribute, $value, $fail) {
                    $year = \App\Models\Year::where('year', $value)->first();
                    if ($year && $year->is_closed) {
                        $fail('Não é possível criar capacitações para um ano encerrado.');
                    }
                },
            ],
            'status' => 'required|in:active,inactive',
            'directorate_id' => 'nullable|exists:directorates,id',
            'location_id' => 'nullable|exists:locations,id',
            'modality_id' => 'nullable|exists:modalities,id',
            'strategies' => 'array',
            'strategies.*' => 'exists:strategies,id',
            'training_type_id' => 'nullable|exists:training_types,id',
            'target_audience_id' => 'nullable|exists:target_audiences,id',
            'start_date' => 'nullable|date',
            'workload' => 'nullable|string|max:255',
            'venue' => 'nullable|string|max:255',
        ]);

        $training = Training::create($validated);
        
        if (isset($validated['strategies'])) {
            $training->strategies()->sync($validated['strategies']);
        }
        
        \App\Services\LoggerService::log('TRAINING_CREATE', "Capacitação '{$training->action_name}' criada");

        // Reload to include relations
        return response()->json($training->load(['directorate', 'location', 'modality', 'strategies', 'trainingType', 'targetAudience']), 201);
    }

    public function update(Request $request, Training $training)
    {
        $validated = $request->validate([
            'action_name' => 'required|string|max:255',
            'year' => [
                'required',
                'integer',
                'exists:years,year',
                function ($attribute, $value, $fail) {
                    $year = \App\Models\Year::where('year', $value)->first();
                    if ($year && $year->is_closed) {
                        $fail('Não é possível mover capacitações para um ano encerrado.');
                    }
                },
            ],
            'status' => 'required|in:active,inactive',
            'directorate_id' => 'nullable|exists:directorates,id',
            'location_id' => 'nullable|exists:locations,id',
            'modality_id' => 'nullable|exists:modalities,id',
            'training_type_id' => 'nullable|exists:training_types,id',
            'target_audience_id' => 'nullable|exists:target_audiences,id',
            'start_date' => 'nullable|date',
            'workload' => 'nullable|string|max:255',
            'venue' => 'nullable|string|max:255',
            'strategies' => 'array',
            'strategies.*' => 'exists:strategies,id',
        ]);

        $training->update($validated);

        if (isset($validated['strategies'])) {
            $training->strategies()->sync($validated['strategies']);
        }
        
        \App\Services\LoggerService::log('TRAINING_UPDATE', "Capacitação '{$training->action_name}' atualizada");

        return response()->json($training->load(['directorate', 'location', 'modality', 'strategies', 'trainingType', 'targetAudience']));
    }

    public function destroy(Training $training)
    {
        $name = $training->action_name;
        $training->delete();
        
        \App\Services\LoggerService::log('TRAINING_DELETE', "Capacitação '{$name}' excluída");

        return response()->json(null, 204);
    }
}
