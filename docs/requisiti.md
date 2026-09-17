## Clone di trello

## 1. Descrizione progetto

Il progetto consiste nello sviluppo di un'applicazione web che replica le funzionalità di Trello.

L'applicazione permette di organizzare le attività attraverso bacheche Kanban composte da liste e card.

Le prerogative sono che ogni Board può essere condivisa con più utenti. Le card presentano i task da svolgere e possono essere spostate tra diverse liste tramite drag .

Le modifiche degli utenti devono essere persistenti e salvate tramite il backend dell'applicazione.

## 2. Obiettivo del sistema

L'obiettivo del sistema è quello di permettere ad un gruppo di utenti di organizzare e monitorare le attività di un progetto attraverso board liste e card.

Il sistema deve permettere di:

-creare e gestire board
-creare e gestire liste
-creare e gestire card
-spsotare card tra liste
-assegnare card ai membri di una board
-salvare in modo persistente le modifiche

## 3. Requisiti funzionali

### RF01 - Visualizzazione delle Board
Il sistema deve permettere all'utente di visualizzare le board alle quali ha accesso.

### RF02 - Creazione di una Board
Il sistema deve permettere di creare una nuova board

### RF03 - Modifica di una Board
Il sistema deve permettere di modificare una board

### RF04 - Eliminazione Board
Il sistema deve permettere di eliminare una board.

### RF05 - Gestione dei membri
Il sistema deve permettere di aggiungere membri a una board

### RF06 - Creazione di una lista
Il sistema deve permettere di creare una lista all'interno di una board

### RF07 - Modifica di una lista
Il sistema deve permettere di modificare una lista

### RF08 - Eliminazione di una lista
Il sistema deve permettere eliminare la lista

### RF09 - Creazione di una card
Il sistema deve permettere di creare una card all' interno di una lista

### RF10 - Modifica di una card
Il sistema deve permettere di modificare una card

### RF11 - Eliminazione di una card
Il sistema deve permettere di eliminare una card

### RF12 - Spostamenteo di una card
Il sistema deve permettere di spsotare una card da una lista ad un altra tramite drag-and-drop

### RF13 - Persistenza dello spostamento
Il sistema deve salvare una nuova lista e la nuova posizione della card

### RF14 - Asseganzione di una card
Il sistema deve permettere di assegnare una card a uno o più membri della board

## 4. Requisiti non funzionali

### RNF01 - Frontend
Il Frontend deve essere sviluppato utilizzando HTML,CSS e JavaScript

### RNF02 - Backend
Il backend deve essere sviluppato con l'utilizzo di php

### RNF03 - Comunicazione frontend-backend
Forntend e Backend devono comunicare tramite RESTful API di tipo CRUD.

### RNF04 - Architettura
L'applicazione deve rispettare il modello Model - view - Presenter

### RNF05 - Separazione frontend - backend
Il backend PHP non deve generare codice HTML

### RNF06 - Framework
Non devono essere utilizzati framework architetturali

### RNF07 - Persistenza
I dati devono essere memorizzati in modo persistente tramite database

### RNF08 - Versionamento
Il progetto usa github

### RNF09 - Containerizzazione
L'esecuzione tramite container deve essere eseguita tramite docker