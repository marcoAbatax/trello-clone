# Piano di Test

Questo documento descrive i principali test funzionali eseguiti sul progetto Trello Clone.

L'obiettivo è verificare il corretto funzionamento delle funzionalità principali dell'applicazione e dei vincoli di dominio.

---

# 1. Avvio dell'applicazione

## Test 1.1 - Avvio container

### Procedura

Eseguire:

```bash
docker compose up -d
```
I seguenti container devono risultare attivi:

- trello-db
- trello-backend
- trello-frontend

## Test 1.2 - Accesso frontend

Aprire http://localhost:3000 e la pagina principale verrà caricata correttamente

Successivamente possiamo effettuare i seguenti test come

- creazione board
- Modifca board
- Eliminazione Board
-gestione Utenti
- modifica utenti
- Eliminazione utente
- Gestione Liste
- Modifica lista
- Eliminazione lista
- gestione card
- Modifca card
- Elimnazione card
- Drag and drop
- Riordino della lista
- aggiunta membro lista
- modifca membro lista
- eliminazione membro lista

