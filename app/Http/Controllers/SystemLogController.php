<?php

namespace App\Http\Controllers;

use App\Models\SystemLog;
use Illuminate\Http\Request;

class SystemLogController extends Controller
{
    public function index(Request $request)
    {
        if ($request->user()->role !== 'admin') {
            abort(403, 'Acesso não autorizado');
        }

        $logs = SystemLog::with('user:id,name,email')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json($logs);
    }
}
