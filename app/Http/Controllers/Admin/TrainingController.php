<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Training;

class TrainingController extends Controller
{
    public function index()
    {
        return Training::with('directorate')->orderBy('year', 'desc')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
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
            'directorate_id' => 'nullable|exists:directorates,id'
        ]);

        $training = Training::create($validated);
        return response()->json($training, 201);
    }

    public function update(Request $request, Training $training)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
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
            'directorate_id' => 'nullable|exists:directorates,id'
        ]);

        $training->update($validated);
        return response()->json($training);
    }

    public function destroy(Training $training)
    {
        $training->delete();
        return response()->json(null, 204);
    }
}
