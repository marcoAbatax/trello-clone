# Trello Clone

L'obiettivo del progetto è sviluppare un,applicazione web ispirata a trello che mi permetta di gestire board, liste, card, utenti e assegnazioni tramite un'architettira client-server.

Il progetto utilizza:

- **HTML**
- **CSS**
- **PHP**
- **Javascript**
- **MySQL**
- **Docker**
- **REST API**
- architettura **MVP lato frontend**
- architettura **Controller + Gateway lato backend**

---

## Funzionalità

L'applicazione permette di:

## Board

- visualizzare tute le board;
- creare una nuova board;
- modifcare il nome di una board;
- eliminare una board;
- aprire una board per viualizzare il contenuto

## Liste

All'interno di una Board è possibile:

- visualizzare le liste;
- creare una nuova lista;
- modifcare il titolo di una lista;
- eliminare una lista;
- mantenere la psozione delle liste nel database;

### Card

Per ogni lista è possibile:

- crare una nuova card;
- modificare titolo e descrizione;
- eliminare una card;
- spostare una card da uina lista ad un'altra;
- riordinare le card all'interno della stessa lista tramite drag & drop;
- mantenere le poszioni delle card coerenti nel database;

### Utenti

E' possibile:

- visualizzare gli utenti;
- creare nuovi utenti;
- modifcare nome ed email;
- eliminare utenti;

### Membri delle board

Per ogni board è possibile;

- visualizzare i membri;
- aggiungere un utente come membro;
- rimuovere un membro;

Quando un utente viene rimosso dalla board, vengono elimnare automaticamente anche le sue eventuali assegnazioni alle card appartenenti a quella baord.

### Assegnazioni delle card

Una card può essere assegnata a uno o più membri della board.

E' possibile:

- visualizzare gli utenti assegnati ad una card;
- asseganre un membro;
- rimuovere un assegantario.

Un utente può essere assegnato a una card solamente se è membro della board a cui appartiene la card.

### Interfaccia

Il frontend include inoltre:

- drag & drop delle card;
- conferme prima delle eliminazioni-
- layout responsive;
- interfaccia grafica ispirata a trello

---

# Architettura

Il progetto utilizza un' architettura separata tra frontend, backend e database.

 text
Frontend
   |
   | HTTP / REST
   v
Backend PHP
   |
   v
MySQL

### Frontend

View --> presenter --> model --> REST API

Il model gestisce la comunicazione con il backend tramite fetch

La view gestisce la visualizzazione dell'interfaccia e gli eventi dell'utente

Il presente collega View e Model e contiene la logica applicativa del frontend

### Backend

Controller --> Gateway --> Database

Il controller riceve richieste HTTP e utilizza i Gateway per accedere al database

### Database

Il database utilizzato è MYSQL e le entità principali sono:

-User
-Board
-BoardMember
-BoardList
-Card
-CardAssignment

### API REST

Il backend utilizza API REST per la gestione delle risorse

### Avvio progetto

Per utilizzare il progetto è necessario l'uso di Git e Docker Desktop

-Clonare la repository ed entrare nella cartella trello clone

- configurare le variabili d'ambiente

- Avviare Docker con docker compose up -d -- build

-Si accede al frontend dell'applicazione con http://localhost:3000

- Si accede al Backend con http://localhost:8080

- database MySql localhost:3306

Per l'arresto del progetto si usa docker compose down

per il riavvio sempre docekr compose up -d