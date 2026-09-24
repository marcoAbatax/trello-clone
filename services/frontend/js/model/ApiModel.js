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
}