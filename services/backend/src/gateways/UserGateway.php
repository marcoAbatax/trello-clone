<?php

class UserGateway
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function findAll(): array
    {
        $statement = $this->connection->query(
            "SELECT id, name, email FROM users"
        );

        return $statement->fetchAll();
    }

    public function findById(int $id): array|false
{
    $statement = $this->connection->prepare(
        "SELECT id, name, email FROM users WHERE id = :id"
    );

    $statement->execute([
        'id' => $id
    ]);

    return $statement->fetch();
}

public function create(string $name, string $email): int
{
    $statement = $this->connection->prepare(
        "INSERT INTO users (name, email)
         VALUES (:name, :email)"
    );

    $statement->execute([
        'name' => $name,
        'email' => $email
    ]);

    return (int) $this->connection->lastInsertId();
}

public function update(int $id, string $name, string $email): bool
{
    $statement = $this->connection->prepare(
        "UPDATE users
         SET name = :name, email = :email
         WHERE id = :id"
    );

    return $statement->execute([
        'id' => $id,
        'name' => $name,
        'email' => $email
    ]);
}

public function delete(int $id): bool
{
    $statement = $this->connection->prepare(
        "DELETE FROM users WHERE id = :id"
    );

    return $statement->execute([
        'id' => $id
    ]);
}
}