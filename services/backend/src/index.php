<?php

ini_set('display_errors', '0');
ini_set('display_startup_errors', '0');
error_reporting(E_ALL);

header('Content-Type: application/json');

header('Access-Control-Allow-Origin: http://localhost:3000');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

require_once __DIR__ . '/Controller.php';

try {
    $controller = new Controller();
    $controller->handleRequest();

} catch (Throwable $exception) {

    error_log(
        sprintf(
            "Errore backend: %s in %s:%d",
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine()
        )
    );

    http_response_code(500);

    echo json_encode([
        'error' => 'Errore interno del server'
    ]);
}