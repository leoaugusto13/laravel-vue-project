<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnrollmentController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'phone' => 'required|string',
            'address' => 'required|string',
            'motivation' => 'required|string',
        ]);

        $enrollment = Enrollment::create([
            'user_id' => Auth::id(),
            'course_id' => $request->course_id,
            'registration_data' => [
                'phone' => $request->phone,
                'address' => $request->address,
                'motivation' => $request->motivation,
            ],
            'status' => 'pending',
        ]);

        \App\Services\LoggerService::log('ENROLLMENT_CREATE', "Enrolled in course " . $request->course_id);

        return response()->json([
            'message' => 'Inscrição realizada com sucesso!',
            'enrollment' => $enrollment
        ], 201);
    }
}
