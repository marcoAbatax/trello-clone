<?php

class CardGateway
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function findAll(): array
    {
        $statement = $this->connection->query(
            "SELECT id, list_id, title, description, position
             FROM cards
             ORDER BY list_id, position"
        );

        return $statement->fetchAll();
    }

    public function findById(int $id): array|false
    {
        $statement = $this->connection->prepare(
            "SELECT id, list_id, title, description, position
             FROM cards
             WHERE id = :id"
        );

        $statement->execute([
            'id' => $id
        ]);

        return $statement->fetch();
    }

    public function create(
        int $listId,
        string $title,
        ?string $description,
        int $position
    ): int {
        $statement = $this->connection->prepare(
            "INSERT INTO cards (
                list_id,
                title,
                description,
                position
             )
             VALUES (
                :list_id,
                :title,
                :description,
                :position
             )"
        );

        $statement->execute([
            'list_id' => $listId,
            'title' => $title,
            'description' => $description,
            'position' => $position
        ]);

        return (int) $this->connection->lastInsertId();
    }

    public function update(
        int $id,
        string $title,
        ?string $description,
        int $position
    ): bool {
        $statement = $this->connection->prepare(
            "UPDATE cards
             SET title = :title,
                 description = :description,
                 position = :position
             WHERE id = :id"
        );

        return $statement->execute([
            'id' => $id,
            'title' => $title,
            'description' => $description,
            'position' => $position
        ]);
    }

    public function move(
        int $id,
        int $listId,
        int $position
    ): bool {
        $statement = $this->connection->prepare(
            "UPDATE cards
             SET list_id = :list_id,
                 position = :position
             WHERE id = :id"
        );

        return $statement->execute([
            'id' => $id,
            'list_id' => $listId,
            'position' => $position
        ]);
    }

    public function delete(int $id): bool
    {
        $statement = $this->connection->prepare(
            "DELETE FROM cards
             WHERE id = :id"
        );

        return $statement->execute([
            'id' => $id
        ]);
    }
}