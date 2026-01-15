<?php
try {
    $t = \App\Models\Training::first();
    if ($t) {
        $t->registrationForm()->create([
            'title' => 'Test Form Created by Debugger',
            'description' => 'Automatically created for testing.',
            'published' => true
        ]);
        echo "Form created successfully for Training ID: " . $t->id;
    } else {
        echo "No training found.";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}
