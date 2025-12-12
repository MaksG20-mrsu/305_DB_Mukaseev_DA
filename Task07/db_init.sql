DROP TABLE IF EXISTS students;
DROP TABLE IF EXISTS groups;

CREATE TABLE groups (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    group_number TEXT NOT NULL UNIQUE,
    specialization TEXT NOT NULL,
    graduation_year INTEGER NOT NULL CHECK(graduation_year > 2000 AND graduation_year <= 2100)
);

CREATE TABLE students (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    group_id INTEGER NOT NULL,
    full_name TEXT NOT NULL,
    gender TEXT NOT NULL CHECK(gender IN ('М', 'Ж', 'Мужской', 'Женский')),
    birth_date TEXT NOT NULL,
    student_id TEXT NOT NULL UNIQUE,
    FOREIGN KEY (group_id) REFERENCES groups(id) ON DELETE CASCADE
);

CREATE INDEX idx_groups_number ON groups(group_number);
CREATE INDEX idx_groups_graduation_year ON groups(graduation_year);
CREATE INDEX idx_students_group_id ON students(group_id);
CREATE INDEX idx_students_full_name ON students(full_name);

INSERT INTO groups (group_number, specialization, graduation_year) VALUES
('ИСТ-301', 'Информационные системы и технологии', 2025),
('ИСТ-302', 'Информационные системы и технологии', 2025),
('ИСТ-303', 'Информационные системы и технологии', 2026),
('ПИ-304', 'Прикладная информатика', 2025),
('ПИ-305', 'Прикладная информатика', 2026),
('ИСТ-401', 'Информационные системы и технологии', 2024),
('ПИ-402', 'Прикладная информатика', 2024);

INSERT INTO students (group_id, full_name, gender, birth_date, student_id) VALUES
(1, 'Иванов Иван Иванович', 'М', '2003-05-15', 'ИСТ-301-001'),
(1, 'Петрова Анна Сергеевна', 'Ж', '2003-08-22', 'ИСТ-301-002'),
(1, 'Сидоров Петр Александрович', 'М', '2003-03-10', 'ИСТ-301-003'),
(2, 'Кузнецова Мария Дмитриевна', 'Ж', '2003-11-05', 'ИСТ-302-001'),
(2, 'Морозов Алексей Викторович', 'М', '2003-07-18', 'ИСТ-302-002'),
(2, 'Волкова Елена Николаевна', 'Ж', '2003-02-28', 'ИСТ-302-003'),
(3, 'Новиков Дмитрий Олегович', 'М', '2004-09-12', 'ИСТ-303-001'),
(3, 'Лебедева Ольга Игоревна', 'Ж', '2004-04-25', 'ИСТ-303-002'),
(4, 'Соколов Михаил Андреевич', 'М', '2003-06-30', 'ПИ-304-001'),
(4, 'Павлова Татьяна Владимировна', 'Ж', '2003-12-08', 'ПИ-304-002'),
(4, 'Федоров Сергей Петрович', 'М', '2003-01-20', 'ПИ-304-003'),
(5, 'Орлов Андрей Сергеевич', 'М', '2004-10-14', 'ПИ-305-001'),
(5, 'Антонова Юлия Романовна', 'Ж', '2004-05-07', 'ПИ-305-002');
