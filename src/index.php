<?php

header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Get the HTTP request method
$method = $_SERVER['REQUEST_METHOD'];

// Handle preflight requests
if ($method === 'OPTIONS') {
    // Return OK status for preflight requests
    http_response_code(200);
    exit();
}


require_once __DIR__.'/vendor/autoload.php';

use app\Controller\ApiController;

$api = new ApiController();


// Get the URL path
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Route requests to appropriate controller methods based on the URL
switch ($path) {
    case '/api/employees':
        echo $api->getEmployees();
        break;
    case '/api/upload':
        echo $api->uploadEmployees();
        break;
    case '/api/update-employee-email':
        echo $api->updateEmployeeEmail();
        break;
    default:
        // Handle other routes or show a 404 error
        echo json_encode(['error' => '404 Not Found']);
}