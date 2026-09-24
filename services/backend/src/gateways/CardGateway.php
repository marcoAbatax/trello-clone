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
            '
            SELECT
                id,
                list_id,
                title,
                description,
                position
            FROM cards
            ORDER BY list_id, position
            '
        );

        return $statement->fetchAll();
    }

    public function findById(int $id): array|false
    {
        $statement = $this->connection->prepare(
            '
            SELECT
                id,
                list_id,
                title,
                description,
                position
            FROM cards
            WHERE id = :id
            '
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
            '
            INSERT INTO cards (
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
            )
            '
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
            '
            UPDATE cards
            SET
                title = :title,
                description = :description,
                position = :position
            WHERE id = :id
            '
        );

        return $statement->execute([
            'id' => $id,
            'title' => $title,
            'description' => $description,
            'position' => $position
        ]);
    }

    public function delete(int $id): bool
    {
        $card = $this->findById($id);

        if (!$card) {
            return false;
        }

        $listId = (int) $card['list_id'];
        $oldPosition = (int) $card['position'];

        try {
            $this->connection->beginTransaction();

            $statement = $this->connection->prepare(
                '
                DELETE FROM cards
                WHERE id = :id
                '
            );

            $statement->execute([
                'id' => $id
            ]);

            $statement = $this->connection->prepare(
                '
                UPDATE cards
                SET position = position - 1
                WHERE list_id = :list_id
                AND position > :position
                '
            );

            $statement->execute([
                'list_id' => $listId,
                'position' => $oldPosition
            ]);

            $this->connection->commit();

            return true;

        } catch (Throwable $exception) {
            if ($this->connection->inTransaction()) {
                $this->connection->rollBack();
            }

            throw $exception;
        }
    }

    public function move(
        int $cardId,
        int $newListId,
        int $newPosition
    ): bool {
        $card = $this->findById($cardId);

        if (!$card) {
            return false;
        }

        $oldListId = (int) $card['list_id'];
        $oldPosition = (int) $card['position'];

        try {
            $this->connection->beginTransaction();

            /*
             * Caso 1:
             * la card rimane nella stessa lista.
             */
            if ($oldListId === $newListId) {

                /*
                 * Se la nuova posizione è più in basso,
                 * spostiamo verso l'alto le card comprese
                 * tra la vecchia e la nuova posizione.
                 */
                if ($newPosition > $oldPosition) {
                    $statement = $this->connection->prepare(
                        '
                        UPDATE cards
                        SET position = position - 1
                        WHERE list_id = :list_id
                        AND position > :old_position
                        AND position <= :new_position
                        AND id <> :card_id
                        '
                    );

                    $statement->execute([
                        'list_id' => $oldListId,
                        'old_position' => $oldPosition,
                        'new_position' => $newPosition,
                        'card_id' => $cardId
                    ]);
                }

                /*
                 * Se la nuova posizione è più in alto,
                 * spostiamo verso il basso le card comprese
                 * tra la nuova e la vecchia posizione.
                 */
                if ($newPosition < $oldPosition) {
                    $statement = $this->connection->prepare(
                        '
                        UPDATE cards
                        SET position = position + 1
                        WHERE list_id = :list_id
                        AND position >= :new_position
                        AND position < :old_position
                        AND id <> :card_id
                        '
                    );

                    $statement->execute([
                        'list_id' => $oldListId,
                        'new_position' => $newPosition,
                        'old_position' => $oldPosition,
                        'card_id' => $cardId
                    ]);
                }
            }

            /*
             * Caso 2:
             * la card viene spostata in un'altra lista.
             */
            else {
                /*
                 * Chiudiamo il buco lasciato nella lista
                 * di partenza.
                 */
                $statement = $this->connection->prepare(
                    '
                    UPDATE cards
                    SET position = position - 1
                    WHERE list_id = :old_list_id
                    AND position > :old_position
                    '
                );

                $statement->execute([
                    'old_list_id' => $oldListId,
                    'old_position' => $oldPosition
                ]);

                /*
                 * Facciamo spazio nella lista di destinazione.
                 */
                $statement = $this->connection->prepare(
                    '
                    UPDATE cards
                    SET position = position + 1
                    WHERE list_id = :new_list_id
                    AND position >= :new_position
                    '
                );

                $statement->execute([
                    'new_list_id' => $newListId,
                    'new_position' => $newPosition
                ]);
            }

            /*
             * Infine aggiorniamo la card spostata.
             */
            $statement = $this->connection->prepare(
                '
                UPDATE cards
                SET
                    list_id = :list_id,
                    position = :position
                WHERE id = :id
                '
            );

            $statement->execute([
                'list_id' => $newListId,
                'position' => $newPosition,
                'id' => $cardId
            ]);

            $this->connection->commit();

            return true;

        } catch (Throwable $exception) {
            if ($this->connection->inTransaction()) {
                $this->connection->rollBack();
            }

            throw $exception;
        }
    }
}