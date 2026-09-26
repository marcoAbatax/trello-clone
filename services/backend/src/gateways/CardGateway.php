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


    /* =========================================================
       CREATE
       ========================================================= */

    public function create(
        int $listId,
        string $title,
        ?string $description,
        int $position
    ): int {
        try {
            $this->connection->beginTransaction();

            /*
             * Contiamo quante card sono già presenti
             * nella lista.
             */
            $statement = $this->connection->prepare(
                '
                SELECT COUNT(*)
                FROM cards
                WHERE list_id = :list_id
                '
            );

            $statement->execute([
                'list_id' => $listId
            ]);

            $count =
                (int) $statement->fetchColumn();


            /*
             * La posizione valida è compresa tra
             * 1 e count + 1.
             */
            $position = min(
                max(1, $position),
                $count + 1
            );


            /*
             * Se inseriamo la card in mezzo alla lista,
             * facciamo spazio spostando le altre card.
             */
            $statement = $this->connection->prepare(
                '
                UPDATE cards
                SET position = position + 1
                WHERE list_id = :list_id
                AND position >= :position
                '
            );

            $statement->execute([
                'list_id' => $listId,
                'position' => $position
            ]);


            /*
             * Inserimento della nuova card.
             */
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


    /* =========================================================
       UPDATE
       ========================================================= */

    public function update(
        int $id,
        string $title,
        ?string $description,
        int $position
    ): bool {
        $card =
            $this->findById($id);

        if (!$card) {
            return false;
        }

        $listId =
            (int) $card['list_id'];

        $oldPosition =
            (int) $card['position'];


        /*
         * Se la posizione è cambiata, utilizziamo
         * la stessa logica del move per mantenere
         * coerenti le posizioni.
         */
        if ($position !== $oldPosition) {

            $moved =
                $this->move(
                    $id,
                    $listId,
                    $position
                );

            if (!$moved) {
                return false;
            }
        }


        /*
         * Titolo e descrizione vengono aggiornati
         * separatamente dalla gestione della posizione.
         */
        $statement =
            $this->connection->prepare(
                '
                UPDATE cards
                SET
                    title = :title,
                    description = :description
                WHERE id = :id
                '
            );

        return $statement->execute([
            'id' => $id,
            'title' => $title,
            'description' => $description
        ]);
    }


    /* =========================================================
       DELETE
       ========================================================= */

    public function delete(int $id): bool
    {
        $card =
            $this->findById($id);

        if (!$card) {
            return false;
        }

        $listId =
            (int) $card['list_id'];

        $oldPosition =
            (int) $card['position'];

        try {
            $this->connection
                ->beginTransaction();


            /*
             * Eliminiamo la card.
             */
            $statement =
                $this->connection->prepare(
                    '
                    DELETE FROM cards
                    WHERE id = :id
                    '
                );

            $statement->execute([
                'id' => $id
            ]);


            /*
             * Chiudiamo il buco lasciato
             * nella lista.
             */
            $statement =
                $this->connection->prepare(
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

            $this->connection
                ->commit();

            return true;

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


    /* =========================================================
       MOVE
       ========================================================= */

    public function move(
        int $cardId,
        int $newListId,
        int $newPosition
    ): bool {
        $card =
            $this->findById($cardId);

        if (!$card) {
            return false;
        }

        $oldListId =
            (int) $card['list_id'];

        $oldPosition =
            (int) $card['position'];


        /*
         * Calcoliamo la posizione massima consentita.
         */
        $statement =
            $this->connection->prepare(
                '
                SELECT COUNT(*)
                FROM cards
                WHERE list_id = :list_id
                '
            );

        $statement->execute([
            'list_id' => $newListId
        ]);

        $destinationCount =
            (int) $statement->fetchColumn();


        /*
         * Se la card rimane nella stessa lista,
         * il massimo è il numero totale delle card.
         *
         * Se cambia lista, può essere inserita anche
         * dopo l'ultima card: count + 1.
         */
        if (
            $oldListId === $newListId
        ) {
            $maxPosition =
                max(1, $destinationCount);

        } else {
            $maxPosition =
                $destinationCount + 1;
        }


        $newPosition = min(
            max(1, $newPosition),
            $maxPosition
        );


        /*
         * Se non cambia nulla possiamo terminare.
         */
        if (
            $oldListId === $newListId &&
            $oldPosition === $newPosition
        ) {
            return true;
        }


        try {
            $this->connection
                ->beginTransaction();


            /*
             * CASO 1:
             * spostamento nella stessa lista.
             */
            if (
                $oldListId === $newListId
            ) {

                /*
                 * Spostamento verso il basso.
                 *
                 * Esempio:
                 *
                 * 1 A
                 * 2 B
                 * 3 C
                 *
                 * A passa da 1 a 3.
                 *
                 * B e C salgono di una posizione.
                 */
                if (
                    $newPosition >
                    $oldPosition
                ) {

                    $statement =
                        $this->connection->prepare(
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
                 * Spostamento verso l'alto.
                 */
                if (
                    $newPosition <
                    $oldPosition
                ) {

                    $statement =
                        $this->connection->prepare(
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
             * CASO 2:
             * spostamento in un'altra lista.
             */
            else {

                /*
                 * Chiudiamo il buco nella lista
                 * di origine.
                 */
                $statement =
                    $this->connection->prepare(
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
                 * Facciamo spazio nella lista
                 * di destinazione.
                 */
                $statement =
                    $this->connection->prepare(
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
             * Aggiorniamo infine la card spostata.
             */
            $statement =
                $this->connection->prepare(
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


            $this->connection
                ->commit();

            return true;

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