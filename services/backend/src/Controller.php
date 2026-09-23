<?php

require_once __DIR__ . '/database/Database.php';
require_once __DIR__ . '/gateways/UserGateway.php';
require_once __DIR__ . '/gateways/BoardGateway.php';
require_once __DIR__ . '/gateways/BoardListGateway.php';

class Controller
{
    private UserGateway $userGateway;
    private BoardGateway $boardGateway;
    private BoardListGateway $boardListGateway;

    public function __construct()
    {
        $database = new Database();
        $connection = $database->getConnection();

        $this->userGateway = new UserGateway($connection);
        $this->boardGateway = new BoardGateway($connection);
        $this->boardListGateway = new BoardListGateway($connection);
    }

    public function handleRequest(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        // =========================================================
        // USERS
        // =========================================================

        if ($method === 'GET' && $path === '/users') {
            $this->getUsers();
            return;
        }

        if ($method === 'POST' && $path === '/users') {
            $this->createUser();
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

        // =========================================================
        // BOARDS
        // =========================================================

        if ($method === 'GET' && $path === '/boards') {
            $this->getBoards();
            return;
        }

        if ($method === 'POST' && $path === '/boards') {
            $this->createBoard();
            return;
        }

        if (preg_match('#^/boards/(\d+)$#', $path, $matches)) {
            $id = (int) $matches[1];

            if ($method === 'GET') {
                $this->getBoardById($id);
                return;
            }

            if ($method === 'PUT') {
                $this->updateBoard($id);
                return;
            }

            if ($method === 'DELETE') {
                $this->deleteBoard($id);
                return;
            }
        }

        // =========================================================
        // LISTS
        // =========================================================

        if ($method === 'GET' && $path === '/lists') {
            $this->getLists();
            return;
        }

        if ($method === 'POST' && $path === '/lists') {
            $this->createList();
            return;
        }

        if (preg_match('#^/lists/(\d+)$#', $path, $matches)) {
            $id = (int) $matches[1];

            if ($method === 'GET') {
                $this->getListById($id);
                return;
            }

            if ($method === 'PUT') {
                $this->updateList($id);
                return;
            }

            if ($method === 'DELETE') {
                $this->deleteList($id);
                return;
            }
        }

        // =========================================================
        // ENDPOINT NON TROVATO
        // =========================================================

        http_response_code(404);

        echo json_encode([
            'error' => 'Endpoint non trovato'
        ]);
    }

    // =============================================================
    // USERS
    // =============================================================

    private function getUsers(): void
    {
        $users = $this->userGateway->findAll();

        echo json_encode($users);
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

    // =============================================================
    // BOARDS
    // =============================================================

    private function getBoards(): void
    {
        $boards = $this->boardGateway->findAll();

        echo json_encode($boards);
    }

    private function getBoardById(int $id): void
    {
        $board = $this->boardGateway->findById($id);

        if (!$board) {
            http_response_code(404);

            echo json_encode([
                'error' => 'Board non trovata'
            ]);

            return;
        }

        echo json_encode($board);
    }

    private function createBoard(): void
    {
        $data = json_decode(
            file_get_contents('php://input'),
            true
        );

        if (
            !is_array($data) ||
            !isset($data['name'])
        ) {
            http_response_code(400);

            echo json_encode([
                'error' => 'Il nome della board è obbligatorio'
            ]);

            return;
        }

        $id = $this->boardGateway->create(
            $data['name']
        );

        http_response_code(201);

        echo json_encode([
            'id' => $id
        ]);
    }

    private function updateBoard(int $id): void
    {
        $data = json_decode(
            file_get_contents('php://input'),
            true
        );

        if (
            !is_array($data) ||
            !isset($data['name'])
        ) {
            http_response_code(400);

            echo json_encode([
                'error' => 'Il nome della board è obbligatorio'
            ]);

            return;
        }

        $this->boardGateway->update(
            $id,
            $data['name']
        );

        echo json_encode([
            'message' => 'Board aggiornata'
        ]);
    }

    private function deleteBoard(int $id): void
    {
        $this->boardGateway->delete($id);

        echo json_encode([
            'message' => 'Board eliminata'
        ]);
    }

    // =============================================================
    // LISTS
    // =============================================================

    private function getLists(): void
    {
        $lists = $this->boardListGateway->findAll();

        echo json_encode($lists);
    }

    private function getListById(int $id): void
    {
        $list = $this->boardListGateway->findById($id);

        if (!$list) {
            http_response_code(404);

            echo json_encode([
                'error' => 'Lista non trovata'
            ]);

            return;
        }

        echo json_encode($list);
    }

    private function createList(): void
    {
        $data = json_decode(
            file_get_contents('php://input'),
            true
        );

        if (
            !is_array($data) ||
            !isset($data['board_id']) ||
            !isset($data['title']) ||
            !isset($data['position'])
        ) {
            http_response_code(400);

            echo json_encode([
                'error' => 'board_id, title e position sono obbligatori'
            ]);

            return;
        }

        $id = $this->boardListGateway->create(
            (int) $data['board_id'],
            $data['title'],
            (int) $data['position']
        );

        http_response_code(201);

        echo json_encode([
            'id' => $id
        ]);
    }

    private function updateList(int $id): void
    {
        $data = json_decode(
            file_get_contents('php://input'),
            true
        );

        if (
            !is_array($data) ||
            !isset($data['title']) ||
            !isset($data['position'])
        ) {
            http_response_code(400);

            echo json_encode([
                'error' => 'title e position sono obbligatori'
            ]);

            return;
        }

        $this->boardListGateway->update(
            $id,
            $data['title'],
            (int) $data['position']
        );

        echo json_encode([
            'message' => 'Lista aggiornata'
        ]);
    }

    private function deleteList(int $id): void
    {
        $this->boardListGateway->delete($id);

        echo json_encode([
            'message' => 'Lista eliminata'
        ]);
    }
}