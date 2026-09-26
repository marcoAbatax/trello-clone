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
        try {
            $this->connection->beginTransaction();

            $countStatement =
                $this->connection->prepare(
                    "SELECT COUNT(*)
                     FROM board_lists
                     WHERE board_id = :board_id"
                );

            $countStatement->execute([
                'board_id' => $boardId
            ]);

            $count =
                (int) $countStatement->fetchColumn();

            $position = min(
                max(1, $position),
                $count + 1
            );

            $shiftStatement =
                $this->connection->prepare(
                    "UPDATE board_lists
                     SET position = position + 1
                     WHERE board_id = :board_id
                     AND position >= :position"
                );

            $shiftStatement->execute([
                'board_id' => $boardId,
                'position' => $position
            ]);

            $statement =
                $this->connection->prepare(
                    "INSERT INTO board_lists
                        (board_id, title, position)
                     VALUES
                        (:board_id, :title, :position)"
                );

            $statement->execute([
                'board_id' => $boardId,
                'title' => $title,
                'position' => $position
            ]);

            $id =
                (int) $this->connection
                    ->lastInsertId();

            $this->connection->commit();

            return $id;

        } catch (Throwable $exception) {

            if (
                $this->connection
                    ->inTransaction()
            ) {
                $this->connection
                    ->rollBack();
            }

            throw $exception;
        }
    }

    public function update(
        int $id,
        string $title,
        int $position
    ): bool {
        $list =
            $this->findById($id);

        if (!$list) {
            return false;
        }

        $boardId =
            (int) $list['board_id'];

        $oldPosition =
            (int) $list['position'];

        try {
            $this->connection
                ->beginTransaction();

            $countStatement =
                $this->connection->prepare(
                    "SELECT COUNT(*)
                     FROM board_lists
                     WHERE board_id = :board_id"
                );

            $countStatement->execute([
                'board_id' => $boardId
            ]);

            $count =
                (int) $countStatement->fetchColumn();

            $position = min(
                max(1, $position),
                max(1, $count)
            );

            if (
                $position > $oldPosition
            ) {
                $shiftStatement =
                    $this->connection->prepare(
                        "UPDATE board_lists
                         SET position = position - 1
                         WHERE board_id = :board_id
                         AND position > :old_position
                         AND position <= :new_position
                         AND id <> :id"
                    );

                $shiftStatement->execute([
                    'board_id' => $boardId,
                    'old_position' => $oldPosition,
                    'new_position' => $position,
                    'id' => $id
                ]);

            } elseif (
                $position < $oldPosition
            ) {
                $shiftStatement =
                    $this->connection->prepare(
                        "UPDATE board_lists
                         SET position = position + 1
                         WHERE board_id = :board_id
                         AND position >= :new_position
                         AND position < :old_position
                         AND id <> :id"
                    );

                $shiftStatement->execute([
                    'board_id' => $boardId,
                    'new_position' => $position,
                    'old_position' => $oldPosition,
                    'id' => $id
                ]);
            }

            $statement =
                $this->connection->prepare(
                    "UPDATE board_lists
                     SET title = :title,
                         position = :position
                     WHERE id = :id"
                );

            $result =
                $statement->execute([
                    'id' => $id,
                    'title' => $title,
                    'position' => $position
                ]);

            $this->connection->commit();

            return $result;

        } catch (Throwable $exception) {

            if (
                $this->connection
                    ->inTransaction()
            ) {
                $this->connection
                    ->rollBack();
            }

            throw $exception;
        }
    }

    public function delete(int $id): bool
    {
        $list =
            $this->findById($id);

        if (!$list) {
            return false;
        }

        $boardId =
            (int) $list['board_id'];

        $position =
            (int) $list['position'];

        try {
            $this->connection
                ->beginTransaction();

            $statement =
                $this->connection->prepare(
                    "DELETE FROM board_lists
                     WHERE id = :id"
                );

            $result =
                $statement->execute([
                    'id' => $id
                ]);

            $shiftStatement =
                $this->connection->prepare(
                    "UPDATE board_lists
                     SET position = position - 1
                     WHERE board_id = :board_id
                     AND position > :position"
                );

            $shiftStatement->execute([
                'board_id' => $boardId,
                'position' => $position
            ]);

            $this->connection
                ->commit();

            return $result;

        } catch (Throwable $exception) {

            if (
                $this->connection
                    ->inTransaction()
            ) {
                $this->connection
                    ->rollBack();
            }

            throw $exception;
        }
    }
}