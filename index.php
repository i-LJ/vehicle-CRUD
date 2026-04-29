<?php

require_once 'db.php';
require_once 'validation.php';
require_once 'vehicles.php';

// Ορίζουμε το JSON ως τον μοναδικό τύπο απάντησης για όλα τα requests
header('Content-Type: application/json');

// Καθορισμός του τύπου του request (GET, POST, PUT, DELETE)
$method = $_SERVER['REQUEST_METHOD'];

// Ανάγνωση και σωστή μορφοποίηση του URL
$path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

// Ελεγχουμε αν το URL είναι /vehicles ή /vehicles/123
if ($path === 'vehicles') {

    if ($method === 'GET') {
        getVehicles($pdo);

    } elseif ($method === 'POST') {
        createVehicle($pdo);

    } else {
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
    }

} elseif (preg_match('#^vehicles/(\d+)$#', $path, $matches)) {

    $id = (int)$matches[1];

    if ($method === 'GET') {
        getVehicleById($pdo, $id);

    } elseif ($method === 'PUT') {
        updateVehicle($pdo, $id);

    } elseif ($method === 'DELETE') {
        deleteVehicle($pdo, $id);

    } else {
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
    }

} else {
    http_response_code(404);
    echo json_encode(['error' => 'Not found']);
}
