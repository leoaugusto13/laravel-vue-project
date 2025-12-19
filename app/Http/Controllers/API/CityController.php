<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\City;
use Illuminate\Http\Request;

use App\Models\SystemLog;
use Illuminate\Support\Facades\Auth;

class CityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(City::all());
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

        $city = City::create($validated);

        SystemLog::create([
            'user_id' => Auth::id(),
            'action' => 'CREATE_CITY',
            'description' => "Usuário criou o município {$city->name} ({$city->id})",
            'ip_address' => $request->ip()
        ]);

        return response()->json($city, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(City $city)
    {
        return response()->json($city);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, City $city)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'state_id' => 'sometimes|required|exists:states,id',
            'active' => 'boolean'
        ]);

        $oldName = $city->name;
        $city->update($validated);

        SystemLog::create([
            'user_id' => Auth::id(),
            'action' => 'UPDATE_CITY',
            'description' => "Usuário atualizou o município {$oldName} para {$city->name} (ID: {$city->id})",
            'ip_address' => $request->ip()
        ]);

        return response()->json($city);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, City $city)
    {
        $cityName = $city->name;
        $city->delete();

        SystemLog::create([
            'user_id' => Auth::id(),
            'action' => 'DELETE_CITY',
            'description' => "Usuário excluiu o município {$cityName}",
            'ip_address' => $request->ip()
        ]);

        return response()->json(null, 204);
    }
}
