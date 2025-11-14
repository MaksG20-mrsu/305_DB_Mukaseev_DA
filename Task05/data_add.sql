INSERT INTO users (name, email, gender, register_date, occupation)
VALUES ('Дмитрий Мукасеев', 'dmitriy.mukaseev@student.university.edu', 'male', date('now'), 'student');

INSERT INTO users (name, email, gender, register_date, occupation)
VALUES ('Владислав Наумкин', 'vladislav.naumkin@university.edu', 'male', date('now'), 'student');

INSERT INTO users (name, email, gender, register_date, occupation)
VALUES ('Маскинскова Наталья', 'natalia.maskinskaya@university.edu', 'female', date('now'), 'student');

INSERT INTO users (name, email, gender, register_date, occupation)
VALUES ('Маклаков Сергей', 'sergey.maklakov@university.edu', 'male', date('now'), 'student');

INSERT INTO users (name, email, gender, register_date, occupation)
VALUES ('Макарова Юлия', 'julia.makarova@university.edu', 'female', date('now'), 'student');

INSERT INTO movies (title, year)
VALUES ('Крутой Боевик 2026', 2026);

INSERT INTO movie_genres (movie_id, genre_id)
SELECT m.id, g.id 
FROM movies m, genres g 
WHERE m.title = 'Крутой Боевик 2026' AND g.name = 'Action';

INSERT INTO movies (title, year)
VALUES ('Веселая Комедия 2026', 2026);

INSERT INTO movie_genres (movie_id, genre_id)
SELECT m.id, g.id 
FROM movies m, genres g 
WHERE m.title = 'Веселая Комедия 2026' AND g.name = 'Comedy';

INSERT INTO movies (title, year)
VALUES ('Фантастическое Будущее 2026', 2026);

INSERT INTO movie_genres (movie_id, genre_id)
SELECT m.id, g.id 
FROM movies m, genres g 
WHERE m.title = 'Фантастическое Будущее 2026' AND g.name = 'Sci-Fi';

INSERT INTO ratings (user_id, movie_id, rating, timestamp)
SELECT 
    (SELECT id FROM users WHERE email = 'dmitriy.mukaseev@student.university.edu'),
    (SELECT id FROM movies WHERE title = 'Крутой Боевик 2026'),
    4.5,
    strftime('%s', 'now');

INSERT INTO ratings (user_id, movie_id, rating, timestamp)
SELECT 
    (SELECT id FROM users WHERE email = 'dmitriy.mukaseev@student.university.edu'),
    (SELECT id FROM movies WHERE title = 'Веселая Комедия 2026'),
    5.0,
    strftime('%s', 'now');

INSERT INTO ratings (user_id, movie_id, rating, timestamp)
SELECT 
    (SELECT id FROM users WHERE email = 'dmitriy.mukaseev@student.university.edu'),
    (SELECT id FROM movies WHERE title = 'Фантастическое Будущее 2026'),
    4.0,
    strftime('%s', 'now');
