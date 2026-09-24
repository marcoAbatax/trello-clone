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

        document
            .querySelectorAll('.board')
            .forEach(boardElement => {
                boardElement.addEventListener('click', () => {
                    onBoardClick(
                        boardElement.dataset.boardId
                    );
                });
            });
    }

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
        const membersHtml = members
            .map(member => `
                <div class="member">
                    <strong>${member.name}</strong>
                    <span>${member.email}</span>

                    <button
                        class="remove-member-button"
                        data-user-id="${member.id}"
                    >
                        Rimuovi
                    </button>
                </div>
            `)
            .join('');

        const memberIds = members.map(
            member => Number(member.id)
        );

        const availableUsers = users.filter(
            user => !memberIds.includes(Number(user.id))
        );

        const userOptions = availableUsers
            .map(user => `
                <option value="${user.id}">
                    ${user.name} - ${user.email}
                </option>
            `)
            .join('');

        const listsHtml = lists
            .map(list => {
                const listCards = cards.filter(
                    card => card.list_id == list.id
                );

                const cardsHtml = listCards
                    .map(card => {
                        const cardAssignments =
                            assignments[card.id] ?? [];

                        const assignedUserIds =
                            cardAssignments.map(
                                user => Number(user.id)
                            );

                        const availableMembers =
                            members.filter(
                                member =>
                                    !assignedUserIds.includes(
                                        Number(member.id)
                                    )
                            );

                        const assignmentsHtml =
                            cardAssignments
                                .map(user => `
                                    <div class="card-assignment">
                                        <span>
                                            ${user.name}
                                        </span>

                                        <button
                                            class="remove-assignment-button"
                                            data-card-id="${card.id}"
                                            data-user-id="${user.id}"
                                        >
                                            ×
                                        </button>
                                    </div>
                                `)
                                .join('');

                        const assignmentOptions =
                            availableMembers
                                .map(member => `
                                    <option value="${member.id}">
                                        ${member.name}
                                    </option>
                                `)
                                .join('');

                        return `
                            <div
                                class="card"
                                draggable="true"
                                data-card-id="${card.id}"
                            >
                                <h4>${card.title}</h4>

                                <p>
                                    ${card.description ?? ''}
                                </p>

                                <div class="card-assignments">
                                    ${assignmentsHtml}
                                </div>

                                <div class="assignment-controls">
                                    <select
                                        class="assignment-select"
                                        data-card-id="${card.id}"
                                    >
                                        <option value="">
                                            Assegna membro
                                        </option>

                                        ${assignmentOptions}
                                    </select>

                                    <button
                                        class="add-assignment-button"
                                        data-card-id="${card.id}"
                                    >
                                        Assegna
                                    </button>
                                </div>

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
                        `;
                    })
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

            <section class="board-members">
                <h3>Membri della board</h3>

                <div class="members">
                    ${membersHtml}
                </div>

                <div class="add-member-container">
                    <select id="member-select">
                        <option value="">
                            Seleziona un utente
                        </option>

                        ${userOptions}
                    </select>

                    <button id="add-member-button">
                        Aggiungi membro
                    </button>
                </div>
            </section>

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

        document
            .getElementById('back-button')
            .addEventListener('click', () => {
                onBack();
            });

        document
            .querySelectorAll('.add-card-button')
            .forEach(button => {
                button.addEventListener('click', () => {
                    const listId = button.dataset.listId;

                    const form = document.querySelector(
                        `[data-form-list-id="${listId}"]`
                    );

                    form.style.display = 'block';
                });
            });

        document
            .querySelectorAll('.save-card-button')
            .forEach(button => {
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

        document
            .querySelectorAll('.delete-card-button')
            .forEach(button => {
                button.addEventListener('click', () => {
                    onDeleteCard(
                        button.dataset.cardId
                    );
                });
            });

        document
            .querySelectorAll('.edit-card-button')
            .forEach(button => {
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

        let draggedCardId = null;

        document
            .querySelectorAll('.card')
            .forEach(cardElement => {
                cardElement.addEventListener('dragstart', () => {
                    draggedCardId =
                        cardElement.dataset.cardId;
                });
            });

        document
            .querySelectorAll('.list')
            .forEach(listElement => {
                listElement.addEventListener(
                    'dragover',
                    event => {
                        event.preventDefault();
                    }
                );

                listElement.addEventListener(
                    'drop',
                    () => {
                        const listId =
                            listElement.dataset.listId;

                        const cardsInDestinationList =
                            listElement.querySelectorAll(
                                '.card'
                            );

                        const position =
                            cardsInDestinationList.length + 1;

                        onMoveCard(
                            draggedCardId,
                            listId,
                            position
                        );
                    }
                );
            });

        document
            .getElementById('add-list-button')
            .addEventListener('click', () => {
                document
                    .getElementById('add-list-form')
                    .style.display = 'block';
            });

        document
            .getElementById('save-list-button')
            .addEventListener('click', () => {
                const title = document
                    .getElementById('list-title-input')
                    .value;

                onAddList(title);
            });

        document
            .querySelectorAll('.edit-list-button')
            .forEach(button => {
                button.addEventListener('click', () => {
                    const listId =
                        button.dataset.listId;

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

        document
            .querySelectorAll('.delete-list-button')
            .forEach(button => {
                button.addEventListener('click', () => {
                    onDeleteList(
                        button.dataset.listId
                    );
                });
            });

        document
            .getElementById('add-member-button')
            .addEventListener('click', () => {
                const select =
                    document.getElementById(
                        'member-select'
                    );

                const userId = select.value;

                if (userId === '') {
                    alert('Seleziona un utente');
                    return;
                }

                onAddMember(userId);
            });

        document
            .querySelectorAll('.remove-member-button')
            .forEach(button => {
                button.addEventListener('click', () => {
                    onRemoveMember(
                        button.dataset.userId
                    );
                });
            });

        document
            .querySelectorAll('.add-assignment-button')
            .forEach(button => {
                button.addEventListener('click', () => {
                    const cardId =
                        button.dataset.cardId;

                    const select = document.querySelector(
                        `.assignment-select[data-card-id="${cardId}"]`
                    );

                    const userId = select.value;

                    if (userId === '') {
                        alert(
                            'Seleziona un membro da assegnare'
                        );
                        return;
                    }

                    onAddAssignment(
                        cardId,
                        userId
                    );
                });
            });

        document
            .querySelectorAll('.remove-assignment-button')
            .forEach(button => {
                button.addEventListener('click', () => {
                    onRemoveAssignment(
                        button.dataset.cardId,
                        button.dataset.userId
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