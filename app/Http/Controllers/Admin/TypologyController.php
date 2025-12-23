<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TypologyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return \App\Models\Typology::all();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'description' => 'required|string|max:255',
            'status' => 'required|in:active,inactive'
        ]);

        return \App\Models\Typology::create($validated);
    }

    public function show(string $id)
    {
        return \App\Models\Typology::findOrFail($id);
    }

    public function update(Request $request, string $id)
    {
        $typology = \App\Models\Typology::findOrFail($id);
        
        $validated = $request->validate([
            'description' => 'required|string|max:255',
            'status' => 'required|in:active,inactive'
        ]);

        $typology->update($validated);
        return $typology;
    }

    public function destroy(string $id)
    {
        \App\Models\Typology::findOrFail($id)->delete();
        return response()->noContent();
    }
}
