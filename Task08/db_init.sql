PRAGMA foreign_keys = ON;

CREATE TABLE Groups (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    group_number TEXT NOT NULL UNIQUE
);

CREATE TABLE Specializations (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL UNIQUE
);

CREATE TABLE Students (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    last_name TEXT NOT NULL,
    first_name TEXT NOT NULL,
    middle_name TEXT,
    group_id INTEGER NOT NULL,
    specialization_id INTEGER NOT NULL,
    gender TEXT NOT NULL CHECK (gender IN ('М', 'Ж')),
    birth_date DATE,
    enrollment_year INTEGER NOT NULL,
    FOREIGN KEY (group_id) REFERENCES Groups(id) ON DELETE RESTRICT,
    FOREIGN KEY (specialization_id) REFERENCES Specializations(id) ON DELETE RESTRICT
);

CREATE TABLE Disciplines (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    specialization_id INTEGER NOT NULL,
    course_number INTEGER NOT NULL CHECK (course_number BETWEEN 1 AND 6),
    UNIQUE (name, specialization_id, course_number),
    FOREIGN KEY (specialization_id) REFERENCES Specializations(id) ON DELETE RESTRICT
);

CREATE TABLE Exams (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    student_id INTEGER NOT NULL,
    discipline_id INTEGER NOT NULL,
    exam_date DATE NOT NULL,
    grade INTEGER NOT NULL CHECK (grade BETWEEN 2 AND 5),
    FOREIGN KEY (student_id) REFERENCES Students(id) ON DELETE CASCADE,
    FOREIGN KEY (discipline_id) REFERENCES Disciplines(id) ON DELETE RESTRICT
);

CREATE INDEX idx_students_group ON Students(group_id);
CREATE INDEX idx_students_specialization ON Students(specialization_id);
CREATE INDEX idx_exams_student ON Exams(student_id);
CREATE INDEX idx_exams_date ON Exams(exam_date);
CREATE INDEX idx_disciplines_spec_course ON Disciplines(specialization_id, course_number);

INSERT INTO Groups (group_number) VALUES
    ('ПИ-304'),
    ('ПИ-305');

INSERT INTO Specializations (name) VALUES
    ('Информационные системы'),
    ('Прикладная информатика'),
    ('Программная инженерия');

INSERT INTO Disciplines (name, specialization_id, course_number) VALUES
    ('Базы данных', 1, 2),
    ('Базы данных', 1, 3),
    ('Программирование', 1, 1),
    ('Программирование', 1, 2),
    ('Веб-технологии', 1, 3),
    ('Веб-технологии', 1, 4),
    ('Математический анализ', 1, 1),
    ('Математический анализ', 1, 2),
    ('Операционные системы', 1, 2),
    ('Операционные системы', 1, 3);

INSERT INTO Disciplines (name, specialization_id, course_number) VALUES
    ('Базы данных', 2, 2),
    ('Базы данных', 2, 3),
    ('Программирование', 2, 1),
    ('Программирование', 2, 2),
    ('Веб-технологии', 2, 3),
    ('Экономика', 2, 1),
    ('Экономика', 2, 2),
    ('Менеджмент', 2, 3);

INSERT INTO Disciplines (name, specialization_id, course_number) VALUES
    ('Базы данных', 3, 2),
    ('Программирование', 3, 1),
    ('Программирование', 3, 2),
    ('Программирование', 3, 3),
    ('Алгоритмы и структуры данных', 3, 2),
    ('Алгоритмы и структуры данных', 3, 3),
    ('Инженерия программного обеспечения', 3, 3),
    ('Инженерия программного обеспечения', 3, 4);

