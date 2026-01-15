<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

try {
    // Mock request data
    $data = [
        'title' => 'Updated Form Title',
        'description' => 'Updated Description',
        'published' => false,
        'fields' => [
            [
                'id' => null,
                'type' => 'text',
                'label' => 'Full Name',
                'placeholder' => 'Enter your name',
                'required' => true,
                'order' => 0
            ]
        ]
    ];

    // Create a request
    $request = Request::create('/api/admin/trainings/1/registration-form', 'POST', $data);
    $request->headers->set('Accept', 'application/json');

    // Run the app to handle the request
    $response = app()->handle($request);

    echo "Status: " . $response->getStatusCode() . "\n";
    echo "Content: " . $response->getContent() . "\n";

} catch (\Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
