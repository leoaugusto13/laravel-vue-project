<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RegistrationFormController extends Controller
{
    public function show($trainingId)
    {
        try {
            \Illuminate\Support\Facades\Log::info("Fetching registration form for training: $trainingId");

            $form = \App\Models\RegistrationForm::with('fields')
                ->where('training_id', $trainingId)
                ->first();

            if (!$form) {
                 \Illuminate\Support\Facades\Log::info("Form not found for training: $trainingId");
                 return response()->json(['form' => null]);
            }
            
            return response()->json(['form' => $form]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Error fetching registration form: " . $e->getMessage());
            \Illuminate\Support\Facades\Log::error($e->getTraceAsString());
            return response()->json(['error' => 'Erro ao carregar o formulário'], 500);
        }
    }

    public function store(Request $request, $trainingId)
    {
        try {
            \Illuminate\Support\Facades\Log::info("Saving registration form for training: $trainingId", $request->all());

            /*
            $request->validate([
                'title' => 'required|string',
                'fields' => 'array',
                'fields.*.type' => 'required|string',
                'fields.*.label' => 'required|string',
            ]);
            */

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
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Illuminate\Support\Facades\Log::error("Validation error saving form: ", $e->errors());
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Error saving registration form: " . $e->getMessage());
            \Illuminate\Support\Facades\Log::error($e->getTraceAsString());
            return response()->json(['error' => 'Erro ao salvar o formulário: ' . $e->getMessage()], 500);
        }
    }
}
