<?php

require_once __DIR__ . '/database/Database.php';
require_once __DIR__ . '/gateways/UserGateway.php';

class Controller
{
    private UserGateway $userGateway;

    public function __construct()
    {
        $database = new Database();

        $this->userGateway = new UserGateway(
            $database->getConnection()
        );
    }

    public function handleRequest(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        if ($method === 'GET' && $path === '/users') {
            $this->getUsers();
            return;
        }

        http_response_code(404);

        echo json_encode([
            'error' => 'Endpoint non trovato'
        ]);
    }

    private function getUsers(): void
    {
        $users = $this->userGateway->findAll();

        echo json_encode($users);
    }
}