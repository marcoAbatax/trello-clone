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

    showBoard(board, lists, cards, onBack, onAddCard) {
        const listsHtml = lists
            .map(list => {
                const listCards = cards.filter(
                    card => card.list_id == list.id
                );

                const cardsHtml = listCards
                    .map(card => `
                        <div class="card">
                            <h4>${card.title}</h4>

                            <p>
                                ${card.description ?? ''}
                            </p>
                        </div>
                    `)
                    .join('');

                return `
                    <div class="list">

                        <h3>${list.title}</h3>

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
    }

    showError(message) {
        this.app.innerHTML = `
            <p>Errore: ${message}</p>
        `;
    }
}