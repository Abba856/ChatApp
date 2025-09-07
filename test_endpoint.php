<?php
// Simple test script to check if PHP is working
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Log the request
    error_log("Test script received POST request");
    error_log("POST data: " . print_r($_POST, true));
    error_log("FILES data: " . print_r($_FILES, true));
    
    // Return a simple response
    echo json_encode([
        'status' => 'success',
        'message' => 'POST request received successfully',
        'post_data' => $_POST,
        'files_data' => array_keys($_FILES ?? [])
    ]);
} else {
    echo json_encode([
        'status' => 'info',
        'message' => 'This is a test endpoint. Send a POST request to test.',
        'method' => $_SERVER['REQUEST_METHOD']
    ]);
}
?>