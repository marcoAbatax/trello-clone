INSERT INTO users(name, email) VALUES
('Marco', 'marco@example.com'),
('Anna', 'anna@example.com');

INSERT INTO boards (name) VALUES
('Progetto Ingegneria del Software');

INSERT INTO board_members(board_id, user_id) VALUES
(1, 1),
(1, 2);

INSERT INTO board_lists (board_id, title, position) VALUES
(1, 'Da fare', 1),
(1, 'In corso', 2),
(1, 'Completato' 3);

INSERT INTO cards (list_id, title, description, position)VALUES
(1, 'definire requisiti', 'Scrivere requisiti funzionali e non funzionali', 1),
(1, 'Creare database', 'definire schema realzionale', 2);

INSERT INTO card_assignments (card_id, user_id)VALUES
(1,1),
(2,2);
