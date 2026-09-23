<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/database/Database.php';

header('Content-Type: application/json');

try {
    $database = new Database();
    $connection = $database->getConnection();

    $statement = $connection->query("SELECT * FROM users");
    $users = $statement->fetchAll();

    echo json_encode($users);

} catch (Throwable $exception) {
    http_response_code(500);

    echo json_encode([
        'error' => $exception->getMessage()
    ]);
}