<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RegistrationController extends Controller
{
    public function show($trainingId)
    {
        $training = \App\Models\Training::findOrFail($trainingId);
        $form = $training->registrationForm()->with('fields')->where('published', true)->firstOrFail();

        return response()->json([
            'training_title' => $training->action_name,
            'form' => $form
        ]);
    }

    public function submit(Request $request, $trainingId)
    {
        $training = \App\Models\Training::findOrFail($trainingId);
        $form = $training->registrationForm()->where('published', true)->firstOrFail();

        // Validation logic could be dynamic based on fields, but for now basic check
        $rules = [];
        foreach ($form->fields as $field) {
            if ($field->required) {
                $rules['answers.' . $field->id] = 'required';
            }
        }
        
        if (!auth()->check()) {
            $rules['guest_name'] = 'required|string';
            $rules['guest_email'] = 'required|email';
        }

        $request->validate($rules);

        $submission = $form->submissions()->create([
            'user_id' => auth()->id(),
            'guest_name' => $request->guest_name,
            'guest_email' => $request->guest_email,
            'answers' => $request->answers,
        ]);

        return response()->json(['message' => 'Registration successful', 'submission_id' => $submission->id]);
    }}
