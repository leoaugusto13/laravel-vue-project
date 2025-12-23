<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Year;

class YearController extends Controller
{
    public function index()
    {
        return Year::orderBy('year', 'desc')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'year' => 'required|integer|min:2000|max:2100|unique:years,year',
            'is_closed' => 'boolean'
        ]);

        $year = Year::create($validated);
        
        \App\Services\LoggerService::log('YEAR_CREATE', "Ano '{$year->year}' criado");

        return response()->json($year, 201);
    }

    public function update(Request $request, Year $year)
    {
        $validated = $request->validate([
            'is_closed' => 'required|boolean'
        ]);

        $year->update($validated);
        
        \App\Services\LoggerService::log('YEAR_UPDATE', "Ano '{$year->year}' atualizado (Fechado: " . ($year->is_closed ? 'Sim' : 'Não') . ")");

        return response()->json($year);
    }

    public function destroy(Year $year)
    {
        $yr = $year->year;
        $year->delete();
        
        \App\Services\LoggerService::log('YEAR_DELETE', "Ano '{$yr}' excluído");

        return response()->json(null, 204);
    }
}
