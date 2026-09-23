<?php

class BoardListGateway
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function findAll(): array
    {
        $statement = $this->connection->query(
            "SELECT id, board_id, title, position
             FROM board_lists
             ORDER BY board_id, position"
        );

        return $statement->fetchAll();
    }

    public function findById(int $id): array|false
    {
        $statement = $this->connection->prepare(
            "SELECT id, board_id, title, position
             FROM board_lists
             WHERE id = :id"
        );

        $statement->execute([
            'id' => $id
        ]);

        return $statement->fetch();
    }

    public function create(
        int $boardId,
        string $title,
        int $position
    ): int {
        $statement = $this->connection->prepare(
            "INSERT INTO board_lists (board_id, title, position)
             VALUES (:board_id, :title, :position)"
        );

        $statement->execute([
            'board_id' => $boardId,
            'title' => $title,
            'position' => $position
        ]);

        return (int) $this->connection->lastInsertId();
    }

    public function update(
        int $id,
        string $title,
        int $position
    ): bool {
        $statement = $this->connection->prepare(
            "UPDATE board_lists
             SET title = :title,
                 position = :position
             WHERE id = :id"
        );

        return $statement->execute([
            'id' => $id,
            'title' => $title,
            'position' => $position
        ]);
    }

    public function delete(int $id): bool
    {
        $statement = $this->connection->prepare(
            "DELETE FROM board_lists
             WHERE id = :id"
        );

        return $statement->execute([
            'id' => $id
        ]);
    }
}