<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Course;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        if ($request->user()->role === 'admin') {
            return Course::orderBy('created_at', 'desc')->get();
        }
        return Course::where('status', 'active')->orderBy('start_date', 'asc')->get();
    }

    public function store(Request $request)
    {
        if ($request->user()->role !== 'admin') {
            abort(403, 'Acesso não autorizado');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'start_date' => 'required|date',
            'status' => 'required|in:active,inactive',
            'image' => 'nullable|image|max:2048' // Max 2MB
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('courses', 'public');
            $validated['image'] = $path;
        }

        $course = Course::create($validated);

        \App\Services\LoggerService::log('COURSE_CREATE', "Course '{$course->title}' created");

        return response()->json($course, 201);
    }

    public function update(Request $request, Course $course)
    {
        if ($request->user()->role !== 'admin') {
            abort(403, 'Acesso não autorizado');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'start_date' => 'required|date',
            'status' => 'required|in:active,inactive',
            'image' => 'nullable|image|max:2048'
        ]);

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($course->image) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($course->image);
            }
            $path = $request->file('image')->store('courses', 'public');
            $validated['image'] = $path;
        }

        $course->update($validated);

        \App\Services\LoggerService::log('COURSE_UPDATE', "Course '{$course->title}' updated");

        return response()->json($course);
    }

    public function destroy(Request $request, Course $course)
    {
        if ($request->user()->role !== 'admin') {
            abort(403, 'Acesso não autorizado');
        }

        $title = $course->title;
        $course->delete();

        \App\Services\LoggerService::log('COURSE_DELETE', "Course '{$title}' deleted");

        return response()->noContent();
    }
}