INSERT INTO Students (last_name, first_name, middle_name, group_id, specialization_id, gender, birth_date, enrollment_year) VALUES
    ('Зубков', 'Роман', 'Сергеевич', 1, 3, 'М', '2005-01-15', 2023),
    ('Иванов', 'Максим', 'Александрович', 1, 3, 'М', '2005-03-20', 2023),
    ('Ивенин', 'Артём', 'Андреевич', 1, 3, 'М', '2005-05-10', 2023),
    ('Казейкин', 'Иван', 'Иванович', 1, 3, 'М', '2005-07-25', 2023),
    ('Кочнев', 'Артем', 'Алексеевич', 1, 3, 'М', '2005-09-12', 2023),
    ('Логунов', 'Илья', 'Сергеевич', 1, 3, 'М', '2005-11-08', 2023),
    ('Макарова', 'Юлия', 'Сергеевна', 1, 3, 'Ж', '2005-02-18', 2023),
    ('Маклаков', 'Сергей', 'Александрович', 1, 3, 'М', '2005-04-22', 2023),
    ('Маскинскова', 'Наталья', 'Сергеевна', 1, 3, 'Ж', '2005-06-30', 2023),
    ('Мукасеев', 'Дмитрий', 'Александрович', 1, 3, 'М', '2005-08-14', 2023),
    ('Наумкин', 'Владислав', 'Валерьевич', 1, 3, 'М', '2005-10-05', 2023),
    ('Паркаев', 'Василий', 'Александрович', 1, 3, 'М', '2005-12-20', 2023),
    ('Полковников', 'Дмитрий', 'Александрович', 1, 3, 'М', '2005-01-28', 2023),
    ('Пузаков', 'Дмитрий', 'Александрович', 2, 3, 'М', '2005-02-10', 2023),
    ('Пшеницына', 'Полина', 'Алексеевна', 2, 3, 'Ж', '2005-04-15', 2023),
    ('Пяткин', 'Игорь', 'Алексеевич', 2, 3, 'М', '2005-06-22', 2023),
    ('Рыбаков', 'Евгений', 'Геннадьевич', 2, 3, 'М', '2005-08-30', 2023),
    ('Рыжкин', 'Владислав', 'Дмитриевич', 2, 3, 'М', '2005-10-12', 2023),
    ('Рябченко', 'Александра', 'Станиславовна', 2, 3, 'Ж', '2005-12-05', 2023),
    ('Томилин', 'Илья', 'Петрович', 2, 3, 'М', '2005-03-18', 2023),
    ('Тульсков', 'Илья', 'Андреевич', 2, 3, 'М', '2005-05-25', 2023),
    ('Фирстов', 'Артём', 'Александрович', 2, 3, 'М', '2005-07-08', 2023),
    ('Четайкин', 'Владислав', 'Александрович', 2, 3, 'М', '2005-09-14', 2023),
    ('Шарунов', 'Максим', 'Игоревич', 2, 3, 'М', '2005-11-20', 2023),
    ('Шушев', 'Денис', 'Сергеевич', 2, 3, 'М', '2005-01-30', 2023);

INSERT INTO Exams (student_id, discipline_id, exam_date, grade) VALUES
    (1, 25, '2025-01-15', 5),
    (1, 22, '2025-01-16', 5),
    (2, 25, '2025-01-17', 4),
    (2, 24, '2025-01-18', 4),
    (3, 22, '2025-01-19', 5),
    (3, 25, '2025-01-20', 5),
    (4, 24, '2025-01-21', 5),
    (4, 25, '2025-01-22', 4),
    (5, 22, '2025-01-23', 4),
    (5, 25, '2025-01-24', 4),
    (6, 24, '2025-01-25', 5),
    (6, 25, '2025-01-26', 5),
    (7, 25, '2025-01-27', 5),
    (7, 22, '2025-01-28', 5),
    (8, 22, '2025-01-29', 4),
    (8, 24, '2025-01-30', 4),
    (9, 24, '2025-02-01', 5),
    (9, 25, '2025-02-02', 5),
    (10, 22, '2025-02-03', 5),
    (10, 25, '2025-02-04', 5),
    (11, 24, '2025-02-05', 4),
    (11, 25, '2025-02-06', 5),
    (12, 25, '2025-02-07', 4),
    (12, 22, '2025-02-08', 4),
    (13, 22, '2025-02-09', 5),
    (13, 24, '2025-02-10', 5),
    (14, 25, '2025-02-11', 5),
    (14, 22, '2025-02-12', 5),
    (15, 22, '2025-02-13', 5),
    (15, 25, '2025-02-14', 5),
    (16, 24, '2025-02-15', 4),
    (16, 25, '2025-02-16', 4),
    (17, 22, '2025-02-17', 5),
    (17, 25, '2025-02-18', 5),
    (18, 24, '2025-02-19', 5),
    (18, 25, '2025-02-20', 5),
    (19, 25, '2025-02-21', 5),
    (19, 22, '2025-02-22', 5),
    (20, 22, '2025-02-23', 4),
    (20, 24, '2025-02-24', 4),
    (21, 24, '2025-02-25', 5),
    (21, 25, '2025-02-26', 5),
    (22, 22, '2025-02-27', 5),
    (22, 25, '2025-02-28', 5),
    (23, 24, '2025-03-01', 4),
    (23, 25, '2025-03-02', 5),
    (24, 25, '2025-03-03', 5),
    (24, 22, '2025-03-04', 5),
    (25, 22, '2025-03-05', 4),
    (25, 24, '2025-03-06', 5);
