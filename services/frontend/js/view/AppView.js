export class AppView {

    constructor() {
        this.app = document.getElementById('app');
    }

    showLoading() {
        this.app.innerHTML = `
            <p>Caricamento...</p>
        `;
    }

    showBoards(boards, onBoardClick) {
        if (boards.length === 0) {
            this.app.innerHTML = `
                <p>Nessuna board disponibile.</p>
            `;

            return;
        }

        const boardsHtml = boards
            .map(board => `
                <div class="board" data-board-id="${board.id}">
                    <h2>${board.name}</h2>
                    <p>ID: ${board.id}</p>
                </div>
            `)
            .join('');

        this.app.innerHTML = `
            <h2>Le board</h2>

            <div class="boards">
                ${boardsHtml}
            </div>
        `;

        const boardElements = document.querySelectorAll('.board');

        boardElements.forEach(boardElement => {
            boardElement.addEventListener('click', () => {
                const boardId = boardElement.dataset.boardId;

                onBoardClick(boardId);
            });
        });
    }

    showBoard(
        board,
        lists,
        cards,
        onBack,
        onAddCard,
        onDeleteCard,
        onEditCard,
        onMoveCard,
        onAddList,
        onEditList,
        onDeleteList
    ) {
        const listsHtml = lists
            .map(list => {
                const listCards = cards.filter(
                    card => card.list_id == list.id
                );

                const cardsHtml = listCards
                    .map(card => `
                        <div
                            class="card"
                            draggable="true"
                            data-card-id="${card.id}"
                        >
                            <h4>${card.title}</h4>

                            <p>
                                ${card.description ?? ''}
                            </p>

                            <button
                                class="edit-card-button"
                                data-card-id="${card.id}"
                            >
                                Modifica
                            </button>

                            <button
                                class="delete-card-button"
                                data-card-id="${card.id}"
                            >
                                Elimina
                            </button>
                        </div>
                    `)
                    .join('');

                return `
                    <div
                        class="list"
                        data-list-id="${list.id}"
                    >
                        <h3>${list.title}</h3>

                        <button
                            class="edit-list-button"
                            data-list-id="${list.id}"
                        >
                            Modifica lista
                        </button>

                        <button
                            class="delete-list-button"
                            data-list-id="${list.id}"
                        >
                            Elimina lista
                        </button>

                        <div class="cards">
                            ${cardsHtml}
                        </div>

                        <button
                            class="add-card-button"
                            data-list-id="${list.id}"
                        >
                            + Aggiungi card
                        </button>

                        <div
                            class="add-card-form"
                            data-form-list-id="${list.id}"
                            style="display: none;"
                        >
                            <input
                                type="text"
                                class="card-title-input"
                                placeholder="Titolo card"
                            >

                            <textarea
                                class="card-description-input"
                                placeholder="Descrizione"
                            ></textarea>

                            <button
                                class="save-card-button"
                                data-list-id="${list.id}"
                            >
                                Salva
                            </button>
                        </div>
                    </div>
                `;
            })
            .join('');

        this.app.innerHTML = `
            <button id="back-button">
                ← Torna alle board
            </button>

            <h2>${board.name}</h2>

            <div class="lists">
                ${listsHtml}

                <div class="new-list-container">
                    <button id="add-list-button">
                        + Aggiungi lista
                    </button>

                    <div
                        id="add-list-form"
                        style="display: none;"
                    >
                        <input
                            type="text"
                            id="list-title-input"
                            placeholder="Titolo lista"
                        >

                        <button id="save-list-button">
                            Salva lista
                        </button>
                    </div>
                </div>
            </div>
        `;

        const backButton = document.getElementById('back-button');

        backButton.addEventListener('click', () => {
            onBack();
        });

        const addButtons = document.querySelectorAll(
            '.add-card-button'
        );

        addButtons.forEach(button => {
            button.addEventListener('click', () => {
                const listId = button.dataset.listId;

                const form = document.querySelector(
                    `[data-form-list-id="${listId}"]`
                );

                form.style.display = 'block';
            });
        });

        const saveButtons = document.querySelectorAll(
            '.save-card-button'
        );

        saveButtons.forEach(button => {
            button.addEventListener('click', () => {
                const listId = button.dataset.listId;

                const form = document.querySelector(
                    `[data-form-list-id="${listId}"]`
                );

                const title = form
                    .querySelector('.card-title-input')
                    .value;

                const description = form
                    .querySelector('.card-description-input')
                    .value;

                onAddCard(
                    listId,
                    title,
                    description
                );
            });
        });

        const deleteCardButtons = document.querySelectorAll(
            '.delete-card-button'
        );

        deleteCardButtons.forEach(button => {
            button.addEventListener('click', () => {
                const cardId = button.dataset.cardId;

                onDeleteCard(cardId);
            });
        });

        const editCardButtons = document.querySelectorAll(
            '.edit-card-button'
        );

        editCardButtons.forEach(button => {
            button.addEventListener('click', () => {
                const cardId = button.dataset.cardId;

                const card = cards.find(
                    card => card.id == cardId
                );

                const newTitle = prompt(
                    'Nuovo titolo:',
                    card.title
                );

                if (newTitle === null) {
                    return;
                }

                const newDescription = prompt(
                    'Nuova descrizione:',
                    card.description ?? ''
                );

                if (newDescription === null) {
                    return;
                }

                onEditCard(
                    cardId,
                    newTitle,
                    newDescription,
                    card.position
                );
            });
        });

        const cardElements = document.querySelectorAll('.card');

        let draggedCardId = null;

        cardElements.forEach(cardElement => {
            cardElement.addEventListener('dragstart', () => {
                draggedCardId = cardElement.dataset.cardId;
            });
        });

        const listElements = document.querySelectorAll('.list');

        listElements.forEach(listElement => {
            listElement.addEventListener('dragover', event => {
                event.preventDefault();
            });

            listElement.addEventListener('drop', () => {
                const listId = listElement.dataset.listId;

                const cardsInDestinationList =
                    listElement.querySelectorAll('.card');

                const position =
                    cardsInDestinationList.length + 1;

                onMoveCard(
                    draggedCardId,
                    listId,
                    position
                );
            });
        });

        const addListButton = document.getElementById(
            'add-list-button'
        );

        const addListForm = document.getElementById(
            'add-list-form'
        );

        addListButton.addEventListener('click', () => {
            addListForm.style.display = 'block';
        });

        const saveListButton = document.getElementById(
            'save-list-button'
        );

        saveListButton.addEventListener('click', () => {
            const title = document.getElementById(
                'list-title-input'
            ).value;

            onAddList(title);
        });

        const editListButtons = document.querySelectorAll(
            '.edit-list-button'
        );

        editListButtons.forEach(button => {
            button.addEventListener('click', () => {
                const listId = button.dataset.listId;

                const list = lists.find(
                    list => list.id == listId
                );

                const newTitle = prompt(
                    'Nuovo titolo della lista:',
                    list.title
                );

                if (newTitle === null) {
                    return;
                }

                onEditList(
                    listId,
                    newTitle,
                    list.position
                );
            });
        });

        const deleteListButtons = document.querySelectorAll(
            '.delete-list-button'
        );

        deleteListButtons.forEach(button => {
            button.addEventListener('click', () => {
                const listId = button.dataset.listId;

                onDeleteList(listId);
            });
        });
    }

    showError(message) {
        this.app.innerHTML = `
            <p>Errore: ${message}</p>
        `;
    }
}