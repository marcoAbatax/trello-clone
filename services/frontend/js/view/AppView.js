export class AppView {

    constructor() {
        this.app = document.getElementById('app');
    }

    /* =========================================================
       UTILITY
       ========================================================= */

    showLoading() {
        this.app.innerHTML = '<p>Caricamento...</p>';
    }

    escapeHtml(value) {
        if (value === null || value === undefined) {
            return '';
        }

        return String(value)
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }


    /* =========================================================
       HOME
       ========================================================= */

    showBoards(
        boards,
        users,
        onBoardClick,
        onCreateBoard,
        onUpdateBoard,
        onDeleteBoard,
        onCreateUser,
        onUpdateUser,
        onDeleteUser
    ) {

        this.app.innerHTML = `
            <h2>Le board</h2>

            <div class="create-board-container">

                <input
                    id="board-name-input"
                    type="text"
                    placeholder="Nome nuova board"
                    maxlength="150"
                >

                <button
                    id="create-board-button"
                    type="button"
                >
                    Crea board
                </button>

            </div>

            <div class="boards">

                ${boards.map(board => `
                    <div class="board">

                        <h2>
                            ${this.escapeHtml(board.name)}
                        </h2>

                        <p>
                            ID: ${Number(board.id)}
                        </p>

                        <button
                            class="open-board-button"
                            data-board-id="${Number(board.id)}"
                            type="button"
                        >
                            Apri
                        </button>

                        <button
                            class="edit-board-button"
                            data-board-id="${Number(board.id)}"
                            type="button"
                        >
                            Modifica
                        </button>

                        <button
                            class="delete-board-button"
                            data-board-id="${Number(board.id)}"
                            type="button"
                        >
                            Elimina
                        </button>

                    </div>
                `).join('')}

            </div>

            <hr>

            <section class="users-section">

                <h2>Utenti</h2>

                <div class="create-user-container">

                    <input
                        id="user-name-input"
                        type="text"
                        placeholder="Nome utente"
                        maxlength="100"
                    >

                    <input
                        id="user-email-input"
                        type="email"
                        placeholder="Email utente"
                        maxlength="255"
                    >

                    <button
                        id="create-user-button"
                        type="button"
                    >
                        Crea utente
                    </button>

                </div>

                <div class="users">

                    ${users.map(user => `
                        <div class="user">

                            <strong>
                                ${this.escapeHtml(user.name)}
                            </strong>

                            <span>
                                ${this.escapeHtml(user.email)}
                            </span>

                            <button
                                class="edit-user-button"
                                data-user-id="${Number(user.id)}"
                                type="button"
                            >
                                Modifica
                            </button>

                            <button
                                class="delete-user-button"
                                data-user-id="${Number(user.id)}"
                                type="button"
                            >
                                Elimina
                            </button>

                        </div>
                    `).join('')}

                </div>

            </section>
        `;


        /* CREATE BOARD */

        const createBoardButton =
            document.getElementById('create-board-button');

        createBoardButton.addEventListener(
            'click',
            async () => {

                const input =
                    document.getElementById(
                        'board-name-input'
                    );

                const name =
                    input.value.trim();

                if (!name) {
                    return;
                }

                await onCreateBoard(name);
            }
        );


        /* OPEN BOARD */

        document
            .querySelectorAll('.open-board-button')
            .forEach(button => {

                button.addEventListener(
                    'click',
                    () => {

                        const boardId =
                            Number(
                                button.dataset.boardId
                            );

                        onBoardClick(boardId);
                    }
                );
            });


        /* EDIT BOARD */

        document
            .querySelectorAll('.edit-board-button')
            .forEach(button => {

                button.addEventListener(
                    'click',
                    async () => {

                        const boardId =
                            Number(
                                button.dataset.boardId
                            );

                        const board =
                            boards.find(
                                item =>
                                    Number(item.id) ===
                                    boardId
                            );

                        if (!board) {
                            return;
                        }

                        const newName = prompt(
                            'Nuovo nome della board:',
                            board.name
                        );

                        if (
                            newName === null ||
                            !newName.trim()
                        ) {
                            return;
                        }

                        await onUpdateBoard(
                            boardId,
                            newName.trim()
                        );
                    }
                );
            });


        /* DELETE BOARD */

        document
            .querySelectorAll('.delete-board-button')
            .forEach(button => {

                button.addEventListener(
                    'click',
                    async () => {

                        const boardId =
                            Number(
                                button.dataset.boardId
                            );

                        const confirmed =
                            confirm(
                                'Sei sicuro di voler eliminare questa board? Verranno eliminate anche le liste e le card contenute.'
                            );

                        if (!confirmed) {
                            return;
                        }

                        await onDeleteBoard(
                            boardId
                        );
                    }
                );
            });


        /* CREATE USER */

        const createUserButton =
            document.getElementById(
                'create-user-button'
            );

        createUserButton.addEventListener(
            'click',
            async () => {

                const nameInput =
                    document.getElementById(
                        'user-name-input'
                    );

                const emailInput =
                    document.getElementById(
                        'user-email-input'
                    );

                const name =
                    nameInput.value.trim();

                const email =
                    emailInput.value.trim();

                if (!name || !email) {
                    return;
                }

                await onCreateUser(
                    name,
                    email
                );
            }
        );


        /* EDIT USER */

        document
            .querySelectorAll('.edit-user-button')
            .forEach(button => {

                button.addEventListener(
                    'click',
                    async () => {

                        const userId =
                            Number(
                                button.dataset.userId
                            );

                        const user =
                            users.find(
                                item =>
                                    Number(item.id) ===
                                    userId
                            );

                        if (!user) {
                            return;
                        }

                        const newName = prompt(
                            'Nome utente:',
                            user.name
                        );

                        if (
                            newName === null ||
                            !newName.trim()
                        ) {
                            return;
                        }

                        const newEmail = prompt(
                            'Email utente:',
                            user.email
                        );

                        if (
                            newEmail === null ||
                            !newEmail.trim()
                        ) {
                            return;
                        }

                        await onUpdateUser(
                            userId,
                            newName.trim(),
                            newEmail.trim()
                        );
                    }
                );
            });


        /* DELETE USER */

        document
            .querySelectorAll('.delete-user-button')
            .forEach(button => {

                button.addEventListener(
                    'click',
                    async () => {

                        const userId =
                            Number(
                                button.dataset.userId
                            );

                        const confirmed =
                            confirm(
                                'Sei sicuro di voler eliminare questo utente?'
                            );

                        if (!confirmed) {
                            return;
                        }

                        await onDeleteUser(
                            userId
                        );
                    }
                );
            });
    }


    /* =========================================================
       BOARD
       ========================================================= */

    showBoard(
        board,
        lists,
        cards,
        members,
        users,
        assignments,
        onBack,
        onAddCard,
        onDeleteCard,
        onEditCard,
        onMoveCard,
        onAddList,
        onEditList,
        onDeleteList,
        onAddMember,
        onRemoveMember,
        onAddAssignment,
        onRemoveAssignment
    ) {

        const boardLists =
            lists
                .filter(
                    list =>
                        Number(list.board_id) ===
                        Number(board.id)
                )
                .sort(
                    (a, b) =>
                        Number(a.position) -
                        Number(b.position)
                );

        const boardListIds =
            boardLists.map(
                list => Number(list.id)
            );

        const boardCards =
            cards.filter(
                card =>
                    boardListIds.includes(
                        Number(card.list_id)
                    )
            );

        const availableMembers =
            users.filter(
                user =>
                    !members.some(
                        member =>
                            Number(member.id) ===
                            Number(user.id)
                    )
            );


        this.app.innerHTML = `

            <button
                id="back-button"
                type="button"
            >
                ← Torna alle board
            </button>

            <h2>
                ${this.escapeHtml(board.name)}
            </h2>


            <!-- MEMBRI -->

            <section class="board-members">

                <h3>Membri della board</h3>

                <div class="members">

                    ${members.map(member => `
                        <div class="member">

                            <strong>
                                ${this.escapeHtml(member.name)}
                            </strong>

                            <span>
                                ${this.escapeHtml(member.email)}
                            </span>

                            <button
                                class="remove-member-button"
                                data-user-id="${Number(member.id)}"
                                type="button"
                            >
                                Rimuovi
                            </button>

                        </div>
                    `).join('')}

                </div>


                ${
                    availableMembers.length > 0
                        ? `
                            <div class="add-member-container">

                                <select id="member-select">

                                    <option value="">
                                        Seleziona utente
                                    </option>

                                    ${availableMembers.map(user => `
                                        <option
                                            value="${Number(user.id)}"
                                        >
                                            ${this.escapeHtml(user.name)}
                                        </option>
                                    `).join('')}

                                </select>

                                <button
                                    id="add-member-button"
                                    type="button"
                                >
                                    Aggiungi membro
                                </button>

                            </div>
                        `
                        : ''
                }

            </section>


            <!-- LISTE -->

            <div class="lists">

                ${boardLists.map(list => {

                    const listCards =
                        boardCards
                            .filter(
                                card =>
                                    Number(card.list_id) ===
                                    Number(list.id)
                            )
                            .sort(
                                (a, b) =>
                                    Number(a.position) -
                                    Number(b.position)
                            );

                    return `
                        <div
                            class="list"
                            data-list-id="${Number(list.id)}"
                        >

                            <h3>
                                ${this.escapeHtml(list.title)}
                            </h3>

                            <button
                                class="edit-list-button"
                                data-list-id="${Number(list.id)}"
                                type="button"
                            >
                                Modifica lista
                            </button>

                            <button
                                class="delete-list-button"
                                data-list-id="${Number(list.id)}"
                                type="button"
                            >
                                Elimina lista
                            </button>


                            <div
                                class="cards"
                                data-list-id="${Number(list.id)}"
                            >

                                ${listCards.map(card => {

                                    const cardAssignments =
                                        assignments[card.id] || [];

                                    const assignableMembers =
                                        members.filter(
                                            member =>
                                                !cardAssignments.some(
                                                    assignment =>
                                                        Number(
                                                            assignment.id
                                                        ) ===
                                                        Number(
                                                            member.id
                                                        )
                                                )
                                        );

                                    return `
                                        <div
                                            class="card"
                                            draggable="true"
                                            data-card-id="${Number(card.id)}"
                                            data-list-id="${Number(list.id)}"
                                        >

                                            <h4>
                                                ${this.escapeHtml(card.title)}
                                            </h4>


                                            ${
                                                card.description
                                                    ? `
                                                        <p>
                                                            ${this.escapeHtml(
                                                                card.description
                                                            )}
                                                        </p>
                                                    `
                                                    : ''
                                            }


                                            <div class="card-assignments">

                                                ${cardAssignments.map(
                                                    assignment => `
                                                        <span class="card-assignment">

                                                            ${this.escapeHtml(
                                                                assignment.name
                                                            )}

                                                            <button
                                                                class="remove-assignment-button"
                                                                data-card-id="${Number(card.id)}"
                                                                data-user-id="${Number(assignment.id)}"
                                                                type="button"
                                                                title="Rimuovi assegnazione"
                                                            >
                                                                ×
                                                            </button>

                                                        </span>
                                                    `
                                                ).join('')}

                                            </div>


                                            ${
                                                assignableMembers.length > 0
                                                    ? `
                                                        <div class="assignment-controls">

                                                            <select
                                                                class="assignment-select"
                                                                data-card-id="${Number(card.id)}"
                                                            >

                                                                <option value="">
                                                                    Assegna membro
                                                                </option>

                                                                ${assignableMembers.map(
                                                                    member => `
                                                                        <option
                                                                            value="${Number(member.id)}"
                                                                        >
                                                                            ${this.escapeHtml(
                                                                                member.name
                                                                            )}
                                                                        </option>
                                                                    `
                                                                ).join('')}

                                                            </select>

                                                            <button
                                                                class="add-assignment-button"
                                                                data-card-id="${Number(card.id)}"
                                                                type="button"
                                                            >
                                                                +
                                                            </button>

                                                        </div>
                                                    `
                                                    : ''
                                            }


                                            <button
                                                class="edit-card-button"
                                                data-card-id="${Number(card.id)}"
                                                type="button"
                                            >
                                                Modifica
                                            </button>

                                            <button
                                                class="delete-card-button"
                                                data-card-id="${Number(card.id)}"
                                                type="button"
                                            >
                                                Elimina
                                            </button>

                                        </div>
                                    `;
                                }).join('')}

                            </div>


                            <button
                                class="add-card-button"
                                data-list-id="${Number(list.id)}"
                                type="button"
                            >
                                + Aggiungi card
                            </button>

                        </div>
                    `;
                }).join('')}


                <div class="new-list-container">

                    <button
                        id="add-list-button"
                        type="button"
                    >
                        + Aggiungi lista
                    </button>

                </div>

            </div>
        `;


        /* BACK */

        document
            .getElementById('back-button')
            .addEventListener(
                'click',
                onBack
            );


        /* ADD MEMBER */

        const addMemberButton =
            document.getElementById(
                'add-member-button'
            );

        if (addMemberButton) {

            addMemberButton.addEventListener(
                'click',
                async () => {

                    const select =
                        document.getElementById(
                            'member-select'
                        );

                    const userId =
                        Number(select.value);

                    if (!userId) {
                        return;
                    }

                    await onAddMember(
                        userId
                    );
                }
            );
        }


        /* REMOVE MEMBER */

        document
            .querySelectorAll(
                '.remove-member-button'
            )
            .forEach(button => {

                button.addEventListener(
                    'click',
                    async () => {

                        const userId =
                            Number(
                                button.dataset.userId
                            );

                        await onRemoveMember(
                            userId
                        );
                    }
                );
            });


        /* ADD LIST */

        document
            .getElementById('add-list-button')
            .addEventListener(
                'click',
                async () => {

                    const title =
                        prompt(
                            'Titolo della nuova lista:'
                        );

                    if (
                        title === null ||
                        !title.trim()
                    ) {
                        return;
                    }

                    await onAddList(
                        title.trim()
                    );
                }
            );


        /* EDIT LIST */

        document
            .querySelectorAll(
                '.edit-list-button'
            )
            .forEach(button => {

                button.addEventListener(
                    'click',
                    async () => {

                        const listId =
                            Number(
                                button.dataset.listId
                            );

                        const list =
                            boardLists.find(
                                item =>
                                    Number(item.id) ===
                                    listId
                            );

                        if (!list) {
                            return;
                        }

                        const title =
                            prompt(
                                'Nuovo titolo della lista:',
                                list.title
                            );

                        if (
                            title === null ||
                            !title.trim()
                        ) {
                            return;
                        }

                        await onEditList(
                            listId,
                            title.trim(),
                            Number(list.position)
                        );
                    }
                );
            });


        /* DELETE LIST */

        document
            .querySelectorAll(
                '.delete-list-button'
            )
            .forEach(button => {

                button.addEventListener(
                    'click',
                    async () => {

                        const listId =
                            Number(
                                button.dataset.listId
                            );

                        const confirmed =
                            confirm(
                                'Sei sicuro di voler eliminare questa lista e tutte le sue card?'
                            );

                        if (!confirmed) {
                            return;
                        }

                        await onDeleteList(
                            listId
                        );
                    }
                );
            });


        /* ADD CARD */

        document
            .querySelectorAll(
                '.add-card-button'
            )
            .forEach(button => {

                button.addEventListener(
                    'click',
                    async () => {

                        const listId =
                            Number(
                                button.dataset.listId
                            );

                        const title =
                            prompt(
                                'Titolo della card:'
                            );

                        if (
                            title === null ||
                            !title.trim()
                        ) {
                            return;
                        }

                        const description =
                            prompt(
                                'Descrizione della card:',
                                ''
                            );

                        if (
                            description === null
                        ) {
                            return;
                        }

                        await onAddCard(
                            listId,
                            title.trim(),
                            description.trim()
                        );
                    }
                );
            });


        /* EDIT CARD */

        document
            .querySelectorAll(
                '.edit-card-button'
            )
            .forEach(button => {

                button.addEventListener(
                    'click',
                    async () => {

                        const cardId =
                            Number(
                                button.dataset.cardId
                            );

                        const card =
                            boardCards.find(
                                item =>
                                    Number(item.id) ===
                                    cardId
                            );

                        if (!card) {
                            return;
                        }

                        const title =
                            prompt(
                                'Titolo della card:',
                                card.title
                            );

                        if (
                            title === null ||
                            !title.trim()
                        ) {
                            return;
                        }

                        const description =
                            prompt(
                                'Descrizione della card:',
                                card.description || ''
                            );

                        if (
                            description === null
                        ) {
                            return;
                        }

                        await onEditCard(
                            cardId,
                            title.trim(),
                            description.trim(),
                            Number(card.position)
                        );
                    }
                );
            });


        /* DELETE CARD */

        document
            .querySelectorAll(
                '.delete-card-button'
            )
            .forEach(button => {

                button.addEventListener(
                    'click',
                    async () => {

                        const cardId =
                            Number(
                                button.dataset.cardId
                            );

                        const confirmed =
                            confirm(
                                'Sei sicuro di voler eliminare questa card?'
                            );

                        if (!confirmed) {
                            return;
                        }

                        await onDeleteCard(
                            cardId
                        );
                    }
                );
            });


        /* ADD ASSIGNMENT */

        document
            .querySelectorAll(
                '.add-assignment-button'
            )
            .forEach(button => {

                button.addEventListener(
                    'click',
                    async () => {

                        const cardId =
                            Number(
                                button.dataset.cardId
                            );

                        const select =
                            document.querySelector(
                                `.assignment-select[data-card-id="${cardId}"]`
                            );

                        if (!select) {
                            return;
                        }

                        const userId =
                            Number(
                                select.value
                            );

                        if (!userId) {
                            return;
                        }

                        await onAddAssignment(
                            cardId,
                            userId
                        );
                    }
                );
            });


        /* REMOVE ASSIGNMENT */

        document
            .querySelectorAll(
                '.remove-assignment-button'
            )
            .forEach(button => {

                button.addEventListener(
                    'click',
                    async () => {

                        const cardId =
                            Number(
                                button.dataset.cardId
                            );

                        const userId =
                            Number(
                                button.dataset.userId
                            );

                        await onRemoveAssignment(
                            cardId,
                            userId
                        );
                    }
                );
            });


        /* DRAG & DROP */

        this.enableCardDragAndDrop(
            onMoveCard
        );
    }


    /* =========================================================
       DRAG & DROP
       ========================================================= */

    enableCardDragAndDrop(
        onMoveCard
    ) {

        let draggedCardId = null;


        document
            .querySelectorAll('.card')
            .forEach(card => {

                card.addEventListener(
                    'dragstart',
                    event => {

                        draggedCardId =
                            Number(
                                card.dataset.cardId
                            );

                        card.classList.add(
                            'dragging'
                        );

                        event.dataTransfer
                            .effectAllowed =
                            'move';
                    }
                );


                card.addEventListener(
                    'dragend',
                    () => {

                        card.classList.remove(
                            'dragging'
                        );

                        draggedCardId =
                            null;
                    }
                );
            });


        document
            .querySelectorAll('.cards')
            .forEach(container => {

                container.addEventListener(
                    'dragover',
                    event => {

                        event.preventDefault();

                        event.dataTransfer
                            .dropEffect =
                            'move';
                    }
                );


                container.addEventListener(
                    'drop',
                    async event => {

                        event.preventDefault();

                        if (!draggedCardId) {
                            return;
                        }

                        const destinationListId =
                            Number(
                                container.dataset.listId
                            );

                        const otherCards = [
                            ...container
                                .querySelectorAll(
                                    '.card:not(.dragging)'
                                )
                        ];

                        let newPosition =
                            otherCards.length + 1;


                        for (
                            let index = 0;
                            index <
                            otherCards.length;
                            index++
                        ) {

                            const rect =
                                otherCards[index]
                                    .getBoundingClientRect();

                            const middle =
                                rect.top +
                                rect.height / 2;


                            if (
                                event.clientY <
                                middle
                            ) {

                                newPosition =
                                    index + 1;

                                break;
                            }
                        }


                        await onMoveCard(
                            draggedCardId,
                            destinationListId,
                            newPosition
                        );
                    }
                );
            });
    }


    /* =========================================================
       ERRORI
       ========================================================= */

    showError(message) {
        alert(message);
    }
}