# Progettazione del database

## 1. Obiettivo

Il database ha il compito di garantire la persistenza dei dati dell'applicazione

Le principali entità da rappresentare sono:

-User
-Board
-BoardMember
-BoardList
-Card
-CardAssignment

## 2. Tabelle

### User

Rappresenta un utente dell'applicazione

Attributi:

-id
-name
-email

### Board

Rappresenta una bacheca Kenban

Attributi:

-id
-name

### BoardMember

rappresenta l'appartenenza di un utente a una board

Attributi:

-board_id
-user_id

### BoardList

Rappresenta una lista contenuta all'interno di una board

Attributi:

-id
-board_id
-title
-position

### Card

Rappresenta un'attività contenuta all'interno di una lista

Attributi:

-id
-list_id
-title
-description
-position

### CardAssignment

Rappresenta l'assegnazione di una card a un utente

Attributi:

-card_id
-user_id

## 3. Chiavi e relazioni

### User

Primary Key:

-id

### Board

Primary Key:

-id

### BoardMember

Primary Key Ccomposta:

-board_id
-user_id

Foreign Key:

-board_id -> Board(id)
-user_id -> User(id)

### BoardList

Primary Key:

-id

Foreign key:

-board_id -> Board(id)

### Card

Primary Key

-id

Foreign Key

-list_id -> BoardList(id)

### CardAssignment

Primary Key composta:

-card_id
-user_id

Foreign Key

-card_id -> Card(id)
-user_id -> User(id)

