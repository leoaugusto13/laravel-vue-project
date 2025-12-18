<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\User;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        if ($request->user()->role !== 'admin') {
            abort(403, 'Acesso não autorizado');
        }

        $totalUsers = User::count();
        $totalCourses = Course::count();
        $totalEnrollments = Enrollment::count();

        // Enrollments by Course
        $enrollmentsByCourse = Enrollment::select('course_id', DB::raw('count(*) as total'))
            ->with('course:id,title')
            ->groupBy('course_id')
            ->orderByDesc('total')
            ->take(5)
            ->get()
            ->map(function ($item) {
                return [
                    'course_title' => $item->course->title,
                    'count' => $item->total
                ];
            });

        // Recent Enrollments
        $recentEnrollments = Enrollment::with(['user:id,name', 'course:id,title'])
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

        return response()->json([
            'total_users' => $totalUsers,
            'total_courses' => $totalCourses,
            'total_enrollments' => $totalEnrollments,
            'enrollments_by_course' => $enrollmentsByCourse,
            'recent_enrollments' => $recentEnrollments
        ]);
    }
}
