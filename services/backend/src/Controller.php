<?php

require_once __DIR__ . '/database/Database.php';
require_once __DIR__ . '/gateways/UserGateway.php';
require_once __DIR__ . '/gateways/BoardGateway.php';
require_once __DIR__ . '/gateways/BoardListGateway.php';
require_once __DIR__ . '/gateways/CardGateway.php';
require_once __DIR__ . '/gateways/BoardMemberGateway.php';
require_once __DIR__ . '/gateways/CardAssignmentGateway.php';

class Controller
{
    private UserGateway $userGateway;
    private BoardGateway $boardGateway;
    private BoardListGateway $boardListGateway;
    private CardGateway $cardGateway;
    private BoardMemberGateway $boardMemberGateway;
    private CardAssignmentGateway $cardAssignmentGateway;

    public function __construct()
    {
        $database = new Database();
        $connection = $database->getConnection();

        $this->userGateway = new UserGateway($connection);
        $this->boardGateway = new BoardGateway($connection);
        $this->boardListGateway = new BoardListGateway($connection);
        $this->cardGateway = new CardGateway($connection);
        $this->boardMemberGateway = new BoardMemberGateway($connection);
        $this->cardAssignmentGateway = new CardAssignmentGateway($connection);
    }

    public function handleRequest(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        // USERS

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

        // BOARD MEMBERS

        if (
            $method === 'GET' &&
            preg_match('#^/boards/(\d+)/members$#', $path, $matches)
        ) {
            $this->getBoardMembers((int) $matches[1]);
            return;
        }

        if (
            $method === 'POST' &&
            preg_match('#^/boards/(\d+)/members$#', $path, $matches)
        ) {
            $this->addBoardMember((int) $matches[1]);
            return;
        }

        if (
            $method === 'DELETE' &&
            preg_match('#^/boards/(\d+)/members/(\d+)$#', $path, $matches)
        ) {
            $this->removeBoardMember(
                (int) $matches[1],
                (int) $matches[2]
            );
            return;
        }

        // BOARDS

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

        // LISTS

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

        // CARD ASSIGNMENTS

        if (
            $method === 'GET' &&
            preg_match('#^/cards/(\d+)/assignments$#', $path, $matches)
        ) {
            $this->getCardAssignments((int) $matches[1]);
            return;
        }

        if (
            $method === 'POST' &&
            preg_match('#^/cards/(\d+)/assignments$#', $path, $matches)
        ) {
            $this->addCardAssignment((int) $matches[1]);
            return;
        }

        if (
            $method === 'DELETE' &&
            preg_match('#^/cards/(\d+)/assignments/(\d+)$#', $path, $matches)
        ) {
            $this->removeCardAssignment(
                (int) $matches[1],
                (int) $matches[2]
            );
            return;
        }

        // CARD MOVE

        if (
            $method === 'PUT' &&
            preg_match('#^/cards/(\d+)/move$#', $path, $matches)
        ) {
            $this->moveCard((int) $matches[1]);
            return;
        }

        // CARDS

        if ($method === 'GET' && $path === '/cards') {
            $this->getCards();
            return;
        }

        if ($method === 'POST' && $path === '/cards') {
            $this->createCard();
            return;
        }

        if (preg_match('#^/cards/(\d+)$#', $path, $matches)) {
            $id = (int) $matches[1];

            if ($method === 'GET') {
                $this->getCardById($id);
                return;
            }

            if ($method === 'PUT') {
                $this->updateCard($id);
                return;
            }

            if ($method === 'DELETE') {
                $this->deleteCard($id);
                return;
            }
        }

        http_response_code(404);

        echo json_encode([
            'error' => 'Endpoint non trovato'
        ]);
    }

    // USERS

    private function getUsers(): void
    {
        echo json_encode(
            $this->userGateway->findAll()
        );
    }

