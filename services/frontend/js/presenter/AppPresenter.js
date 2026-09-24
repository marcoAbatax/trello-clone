import { ApiModel } from '../model/ApiModel.js?v=2';
import { AppView } from '../view/AppView.js';

class AppPresenter {

    constructor(model, view) {
        this.model = model;
        this.view = view;
        this.boards = [];
    }

    async start() {
        this.view.showLoading();

        try {
            this.boards = await this.model.getBoards();
            this.showBoards();

        } catch (error) {
            this.view.showError(error.message);
        }
    }

    showBoards() {
        this.view.showBoards(

            this.boards,

            async boardId => {
                await this.openBoard(boardId);
            },

            async name => {
                await this.createBoard(name);
            },

            async (boardId, name) => {
                await this.updateBoard(
                    boardId,
                    name
                );
            },

            async boardId => {
                await this.deleteBoard(boardId);
            }
        );
    }

    async createBoard(name) {
        try {
            if (name.trim() === '') {
                alert('Inserisci un nome per la board');
                return;
            }

            await this.model.createBoard(name);

            this.boards =
                await this.model.getBoards();

            this.showBoards();

        } catch (error) {
            this.view.showError(error.message);
        }
    }

    async updateBoard(
        boardId,
        name
    ) {
        try {
            if (name.trim() === '') {
                alert(
                    'Il nome della board non può essere vuoto'
                );
                return;
            }

            await this.model.updateBoard(
                Number(boardId),
                name
            );

            this.boards =
                await this.model.getBoards();

            this.showBoards();

        } catch (error) {
            this.view.showError(error.message);
        }
    }

    async deleteBoard(boardId) {
        try {
            await this.model.deleteBoard(
                Number(boardId)
            );

            this.boards =
                await this.model.getBoards();

            this.showBoards();

        } catch (error) {
            this.view.showError(error.message);
        }
    }

    async openBoard(boardId) {
        try {
            const lists =
                await this.model.getLists();

            const cards =
                await this.model.getCards();

            const members =
                await this.model.getBoardMembers(
                    boardId
                );

            const users =
                await this.model.getUsers();

            const assignments = {};

            for (const card of cards) {
                assignments[card.id] =
                    await this.model.getCardAssignments(
                        card.id
                    );
            }

            const boardLists = lists.filter(
                list => list.board_id == boardId
            );

            const board = this.boards.find(
                board => board.id == boardId
            );

            this.view.showBoard(
                board,
                boardLists,
                cards,
                members,
                users,
                assignments,

                () => {
                    this.showBoards();
                },

                async (
                    listId,
                    title,
                    description
                ) => {
                    await this.addCard(
                        boardId,
                        listId,
                        title,
                        description
                    );
                },

                async cardId => {
                    await this.deleteCard(
                        boardId,
                        cardId
                    );
                },

                async (
                    cardId,
                    title,
                    description,
                    position
                ) => {
                    await this.editCard(
                        boardId,
                        cardId,
                        title,
                        description,
                        position
                    );
                },

                async (
                    cardId,
                    listId,
                    position
                ) => {
                    await this.moveCard(
                        boardId,
                        cardId,
                        listId,
                        position
                    );
                },

                async title => {
                    await this.addList(
                        boardId,
                        title
                    );
                },

                async (
                    listId,
                    title,
                    position
                ) => {
                    await this.editList(
                        boardId,
                        listId,
                        title,
                        position
                    );
                },

                async listId => {
                    await this.deleteList(
                        boardId,
                        listId
                    );
                },

                async userId => {
                    await this.addBoardMember(
                        boardId,
                        userId
                    );
                },

                async userId => {
                    await this.removeBoardMember(
                        boardId,
                        userId
                    );
                },

                async (
                    cardId,
                    userId
                ) => {
                    await this.addCardAssignment(
                        boardId,
                        cardId,
                        userId
                    );
                },

                async (
                    cardId,
                    userId
                ) => {
                    await this.removeCardAssignment(
                        boardId,
                        cardId,
                        userId
                    );
                }
            );

        } catch (error) {
            this.view.showError(error.message);
        }
    }

