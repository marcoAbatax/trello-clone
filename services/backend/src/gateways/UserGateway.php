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
}