    private function getUserById(int $id): void
    {
        $user = $this->userGateway->findById($id);

        if (!$user) {
            http_response_code(404);
            echo json_encode(['error' => 'Utente non trovato']);
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

    // BOARDS

    private function getBoards(): void
    {
        echo json_encode(
            $this->boardGateway->findAll()
        );
    }

    private function getBoardById(int $id): void
    {
        $board = $this->boardGateway->findById($id);

        if (!$board) {
            http_response_code(404);
            echo json_encode(['error' => 'Board non trovata']);
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

    // BOARD MEMBERS

    private function getBoardMembers(int $boardId): void
    {
        if (!$this->boardGateway->findById($boardId)) {
            http_response_code(404);
            echo json_encode([
                'error' => 'Board non trovata'
            ]);
            return;
        }

        echo json_encode(
            $this->boardMemberGateway->findByBoardId($boardId)
        );
    }

    private function addBoardMember(int $boardId): void
    {
        if (!$this->boardGateway->findById($boardId)) {
            http_response_code(404);
            echo json_encode([
                'error' => 'Board non trovata'
            ]);
            return;
        }

        $data = json_decode(
            file_get_contents('php://input'),
            true
        );

        if (
            !is_array($data) ||
            !isset($data['user_id'])
        ) {
            http_response_code(400);
            echo json_encode([
                'error' => 'user_id è obbligatorio'
            ]);
            return;
        }

        $userId = (int) $data['user_id'];

        if (!$this->userGateway->findById($userId)) {
            http_response_code(404);
            echo json_encode([
                'error' => 'Utente non trovato'
            ]);
            return;
        }

        if ($this->boardMemberGateway->isMember($boardId, $userId)) {
            http_response_code(409);
            echo json_encode([
                'error' => 'Utente già membro della board'
            ]);
            return;
        }

        $this->boardMemberGateway->addMember(
            $boardId,
            $userId
        );

        http_response_code(201);

        echo json_encode([
            'message' => 'Membro aggiunto alla board'
        ]);
    }

    private function removeBoardMember(
        int $boardId,
        int $userId
    ): void {
        if (!$this->boardGateway->findById($boardId)) {
            http_response_code(404);
            echo json_encode([
                'error' => 'Board non trovata'
            ]);
            return;
        }

        if (!$this->boardMemberGateway->isMember($boardId, $userId)) {
            http_response_code(404);
            echo json_encode([
                'error' => 'Utente non membro della board'
            ]);
            return;
        }

        $this->boardMemberGateway->removeMember(
            $boardId,
            $userId
        );

        echo json_encode([
            'message' => 'Membro rimosso dalla board'
        ]);
    }

    // LISTS

    private function getLists(): void
    {
        echo json_encode(
            $this->boardListGateway->findAll()
        );
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

    // CARDS

    private function getCards(): void
    {
        echo json_encode(
            $this->cardGateway->findAll()
        );
    }

    private function getCardById(int $id): void
    {
        $card = $this->cardGateway->findById($id);

        if (!$card) {
            http_response_code(404);
            echo json_encode([
                'error' => 'Card non trovata'
            ]);
            return;
        }

        echo json_encode($card);
    }

    private function createCard(): void
    {
        $data = json_decode(
            file_get_contents('php://input'),
            true
        );

        if (
            !is_array($data) ||
            !isset($data['list_id']) ||
            !isset($data['title']) ||
            !isset($data['position'])
        ) {
            http_response_code(400);
            echo json_encode([
                'error' => 'list_id, title e position sono obbligatori'
            ]);
            return;
        }

        $description = $data['description'] ?? null;

        $id = $this->cardGateway->create(
            (int) $data['list_id'],
            $data['title'],
            $description,
            (int) $data['position']
        );

        http_response_code(201);

        echo json_encode([
            'id' => $id
        ]);
    }

    private function updateCard(int $id): void
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

        $description = $data['description'] ?? null;

        $this->cardGateway->update(
            $id,
            $data['title'],
            $description,
            (int) $data['position']
        );

        echo json_encode([
            'message' => 'Card aggiornata'
        ]);
    }

    private function deleteCard(int $id): void
    {
        $this->cardGateway->delete($id);

        echo json_encode([
            'message' => 'Card eliminata'
        ]);
    }

    // CARD MOVE

    private function moveCard(int $cardId): void
    {
        $card = $this->cardGateway->findById($cardId);

        if (!$card) {
            http_response_code(404);

            echo json_encode([
                'error' => 'Card non trovata'
            ]);

            return;
        }

        $data = json_decode(
            file_get_contents('php://input'),
            true
        );

        if (
            !is_array($data) ||
            !isset($data['list_id']) ||
            !isset($data['position'])
        ) {
            http_response_code(400);

            echo json_encode([
                'error' => 'list_id e position sono obbligatori'
            ]);

            return;
        }

        $listId = (int) $data['list_id'];
        $position = (int) $data['position'];

        $list = $this->boardListGateway->findById($listId);

        if (!$list) {
            http_response_code(404);

            echo json_encode([
                'error' => 'Lista di destinazione non trovata'
            ]);

            return;
        }

        if ($position < 1) {
            http_response_code(400);

            echo json_encode([
                'error' => 'position deve essere maggiore o uguale a 1'
            ]);

            return;
        }

        $this->cardGateway->move(
            $cardId,
            $listId,
            $position
        );

        echo json_encode([
            'message' => 'Card spostata'
        ]);
    }

    // CARD ASSIGNMENTS

    private function getCardAssignments(int $cardId): void
    {
        $card = $this->cardGateway->findById($cardId);

        if (!$card) {
            http_response_code(404);
            echo json_encode([
                'error' => 'Card non trovata'
            ]);
            return;
        }

        echo json_encode(
            $this->cardAssignmentGateway->findByCardId($cardId)
        );
    }

    private function addCardAssignment(int $cardId): void
    {
        $card = $this->cardGateway->findById($cardId);

        if (!$card) {
            http_response_code(404);
            echo json_encode([
                'error' => 'Card non trovata'
            ]);
            return;
        }

        $data = json_decode(
            file_get_contents('php://input'),
            true
        );

        if (
            !is_array($data) ||
            !isset($data['user_id'])
        ) {
            http_response_code(400);
            echo json_encode([
                'error' => 'user_id è obbligatorio'
            ]);
            return;
        }

        $userId = (int) $data['user_id'];

        if (!$this->userGateway->findById($userId)) {
            http_response_code(404);
            echo json_encode([
                'error' => 'Utente non trovato'
            ]);
            return;
        }

        $list = $this->boardListGateway->findById(
            (int) $card['list_id']
        );

        if (!$list) {
            http_response_code(404);
            echo json_encode([
                'error' => 'Lista della card non trovata'
            ]);
            return;
        }

        $boardId = (int) $list['board_id'];

        if (!$this->boardMemberGateway->isMember($boardId, $userId)) {
            http_response_code(400);

            echo json_encode([
                'error' => 'L\'utente non è membro della board'
            ]);

            return;
        }

        if (
            $this->cardAssignmentGateway->isAssigned(
                $cardId,
                $userId
            )
        ) {
            http_response_code(409);

            echo json_encode([
                'error' => 'Utente già assegnato alla card'
            ]);

            return;
        }

        $this->cardAssignmentGateway->addAssignment(
            $cardId,
            $userId
        );

        http_response_code(201);

        echo json_encode([
            'message' => 'Utente assegnato alla card'
        ]);
    }

    private function removeCardAssignment(
        int $cardId,
        int $userId
    ): void {
        $card = $this->cardGateway->findById($cardId);

        if (!$card) {
            http_response_code(404);

            echo json_encode([
                'error' => 'Card non trovata'
            ]);

            return;
        }

        if (
            !$this->cardAssignmentGateway->isAssigned(
                $cardId,
                $userId
            )
        ) {
            http_response_code(404);

            echo json_encode([
                'error' => 'Utente non assegnato alla card'
            ]);

            return;
        }

        $this->cardAssignmentGateway->removeAssignment(
            $cardId,
            $userId
        );

        echo json_encode([
            'message' => 'Assegnazione rimossa'
        ]);
    }
}