import { ApiModel } from '../model/ApiModel.js';
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
            this.view.showError(
                error.message
            );
        }
    }

    showBoards() {
        this.view.showBoards(
            this.boards,
            async boardId => {
                await this.openBoard(boardId);
            }
        );
    }

    async openBoard(boardId) {
        try {
            const lists = await this.model.getLists();
            const cards = await this.model.getCards();

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
                () => {
                    this.showBoards();
                },
                async (listId, title, description) => {
                    await this.addCard(
                        boardId,
                        listId,
                        title,
                        description
                    );
                }
            );

        } catch (error) {
            this.view.showError(
                error.message
            );
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
                alert('Inserisci un titolo per la card');
                return;
            }

            const cards = await this.model.getCards();

            const cardsInList = cards.filter(
                card => card.list_id == listId
            );

            const position = cardsInList.length + 1;

            await this.model.createCard(
                Number(listId),
                title,
                description,
                position
            );

            await this.openBoard(boardId);

        } catch (error) {
            this.view.showError(
                error.message
            );
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