<?php

class BoardGateway
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function findAll(): array
    {
        $statement = $this->connection->query(
            "SELECT id, name FROM boards"
        );

        return $statement->fetchAll();
    }

    public function findById(int $id): array|false
    {
        $statement = $this->connection->prepare(
            "SELECT id, name FROM boards WHERE id = :id"
        );

        $statement->execute([
            'id' => $id
        ]);

        return $statement->fetch();
    }

    public function create(string $name): int
    {
        $statement = $this->connection->prepare(
            "INSERT INTO boards (name)
             VALUES (:name)"
        );

        $statement->execute([
            'name' => $name
        ]);

        return (int) $this->connection->lastInsertId();
    }

    public function update(int $id, string $name): bool
    {
        $statement = $this->connection->prepare(
            "UPDATE boards
             SET name = :name
             WHERE id = :id"
        );

        return $statement->execute([
            'id' => $id,
            'name' => $name
        ]);
    }

    public function delete(int $id): bool
    {
        $statement = $this->connection->prepare(
            "DELETE FROM boards WHERE id = :id"
        );

        return $statement->execute([
            'id' => $id
        ]);
    }
}