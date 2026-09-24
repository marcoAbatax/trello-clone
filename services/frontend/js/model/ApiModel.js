export class ApiModel {

    constructor() {
        this.baseUrl = 'http://localhost:8080';
    }

    async getBoards() {
        const response = await fetch(
            `${this.baseUrl}/boards`
        );

        if (!response.ok) {
            throw new Error(
                'Errore durante il caricamento delle board'
            );
        }

        return await response.json();
    }

    async getLists() {
    const response = await fetch(
        `${this.baseUrl}/lists`
    );

    if (!response.ok) {
        throw new Error(
            'Errore durante il caricamento delle liste'
        );
    }

    return await response.json();
}

async getCards() {
    const response = await fetch(
        `${this.baseUrl}/cards`
    );

    if (!response.ok) {
        throw new Error(
            'Errore durante il caricamento delle card'
        );
    }

    return await response.json();
}

async createCard(listId, title, description, position) {
    const response = await fetch(
        `${this.baseUrl}/cards`,
        {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                list_id: listId,
                title: title,
                description: description,
                position: position
            })
        }
    );

    if (!response.ok) {
        throw new Error(
            'Errore durante la creazione della card'
        );
    }

    return await response.json();
}

async deleteCard(cardId) {
    const response = await fetch(
        `${this.baseUrl}/cards/${cardId}`,
        {
            method: 'DELETE'
        }
    );

    if (!response.ok) {
        throw new Error(
            'Errore durante l\'eliminazione della card'
        );
    }

    return await response.json();
}

async updateCard(cardId, title, description, position) {
    const response = await fetch(
        `${this.baseUrl}/cards/${cardId}`,
        {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                title: title,
                description: description,
                position: position
            })
        }
    );

    if (!response.ok) {
        throw new Error(
            'Errore durante la modifica della card'
        );
    }

    return await response.json();
}

async moveCard(cardId, listId, position) {
    const response = await fetch(
        `${this.baseUrl}/cards/${cardId}/move`,
        {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                list_id: listId,
                position: position
            })
        }
    );

    if (!response.ok) {
        throw new Error(
            'Errore durante lo spostamento della card'
        );
    }

    return await response.json();
}

async createList(boardId, title, position) {
    const response = await fetch(
        `${this.baseUrl}/lists`,
        {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                board_id: boardId,
                title: title,
                position: position
            })
        }
    );

    if (!response.ok) {
        throw new Error(
            'Errore durante la creazione della lista'
        );
    }

    return await response.json();
}

async updateList(listId, title, position) {
    const response = await fetch(
        `${this.baseUrl}/lists/${listId}`,
        {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                title: title,
                position: position
            })
        }
    );

    if (!response.ok) {
        throw new Error(
            'Errore durante la modifica della lista'
        );
    }

    return await response.json();
}

async deleteList(listId) {
    const response = await fetch(
        `${this.baseUrl}/lists/${listId}`,
        {
            method: 'DELETE'
        }
    );

    if (!response.ok) {
        throw new Error(
            'Errore durante l\'eliminazione della lista'
        );
    }

    return await response.json();
}

async getBoardMembers(boardId) {
    const response = await fetch(
        `${this.baseUrl}/boards/${boardId}/members`
    );

    if (!response.ok) {
        throw new Error(
            'Errore durante il caricamento dei membri della board'
        );
    }

    return await response.json();
}

async addBoardMember(boardId, userId) {
    const response = await fetch(
        `${this.baseUrl}/boards/${boardId}/members`,
        {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                user_id: userId
            })
        }
    );

    if (!response.ok) {
        throw new Error(
            'Errore durante l\'aggiunta del membro'
        );
    }

    return await response.json();
}

async removeBoardMember(boardId, userId) {
    const response = await fetch(
        `${this.baseUrl}/boards/${boardId}/members/${userId}`,
        {
            method: 'DELETE'
        }
    );

    if (!response.ok) {
        throw new Error(
            'Errore durante la rimozione del membro'
        );
    }

    return await response.json();
}

async getUsers() {
    const response = await fetch(
        `${this.baseUrl}/users`
    );

    if (!response.ok) {
        throw new Error(
            'Errore durante il caricamento degli utenti'
        );
    }

    return await response.json();
}

async getCardAssignments(cardId) {
    const response = await fetch(
        `${this.baseUrl}/cards/${cardId}/assignments`
    );

    if (!response.ok) {
        throw new Error(
            'Errore durante il caricamento degli assegnatari'
        );
    }

    return await response.json();
}

async addCardAssignment(cardId, userId) {
    const response = await fetch(
        `${this.baseUrl}/cards/${cardId}/assignments`,
        {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                user_id: userId
            })
        }
    );

    if (!response.ok) {
        throw new Error(
            'Errore durante l\'assegnazione dell\'utente'
        );
    }

    return await response.json();
}

async removeCardAssignment(cardId, userId) {
    const response = await fetch(
        `${this.baseUrl}/cards/${cardId}/assignments/${userId}`,
        {
            method: 'DELETE'
        }
    );

    if (!response.ok) {
        throw new Error(
            'Errore durante la rimozione dell\'assegnazione'
        );
    }

    return await response.json();
}

async createBoard(name) {
    const response = await fetch(
        `${this.baseUrl}/boards`,
        {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                name: name
            })
        }
    );

    if (!response.ok) {
        throw new Error(
            'Errore durante la creazione della board'
        );
    }

    return await response.json();
}

async updateBoard(boardId, name) {
    const response = await fetch(
        `${this.baseUrl}/boards/${boardId}`,
        {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                name: name
            })
        }
    );

    if (!response.ok) {
        throw new Error(
            'Errore durante la modifica della board'
        );
    }

    return await response.json();
}

async deleteBoard(boardId) {
    const response = await fetch(
        `${this.baseUrl}/boards/${boardId}`,
        {
            method: 'DELETE'
        }
    );

    if (!response.ok) {
        throw new Error(
            'Errore durante l\'eliminazione della board'
        );
    }

    return await response.json();
}

async createUser(name, email) {
    const response = await fetch(
        `${this.baseUrl}/users`,
        {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                name: name,
                email: email
            })
        }
    );

    if (!response.ok) {
        throw new Error(
            'Errore durante la creazione dell\'utente'
        );
    }

    return await response.json();
}

async updateUser(userId, name, email) {
    const response = await fetch(
        `${this.baseUrl}/users/${userId}`,
        {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                name: name,
                email: email
            })
        }
    );

    if (!response.ok) {
        throw new Error(
            'Errore durante la modifica dell\'utente'
        );
    }

    return await response.json();
}

async deleteUser(userId) {
    const response = await fetch(
        `${this.baseUrl}/users/${userId}`,
        {
            method: 'DELETE'
        }
    );

    if (!response.ok) {
        throw new Error(
            'Errore durante l\'eliminazione dell\'utente'
        );
    }

    return await response.json();
}
}