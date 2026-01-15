<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RegistrationFormController extends Controller
{
    public function show($trainingId)
    {
        $form = \App\Models\RegistrationForm::with('fields')
            ->where('training_id', $trainingId)
            ->first();

        if (!$form) {
             return response()->json(['form' => null]);
        }
        
        return response()->json(['form' => $form]);
    }

    public function store(Request $request, $trainingId)
    {
        $request->validate([
            'title' => 'required|string',
            'fields' => 'array',
            'fields.*.type' => 'required|string',
            'fields.*.label' => 'required|string',
        ]);

        $training = \App\Models\Training::findOrFail($trainingId);

        $form = $training->registrationForm()->updateOrCreate(
            ['training_id' => $trainingId],
            [
                'title' => $request->title,
                'description' => $request->description,
                'published' => $request->published ?? false,
            ]
        );

        // Sync fields
        // Simplest strategy: delete all and recreate (or be smarter to keep IDs if needed for submissions, but for now simple sync)
        // Since we might have submissions, deleting fields is dangerous if we care about old data integrity linked to field IDs.
        // However, this is a "builder" - modifying it assumes structure change. 
        // Better: Update existing if ID present, create new if not, delete missing.
        
        $currentFieldIds = $form->fields()->pluck('id')->toArray();
        $inputFieldIds = collect($request->fields)->pluck('id')->filter()->toArray();
        $fieldsToDelete = array_diff($currentFieldIds, $inputFieldIds);

        \App\Models\RegistrationFormField::destroy($fieldsToDelete);

        foreach ($request->fields as $index => $fieldData) {
            $form->fields()->updateOrCreate(
                ['id' => $fieldData['id'] ?? null],
                [
                    'type' => $fieldData['type'],
                    'label' => $fieldData['label'],
                    'placeholder' => $fieldData['placeholder'] ?? null,
                    'required' => $fieldData['required'] ?? false,
                    'options' => $fieldData['options'] ?? null,
                    'order' => $index,
                ]
            );
        }

        return response()->json([
            'message' => 'Form saved successfully',
            'form' => $form->fresh('fields'),
        ]);
    }
}