    async addCard(
        boardId,
        listId,
        title,
        description
    ) {
        try {
            if (title.trim() === '') {
                alert(
                    'Inserisci un titolo per la card'
                );
                return;
            }

            const cards =
                await this.model.getCards();

            const cardsInList = cards.filter(
                card => card.list_id == listId
            );

            const position =
                cardsInList.length + 1;

            await this.model.createCard(
                Number(listId),
                title,
                description,
                position
            );

            await this.openBoard(boardId);

        } catch (error) {
            this.view.showError(error.message);
        }
    }

    async deleteCard(
        boardId,
        cardId
    ) {
        try {
            await this.model.deleteCard(
                Number(cardId)
            );

            await this.openBoard(boardId);

        } catch (error) {
            this.view.showError(error.message);
        }
    }

    async editCard(
        boardId,
        cardId,
        title,
        description,
        position
    ) {
        try {
            if (title.trim() === '') {
                alert(
                    'Il titolo non può essere vuoto'
                );
                return;
            }

            await this.model.updateCard(
                Number(cardId),
                title,
                description,
                Number(position)
            );

            await this.openBoard(boardId);

        } catch (error) {
            this.view.showError(error.message);
        }
    }

    async moveCard(
        boardId,
        cardId,
        listId,
        position
    ) {
        try {
            await this.model.moveCard(
                Number(cardId),
                Number(listId),
                Number(position)
            );

            await this.openBoard(boardId);

        } catch (error) {
            this.view.showError(error.message);
        }
    }

    async addList(
        boardId,
        title
    ) {
        try {
            if (title.trim() === '') {
                alert(
                    'Inserisci un titolo per la lista'
                );
                return;
            }

            const lists =
                await this.model.getLists();

            const boardLists = lists.filter(
                list => list.board_id == boardId
            );

            const position =
                boardLists.length + 1;

            await this.model.createList(
                Number(boardId),
                title,
                position
            );

            await this.openBoard(boardId);

        } catch (error) {
            this.view.showError(error.message);
        }
    }

    async editList(
        boardId,
        listId,
        title,
        position
    ) {
        try {
            if (title.trim() === '') {
                alert(
                    'Il titolo della lista non può essere vuoto'
                );
                return;
            }

            await this.model.updateList(
                Number(listId),
                title,
                Number(position)
            );

            await this.openBoard(boardId);

        } catch (error) {
            this.view.showError(error.message);
        }
    }

    async deleteList(
        boardId,
        listId
    ) {
        try {
            await this.model.deleteList(
                Number(listId)
            );

            await this.openBoard(boardId);

        } catch (error) {
            this.view.showError(error.message);
        }
    }

    async addBoardMember(
        boardId,
        userId
    ) {
        try {
            await this.model.addBoardMember(
                Number(boardId),
                Number(userId)
            );

            await this.openBoard(boardId);

        } catch (error) {
            this.view.showError(error.message);
        }
    }

    async removeBoardMember(
        boardId,
        userId
    ) {
        try {
            await this.model.removeBoardMember(
                Number(boardId),
                Number(userId)
            );

            await this.openBoard(boardId);

        } catch (error) {
            this.view.showError(error.message);
        }
    }

    async addCardAssignment(
        boardId,
        cardId,
        userId
    ) {
        try {
            await this.model.addCardAssignment(
                Number(cardId),
                Number(userId)
            );

            await this.openBoard(boardId);

        } catch (error) {
            this.view.showError(error.message);
        }
    }

    async removeCardAssignment(
        boardId,
        cardId,
        userId
    ) {
        try {
            await this.model.removeCardAssignment(
                Number(cardId),
                Number(userId)
            );

            await this.openBoard(boardId);

        } catch (error) {
            this.view.showError(error.message);
        }
    }
}

const model = new ApiModel();
const view = new AppView();

const presenter = new AppPresenter(
    model,
    view
);

presenter.start();