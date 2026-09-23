<?php

class BoardMemberGateway
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function findByBoardId(int $boardId): array
    {
        $statement = $this->connection->prepare(
            "SELECT users.id, users.name, users.email
             FROM board_members
             JOIN users ON board_members.user_id = users.id
             WHERE board_members.board_id = :board_id
             ORDER BY users.id"
        );

        $statement->execute([
            'board_id' => $boardId
        ]);

        return $statement->fetchAll();
    }

    public function addMember(int $boardId, int $userId): bool
    {
        $statement = $this->connection->prepare(
            "INSERT INTO board_members (board_id, user_id)
             VALUES (:board_id, :user_id)"
        );

        return $statement->execute([
            'board_id' => $boardId,
            'user_id' => $userId
        ]);
    }

    public function removeMember(int $boardId, int $userId): bool
    {
        $statement = $this->connection->prepare(
            "DELETE FROM board_members
             WHERE board_id = :board_id
             AND user_id = :user_id"
        );

        return $statement->execute([
            'board_id' => $boardId,
            'user_id' => $userId
        ]);
    }

    public function isMember(int $boardId, int $userId): bool
    {
        $statement = $this->connection->prepare(
            "SELECT COUNT(*) 
             FROM board_members
             WHERE board_id = :board_id
             AND user_id = :user_id"
        );

        $statement->execute([
            'board_id' => $boardId,
            'user_id' => $userId
        ]);

        return (int) $statement->fetchColumn() > 0;
    }
}