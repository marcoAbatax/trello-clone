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

        $this->userGateway =
            new UserGateway($connection);

        $this->boardGateway =
            new BoardGateway($connection);

        $this->boardListGateway =
            new BoardListGateway($connection);

        $this->cardGateway =
            new CardGateway($connection);

        $this->boardMemberGateway =
            new BoardMemberGateway($connection);

        $this->cardAssignmentGateway =
            new CardAssignmentGateway($connection);
    }


    /* =========================================================
       ROUTING
       ========================================================= */

    public function handleRequest(): void
    {
        $method =
            $_SERVER['REQUEST_METHOD'];

        $path = parse_url(
            $_SERVER['REQUEST_URI'],
            PHP_URL_PATH
        );


        /* USERS */

        if (
            $method === 'GET' &&
            $path === '/users'
        ) {
            $this->getUsers();
            return;
        }

        if (
            $method === 'POST' &&
            $path === '/users'
        ) {
            $this->createUser();
            return;
        }

        if (
            preg_match(
                '#^/users/(\d+)$#',
                $path,
                $matches
            )
        ) {
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


        /* BOARD MEMBERS */

        if (
            $method === 'GET' &&
            preg_match(
                '#^/boards/(\d+)/members$#',
                $path,
                $matches
            )
        ) {
            $this->getBoardMembers(
                (int) $matches[1]
            );
            return;
        }

        if (
            $method === 'POST' &&
            preg_match(
                '#^/boards/(\d+)/members$#',
                $path,
                $matches
            )
        ) {
            $this->addBoardMember(
                (int) $matches[1]
            );
            return;
        }

        if (
            $method === 'DELETE' &&
            preg_match(
                '#^/boards/(\d+)/members/(\d+)$#',
                $path,
                $matches
            )
        ) {
            $this->removeBoardMember(
                (int) $matches[1],
                (int) $matches[2]
            );
            return;
        }


        /* BOARDS */

        if (
            $method === 'GET' &&
            $path === '/boards'
        ) {
            $this->getBoards();
            return;
        }

        if (
            $method === 'POST' &&
            $path === '/boards'
        ) {
            $this->createBoard();
            return;
        }

        if (
            preg_match(
                '#^/boards/(\d+)$#',
                $path,
                $matches
            )
        ) {
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


        /* LISTS */

        if (
            $method === 'GET' &&
            $path === '/lists'
        ) {
            $this->getLists();
            return;
        }

        if (
            $method === 'POST' &&
            $path === '/lists'
        ) {
            $this->createList();
            return;
        }

        if (
            preg_match(
                '#^/lists/(\d+)$#',
                $path,
                $matches
            )
        ) {
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


        /* CARD ASSIGNMENTS */

        if (
            $method === 'GET' &&
            preg_match(
                '#^/cards/(\d+)/assignments$#',
                $path,
                $matches
            )
        ) {
            $this->getCardAssignments(
                (int) $matches[1]
            );
            return;
        }

        if (
            $method === 'POST' &&
            preg_match(
                '#^/cards/(\d+)/assignments$#',
                $path,
                $matches
            )
        ) {
            $this->addCardAssignment(
                (int) $matches[1]
            );
            return;
        }

        if (
            $method === 'DELETE' &&
            preg_match(
                '#^/cards/(\d+)/assignments/(\d+)$#',
                $path,
                $matches
            )
        ) {
            $this->removeCardAssignment(
                (int) $matches[1],
                (int) $matches[2]
            );
            return;
        }


        /* CARD MOVE */

        if (
            $method === 'PUT' &&
            preg_match(
                '#^/cards/(\d+)/move$#',
                $path,
                $matches
            )
        ) {
            $this->moveCard(
                (int) $matches[1]
            );
            return;
        }


        /* CARDS */

        if (
            $method === 'GET' &&
            $path === '/cards'
        ) {
            $this->getCards();
            return;
        }

        if (
            $method === 'POST' &&
            $path === '/cards'
        ) {
            $this->createCard();
            return;
        }

        if (
            preg_match(
                '#^/cards/(\d+)$#',
                $path,
                $matches
            )
        ) {
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


        $this->sendError(
            404,
            'Endpoint non trovato'
        );
    }


    /* =========================================================
       UTILITY
       ========================================================= */

    private function getJsonBody(): ?array
    {
        $data = json_decode(
            file_get_contents('php://input'),
            true
        );

        if (!is_array($data)) {
            return null;
        }

        return $data;
    }


    private function sendError(
        int $statusCode,
        string $message
    ): void {
        http_response_code($statusCode);

        echo json_encode([
            'error' => $message
        ]);
    }


    private function sendMessage(
        string $message
    ): void {
        echo json_encode([
            'message' => $message
        ]);
    }


    private function isValidNonEmptyString(
        mixed $value,
        int $maxLength
    ): bool {
        return
            is_string($value) &&
            trim($value) !== '' &&
            mb_strlen(trim($value)) <= $maxLength;
    }


    private function isValidPositiveInteger(
        mixed $value
    ): bool {
        if (is_int($value)) {
            return $value >= 1;
        }

        if (
            is_string($value) &&
            ctype_digit($value)
        ) {
            return (int) $value >= 1;
        }

        return false;
    }


    private function isDuplicateEntryException(
        PDOException $exception
    ): bool {
        return
            $exception->getCode() === '23000' ||
            (
                isset($exception->errorInfo[1]) &&
                (int) $exception->errorInfo[1] === 1062
            );
    }


    /* =========================================================
       USERS
       ========================================================= */

    private function getUsers(): void
    {
        echo json_encode(
            $this->userGateway->findAll()
        );
    }


    private function getUserById(
        int $id
    ): void {
        $user =
            $this->userGateway->findById($id);

        if (!$user) {
            $this->sendError(
                404,
                'Utente non trovato'
            );
            return;
        }

        echo json_encode($user);
    }


    private function createUser(): void
    {
        $data =
            $this->getJsonBody();

        if ($data === null) {
            $this->sendError(
                400,
                'JSON non valido'
            );
            return;
        }

        if (
            !array_key_exists('name', $data) ||
            !array_key_exists('email', $data)
        ) {
            $this->sendError(
                400,
                'Nome ed email sono obbligatori'
            );
            return;
        }

        if (
            !$this->isValidNonEmptyString(
                $data['name'],
                100
            )
        ) {
            $this->sendError(
                400,
                'Il nome deve essere una stringa non vuota di massimo 100 caratteri'
            );
            return;
        }

        if (
            !is_string($data['email']) ||
            trim($data['email']) === '' ||
            mb_strlen(trim($data['email'])) > 255 ||
            !filter_var(
                trim($data['email']),
                FILTER_VALIDATE_EMAIL
            )
        ) {
            $this->sendError(
                400,
                'Email non valida'
            );
            return;
        }

        try {
            $id =
                $this->userGateway->create(
                    trim($data['name']),
                    trim($data['email'])
                );

        } catch (PDOException $exception) {

            if (
                $this->isDuplicateEntryException(
                    $exception
                )
            ) {
                $this->sendError(
                    409,
                    'Email già utilizzata'
                );
                return;
            }

            throw $exception;
        }

        http_response_code(201);

        echo json_encode([
            'id' => $id
        ]);
    }


    private function updateUser(
        int $id
    ): void {
        if (
            !$this->userGateway->findById($id)
        ) {
            $this->sendError(
                404,
                'Utente non trovato'
            );
            return;
        }

        $data =
            $this->getJsonBody();

        if ($data === null) {
            $this->sendError(
                400,
                'JSON non valido'
            );
            return;
        }

        if (
            !array_key_exists('name', $data) ||
            !array_key_exists('email', $data)
        ) {
            $this->sendError(
                400,
                'Nome ed email sono obbligatori'
            );
            return;
        }

        if (
            !$this->isValidNonEmptyString(
                $data['name'],
                100
            )
        ) {
            $this->sendError(
                400,
                'Il nome deve essere una stringa non vuota di massimo 100 caratteri'
            );
            return;
        }

        if (
            !is_string($data['email']) ||
            trim($data['email']) === '' ||
            mb_strlen(trim($data['email'])) > 255 ||
            !filter_var(
                trim($data['email']),
                FILTER_VALIDATE_EMAIL
            )
        ) {
            $this->sendError(
                400,
                'Email non valida'
            );
            return;
        }

        try {
            $this->userGateway->update(
                $id,
                trim($data['name']),
                trim($data['email'])
            );

        } catch (PDOException $exception) {

            if (
                $this->isDuplicateEntryException(
                    $exception
                )
            ) {
                $this->sendError(
                    409,
                    'Email già utilizzata'
                );
                return;
            }

            throw $exception;
        }

        $this->sendMessage(
            'Utente aggiornato'
        );
    }


    private function deleteUser(
        int $id
    ): void {
        if (
            !$this->userGateway->findById($id)
        ) {
            $this->sendError(
                404,
                'Utente non trovato'
            );
            return;
        }

        $this->userGateway->delete($id);

        $this->sendMessage(
            'Utente eliminato'
        );
    }


    /* =========================================================
       BOARDS
       ========================================================= */

    private function getBoards(): void
    {
        echo json_encode(
            $this->boardGateway->findAll()
        );
    }


    private function getBoardById(
        int $id
    ): void {
        $board =
            $this->boardGateway->findById($id);

        if (!$board) {
            $this->sendError(
                404,
                'Board non trovata'
            );
            return;
        }

        echo json_encode($board);
    }


    private function createBoard(): void
    {
        $data =
            $this->getJsonBody();

        if ($data === null) {
            $this->sendError(
                400,
                'JSON non valido'
            );
            return;
        }

        if (
            !array_key_exists('name', $data) ||
            !$this->isValidNonEmptyString(
                $data['name'],
                150
            )
        ) {
            $this->sendError(
                400,
                'Il nome della board deve essere una stringa non vuota di massimo 150 caratteri'
            );
            return;
        }

        $id =
            $this->boardGateway->create(
                trim($data['name'])
            );

        http_response_code(201);

        echo json_encode([
            'id' => $id
        ]);
    }


    private function updateBoard(
        int $id
    ): void {
        if (
            !$this->boardGateway->findById($id)
        ) {
            $this->sendError(
                404,
                'Board non trovata'
            );
            return;
        }

        $data =
            $this->getJsonBody();

        if ($data === null) {
            $this->sendError(
                400,
                'JSON non valido'
            );
            return;
        }

        if (
            !array_key_exists('name', $data) ||
            !$this->isValidNonEmptyString(
                $data['name'],
                150
            )
        ) {
            $this->sendError(
                400,
                'Il nome della board deve essere una stringa non vuota di massimo 150 caratteri'
            );
            return;
        }

        $this->boardGateway->update(
            $id,
            trim($data['name'])
        );

        $this->sendMessage(
            'Board aggiornata'
        );
    }


    private function deleteBoard(
        int $id
    ): void {
        if (
            !$this->boardGateway->findById($id)
        ) {
            $this->sendError(
                404,
                'Board non trovata'
            );
            return;
        }

        $this->boardGateway->delete($id);

        $this->sendMessage(
            'Board eliminata'
        );
    }


    /* =========================================================
       BOARD MEMBERS
       ========================================================= */

    private function getBoardMembers(
        int $boardId
    ): void {
        if (
            !$this->boardGateway
                ->findById($boardId)
        ) {
            $this->sendError(
                404,
                'Board non trovata'
            );
            return;
        }

        echo json_encode(
            $this->boardMemberGateway
                ->findByBoardId($boardId)
        );
    }


    private function addBoardMember(
        int $boardId
    ): void {
        if (
            !$this->boardGateway
                ->findById($boardId)
        ) {
            $this->sendError(
                404,
                'Board non trovata'
            );
            return;
        }

        $data =
            $this->getJsonBody();

        if (
            $data === null ||
            !array_key_exists(
                'user_id',
                $data
            ) ||
            !$this->isValidPositiveInteger(
                $data['user_id']
            )
        ) {
            $this->sendError(
                400,
                'user_id deve essere un intero positivo'
            );
            return;
        }

        $userId =
            (int) $data['user_id'];

        if (
            !$this->userGateway
                ->findById($userId)
        ) {
            $this->sendError(
                404,
                'Utente non trovato'
            );
            return;
        }

        if (
            $this->boardMemberGateway
                ->isMember(
                    $boardId,
                    $userId
                )
        ) {
            $this->sendError(
                409,
                'Utente già membro della board'
            );
            return;
        }

        $this->boardMemberGateway
            ->addMember(
                $boardId,
                $userId
            );

        http_response_code(201);

        $this->sendMessage(
            'Membro aggiunto alla board'
        );
    }


    private function removeBoardMember(
        int $boardId,
        int $userId
    ): void {
        if (
            !$this->boardGateway
                ->findById($boardId)
        ) {
            $this->sendError(
                404,
                'Board non trovata'
            );
            return;
        }

        if (
            !$this->boardMemberGateway
                ->isMember(
                    $boardId,
                    $userId
                )
        ) {
            $this->sendError(
                404,
                'Utente non membro della board'
            );
            return;
        }

        /*
         * Prima di rimuovere l'utente dalla board
         * vengono eliminate le sue assegnazioni
         * alle card appartenenti alla stessa board.
         */
        $this->cardAssignmentGateway
            ->removeAssignmentsForBoardMember(
                $boardId,
                $userId
            );

        $this->boardMemberGateway
            ->removeMember(
                $boardId,
                $userId
            );

        $this->sendMessage(
            'Membro rimosso dalla board'
        );
    }


    /* =========================================================
       LISTS
       ========================================================= */

    private function getLists(): void
    {
        echo json_encode(
            $this->boardListGateway->findAll()
        );
    }


    private function getListById(
        int $id
    ): void {
        $list =
            $this->boardListGateway
                ->findById($id);

        if (!$list) {
            $this->sendError(
                404,
                'Lista non trovata'
            );
            return;
        }

        echo json_encode($list);
    }


    private function createList(): void
    {
        $data =
            $this->getJsonBody();

        if ($data === null) {
            $this->sendError(
                400,
                'JSON non valido'
            );
            return;
        }

        if (
            !array_key_exists(
                'board_id',
                $data
            ) ||
            !array_key_exists(
                'title',
                $data
            ) ||
            !array_key_exists(
                'position',
                $data
            )
        ) {
            $this->sendError(
                400,
                'board_id, title e position sono obbligatori'
            );
            return;
        }

        if (
            !$this->isValidPositiveInteger(
                $data['board_id']
            )
        ) {
            $this->sendError(
                400,
                'board_id deve essere un intero positivo'
            );
            return;
        }

        if (
            !$this->isValidNonEmptyString(
                $data['title'],
                150
            )
        ) {
            $this->sendError(
                400,
                'Il titolo deve essere una stringa non vuota di massimo 150 caratteri'
            );
            return;
        }

        if (
            !$this->isValidPositiveInteger(
                $data['position']
            )
        ) {
            $this->sendError(
                400,
                'position deve essere un intero maggiore o uguale a 1'
            );
            return;
        }

        $boardId =
            (int) $data['board_id'];

        if (
            !$this->boardGateway
                ->findById($boardId)
        ) {
            $this->sendError(
                404,
                'Board non trovata'
            );
            return;
        }

        $id =
            $this->boardListGateway->create(
                $boardId,
                trim($data['title']),
                (int) $data['position']
            );

        http_response_code(201);

        echo json_encode([
            'id' => $id
        ]);
    }


    private function updateList(
        int $id
    ): void {
        if (
            !$this->boardListGateway
                ->findById($id)
        ) {
            $this->sendError(
                404,
                'Lista non trovata'
            );
            return;
        }

        $data =
            $this->getJsonBody();

        if ($data === null) {
            $this->sendError(
                400,
                'JSON non valido'
            );
            return;
        }

        if (
            !array_key_exists(
                'title',
                $data
            ) ||
            !array_key_exists(
                'position',
                $data
            )
        ) {
            $this->sendError(
                400,
                'title e position sono obbligatori'
            );
            return;
        }

        if (
            !$this->isValidNonEmptyString(
                $data['title'],
                150
            )
        ) {
            $this->sendError(
                400,
                'Il titolo deve essere una stringa non vuota di massimo 150 caratteri'
            );
            return;
        }

        if (
            !$this->isValidPositiveInteger(
                $data['position']
            )
        ) {
            $this->sendError(
                400,
                'position deve essere un intero maggiore o uguale a 1'
            );
            return;
        }

        $this->boardListGateway->update(
            $id,
            trim($data['title']),
            (int) $data['position']
        );

        $this->sendMessage(
            'Lista aggiornata'
        );
    }


    private function deleteList(
        int $id
    ): void {
        if (
            !$this->boardListGateway
                ->findById($id)
        ) {
            $this->sendError(
                404,
                'Lista non trovata'
            );
            return;
        }

        $this->boardListGateway
            ->delete($id);

        $this->sendMessage(
            'Lista eliminata'
        );
    }


    /* =========================================================
       CARDS
       ========================================================= */

    private function getCards(): void
    {
        echo json_encode(
            $this->cardGateway->findAll()
        );
    }


    private function getCardById(
        int $id
    ): void {
        $card =
            $this->cardGateway
                ->findById($id);

        if (!$card) {
            $this->sendError(
                404,
                'Card non trovata'
            );
            return;
        }

        echo json_encode($card);
    }


    private function createCard(): void
    {
        $data =
            $this->getJsonBody();

        if ($data === null) {
            $this->sendError(
                400,
                'JSON non valido'
            );
            return;
        }

        if (
            !array_key_exists(
                'list_id',
                $data
            ) ||
            !array_key_exists(
                'title',
                $data
            ) ||
            !array_key_exists(
                'position',
                $data
            )
        ) {
            $this->sendError(
                400,
                'list_id, title e position sono obbligatori'
            );
            return;
        }

        if (
            !$this->isValidPositiveInteger(
                $data['list_id']
            )
        ) {
            $this->sendError(
                400,
                'list_id deve essere un intero positivo'
            );
            return;
        }

        if (
            !$this->isValidNonEmptyString(
                $data['title'],
                150
            )
        ) {
            $this->sendError(
                400,
                'Il titolo deve essere una stringa non vuota di massimo 150 caratteri'
            );
            return;
        }

        if (
            !$this->isValidPositiveInteger(
                $data['position']
            )
        ) {
            $this->sendError(
                400,
                'position deve essere un intero maggiore o uguale a 1'
            );
            return;
        }

        $description =
            $data['description'] ?? null;

        if (
            $description !== null &&
            !is_string($description)
        ) {
            $this->sendError(
                400,
                'La descrizione deve essere una stringa'
            );
            return;
        }

        $listId =
            (int) $data['list_id'];

        if (
            !$this->boardListGateway
                ->findById($listId)
        ) {
            $this->sendError(
                404,
                'Lista non trovata'
            );
            return;
        }

        $id =
            $this->cardGateway->create(
                $listId,
                trim($data['title']),
                $description,
                (int) $data['position']
            );

        http_response_code(201);

        echo json_encode([
            'id' => $id
        ]);
    }


    private function updateCard(
        int $id
    ): void {
        if (
            !$this->cardGateway
                ->findById($id)
        ) {
            $this->sendError(
                404,
                'Card non trovata'
            );
            return;
        }

        $data =
            $this->getJsonBody();

        if ($data === null) {
            $this->sendError(
                400,
                'JSON non valido'
            );
            return;
        }

        if (
            !array_key_exists(
                'title',
                $data
            ) ||
            !array_key_exists(
                'position',
                $data
            )
        ) {
            $this->sendError(
                400,
                'title e position sono obbligatori'
            );
            return;
        }

        if (
            !$this->isValidNonEmptyString(
                $data['title'],
                150
            )
        ) {
            $this->sendError(
                400,
                'Il titolo deve essere una stringa non vuota di massimo 150 caratteri'
            );
            return;
        }

        if (
            !$this->isValidPositiveInteger(
                $data['position']
            )
        ) {
            $this->sendError(
                400,
                'position deve essere un intero maggiore o uguale a 1'
            );
            return;
        }

        $description =
            $data['description'] ?? null;

        if (
            $description !== null &&
            !is_string($description)
        ) {
            $this->sendError(
                400,
                'La descrizione deve essere una stringa'
            );
            return;
        }

        $this->cardGateway->update(
            $id,
            trim($data['title']),
            $description,
            (int) $data['position']
        );

        $this->sendMessage(
            'Card aggiornata'
        );
    }


    private function deleteCard(
        int $id
    ): void {
        if (
            !$this->cardGateway
                ->findById($id)
        ) {
            $this->sendError(
                404,
                'Card non trovata'
            );
            return;
        }

        $this->cardGateway
            ->delete($id);

        $this->sendMessage(
            'Card eliminata'
        );
    }


    /* =========================================================
       CARD MOVE
       ========================================================= */

    private function moveCard(
        int $cardId
    ): void {
        $card =
            $this->cardGateway
                ->findById($cardId);

        if (!$card) {
            $this->sendError(
                404,
                'Card non trovata'
            );
            return;
        }

        $data =
            $this->getJsonBody();

        if ($data === null) {
            $this->sendError(
                400,
                'JSON non valido'
            );
            return;
        }

        if (
            !array_key_exists(
                'list_id',
                $data
            ) ||
            !array_key_exists(
                'position',
                $data
            )
        ) {
            $this->sendError(
                400,
                'list_id e position sono obbligatori'
            );
            return;
        }

        if (
            !$this->isValidPositiveInteger(
                $data['list_id']
            )
        ) {
            $this->sendError(
                400,
                'list_id deve essere un intero positivo'
            );
            return;
        }

        if (
            !$this->isValidPositiveInteger(
                $data['position']
            )
        ) {
            $this->sendError(
                400,
                'position deve essere un intero maggiore o uguale a 1'
            );
            return;
        }

        $listId =
            (int) $data['list_id'];

        $position =
            (int) $data['position'];

        $list =
            $this->boardListGateway
                ->findById($listId);

        if (!$list) {
            $this->sendError(
                404,
                'Lista di destinazione non trovata'
            );
            return;
        }

        $this->cardGateway->move(
            $cardId,
            $listId,
            $position
        );

        $this->sendMessage(
            'Card spostata'
        );
    }


    /* =========================================================
       CARD ASSIGNMENTS
       ========================================================= */

    private function getCardAssignments(
        int $cardId
    ): void {
        $card =
            $this->cardGateway
                ->findById($cardId);

        if (!$card) {
            $this->sendError(
                404,
                'Card non trovata'
            );
            return;
        }

        echo json_encode(
            $this->cardAssignmentGateway
                ->findByCardId($cardId)
        );
    }


    private function addCardAssignment(
        int $cardId
    ): void {
        $card =
            $this->cardGateway
                ->findById($cardId);

        if (!$card) {
            $this->sendError(
                404,
                'Card non trovata'
            );
            return;
        }

        $data =
            $this->getJsonBody();

        if (
            $data === null ||
            !array_key_exists(
                'user_id',
                $data
            ) ||
            !$this->isValidPositiveInteger(
                $data['user_id']
            )
        ) {
            $this->sendError(
                400,
                'user_id deve essere un intero positivo'
            );
            return;
        }

        $userId =
            (int) $data['user_id'];

        if (
            !$this->userGateway
                ->findById($userId)
        ) {
            $this->sendError(
                404,
                'Utente non trovato'
            );
            return;
        }

        $list =
            $this->boardListGateway
                ->findById(
                    (int) $card['list_id']
                );

        if (!$list) {
            $this->sendError(
                404,
                'Lista della card non trovata'
            );
            return;
        }

        $boardId =
            (int) $list['board_id'];

        if (
            !$this->boardMemberGateway
                ->isMember(
                    $boardId,
                    $userId
                )
        ) {
            $this->sendError(
                400,
                'L\'utente non è membro della board'
            );
            return;
        }

        if (
            $this->cardAssignmentGateway
                ->isAssigned(
                    $cardId,
                    $userId
                )
        ) {
            $this->sendError(
                409,
                'Utente già assegnato alla card'
            );
            return;
        }

        $this->cardAssignmentGateway
            ->addAssignment(
                $cardId,
                $userId
            );

        http_response_code(201);

        $this->sendMessage(
            'Utente assegnato alla card'
        );
    }


    private function removeCardAssignment(
        int $cardId,
        int $userId
    ): void {
        if (
            !$this->cardGateway
                ->findById($cardId)
        ) {
            $this->sendError(
                404,
                'Card non trovata'
            );
            return;
        }

        if (
            !$this->cardAssignmentGateway
                ->isAssigned(
                    $cardId,
                    $userId
                )
        ) {
            $this->sendError(
                404,
                'Utente non assegnato alla card'
            );
            return;
        }

        $this->cardAssignmentGateway
            ->removeAssignment(
                $cardId,
                $userId
            );

        $this->sendMessage(
            'Assegnazione rimossa'
        );
    }
}