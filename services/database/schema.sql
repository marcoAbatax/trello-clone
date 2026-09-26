CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE
);


CREATE TABLE boards (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL
);


CREATE TABLE board_members (
    board_id INT NOT NULL,
    user_id INT NOT NULL,

    PRIMARY KEY (board_id, user_id),

    FOREIGN KEY (board_id)
        REFERENCES boards(id)
        ON DELETE CASCADE,

    FOREIGN KEY (user_id)
        REFERENCES users(id)
        ON DELETE CASCADE
);


CREATE TABLE board_lists (
    id INT AUTO_INCREMENT PRIMARY KEY,
    board_id INT NOT NULL,
    title VARCHAR(150) NOT NULL,
    position INT NOT NULL,

    FOREIGN KEY (board_id)
        REFERENCES boards(id)
        ON DELETE CASCADE
);


CREATE TABLE cards (
    id INT AUTO_INCREMENT PRIMARY KEY,
    list_id INT NOT NULL,
    title VARCHAR(150) NOT NULL,
    description TEXT,
    position INT NOT NULL,

    FOREIGN KEY (list_id)
        REFERENCES board_lists(id)
        ON DELETE CASCADE
);


CREATE TABLE card_assignments (
    card_id INT NOT NULL,
    user_id INT NOT NULL,

    PRIMARY KEY (card_id, user_id),

    FOREIGN KEY (card_id)
        REFERENCES cards(id)
        ON DELETE CASCADE,

    FOREIGN KEY (user_id)
        REFERENCES users(id)
        ON DELETE CASCADE
);