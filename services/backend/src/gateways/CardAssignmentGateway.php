<?php

class CardAssignmentGateway
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function findByCardId(int $cardId): array
    {
        $statement = $this->connection->prepare(
            "SELECT users.id, users.name, users.email
             FROM card_assignments
             JOIN users ON card_assignments.user_id = users.id
             WHERE card_assignments.card_id = :card_id
             ORDER BY users.id"
        );

        $statement->execute([
            'card_id' => $cardId
        ]);

        return $statement->fetchAll();
    }

    public function addAssignment(int $cardId, int $userId): bool
    {
        $statement = $this->connection->prepare(
            "INSERT INTO card_assignments (card_id, user_id)
             VALUES (:card_id, :user_id)"
        );

        return $statement->execute([
            'card_id' => $cardId,
            'user_id' => $userId
        ]);
    }

    public function removeAssignment(int $cardId, int $userId): bool
    {
        $statement = $this->connection->prepare(
            "DELETE FROM card_assignments
             WHERE card_id = :card_id
             AND user_id = :user_id"
        );

        return $statement->execute([
            'card_id' => $cardId,
            'user_id' => $userId
        ]);
    }

    public function isAssigned(int $cardId, int $userId): bool
    {
        $statement = $this->connection->prepare(
            "SELECT COUNT(*)
             FROM card_assignments
             WHERE card_id = :card_id
             AND user_id = :user_id"
        );

        $statement->execute([
            'card_id' => $cardId,
            'user_id' => $userId
        ]);

        return (int) $statement->fetchColumn() > 0;
    }
}