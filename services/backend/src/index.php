<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

require_once __DIR__ . '/Controller.php';

try {
    $controller = new Controller();
    $controller->handleRequest();

} catch (Throwable $exception) {
    http_response_code(500);

    echo json_encode([
        'error' => $exception->getMessage()
    ]);
}