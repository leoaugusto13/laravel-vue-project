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
            'year' => 'required|integer|min:2000|max:2100',
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
            'year' => 'required|integer|min:2000|max:2100',
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
