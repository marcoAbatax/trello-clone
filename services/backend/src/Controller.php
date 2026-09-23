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

    if (preg_match('#^/users/(\d+)$#', $path, $matches)) {
        $id = (int) $matches[1];

        if ($method === 'GET') {
            $this->getUserById($id);
            return;
        }

        if ($method === 'PUT') {
            $this->updateUser($id);
            return;
        }

        if ($method === 'DELETE') {
            $this->deleteUser($id);
            return;
        }
    }

    if ($method === 'POST' && $path === '/users') {
        $this->createUser();
        return;
    }

    http_response_code(404);

    echo json_encode([
        'error' => 'Endpoint non trovato'
    ]);
}

private function getUserById(int $id): void
{
    $user = $this->userGateway->findById($id);

    if (!$user) {
        http_response_code(404);

        echo json_encode([
            'error' => 'Utente non trovato'
        ]);

        return;
    }

    echo json_encode($user);
}

private function createUser(): void
{
    $data = json_decode(
        file_get_contents('php://input'),
        true
    );

    if (
        !is_array($data) ||
        !isset($data['name']) ||
        !isset($data['email'])
    ) {
        http_response_code(400);

        echo json_encode([
            'error' => 'Nome ed email sono obbligatori'
        ]);

        return;
    }

    $id = $this->userGateway->create(
        $data['name'],
        $data['email']
    );

    http_response_code(201);

    echo json_encode([
        'id' => $id
    ]);
}

private function updateUser(int $id): void
{
    $data = json_decode(
        file_get_contents('php://input'),
        true
    );

    $this->userGateway->update(
        $id,
        $data['name'],
        $data['email']
    );

    echo json_encode([
        'message' => 'Utente aggiornato'
    ]);
}

private function deleteUser(int $id): void
{
    $this->userGateway->delete($id);

    echo json_encode([
        'message' => 'Utente eliminato'
    ]);
}

private function getUsers(): void
{
    $users = $this->userGateway->findAll();

    echo json_encode($users);
}
}