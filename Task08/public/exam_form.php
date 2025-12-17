<?php
require 'config.php';
$db = new Database('../data/db.sqlite');
$pdo = $db->getConnection();

$student_id = $_GET['student_id'] ?? null;
$id = $_GET['id'] ?? null;

if (!$student_id) {
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare('SELECT s.*, g.group_number, sp.name as specialization_name 
                       FROM Students s
                       JOIN Groups g ON s.group_id = g.id
                       JOIN Specializations sp ON s.specialization_id = sp.id
                       WHERE s.id = ?');
$stmt->execute([$student_id]);
$student = $stmt->fetch();

if (!$student) {
    header('Location: index.php');
    exit;
}

$exam = null;
if ($id) {
    $stmt = $pdo->prepare('SELECT * FROM Exams WHERE id = ?');
    $stmt->execute([$id]);
    $exam = $stmt->fetch();
}

// Получаем все дисциплины для направления студента
$disciplines = $pdo->prepare(
    'SELECT d.* FROM Disciplines d 
     WHERE d.specialization_id = ? 
     ORDER BY d.course_number, d.name'
);
$disciplines->execute([$student['specialization_id']]);
$disciplines = $disciplines->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['id'])) {
        $stmt = $pdo->prepare(
            'UPDATE Exams 
             SET discipline_id = ?, 
                 exam_date = ?, 
                 grade = ? 
             WHERE id = ?'
        );
        $stmt->execute([
            $_POST['discipline_id'],
            $_POST['exam_date'],
            $_POST['grade'],
            $_POST['id']
        ]);
    } else {
        $stmt = $pdo->prepare(
            'INSERT INTO Exams (student_id, discipline_id, exam_date, grade) 
             VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([
            $student_id,
            $_POST['discipline_id'],
            $_POST['exam_date'],
            $_POST['grade']
        ]);
    }

    header("Location: exams.php?student_id=$student_id");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title><?= $exam ? 'Редактировать' : 'Добавить' ?> экзамен</title>
    <style>
        body { font-family: Arial; margin: 20px; }
        form { max-width: 500px; }
        label { display: block; margin-top: 10px; font-weight: bold; }
        input, select { width: 100%; padding: 8px; box-sizing: border-box; }
        input[readonly] { background-color: #f0f0f0; cursor: not-allowed; }
        button { margin-top: 20px; padding: 10px 20px; background-color: #FF9800; color: white; border: none; cursor: pointer; }
        a { margin-left: 10px; padding: 10px 20px; background-color: #999; color: white; text-decoration: none; }
        .info { background-color: #e3f2fd; padding: 10px; margin-bottom: 20px; border-radius: 5px; }
    </style>
</head>
<body>
    <h1><?= $exam ? 'Редактировать' : 'Добавить' ?> экзамен</h1>
    
    <div class="info">
        <strong>Студент:</strong> <?= htmlspecialchars($student['last_name'] . ' ' . $student['first_name']) ?><br>
        <strong>Группа:</strong> <?= htmlspecialchars($student['group_number']) ?><br>
        <strong>Направление:</strong> <?= htmlspecialchars($student['specialization_name']) ?><br>
        <strong>Год поступления:</strong> <?= $student['enrollment_year'] ?>
    </div>

    <form method="POST">
        <?php if ($exam): ?>
            <input type="hidden" name="id" value="<?= $exam['id'] ?>">
        <?php endif; ?>

        <label>Дисциплина:
            <select name="discipline_id" required>
                <option value="">Выберите дисциплину</option>
                <?php 
                // Группируем дисциплины по курсам
                $disciplines_by_course = [];
                foreach ($disciplines as $disc) {
                    $disciplines_by_course[$disc['course_number']][] = $disc;
                }
                foreach ($disciplines_by_course as $course => $course_disciplines): ?>
                    <optgroup label="Курс <?= $course ?>">
                        <?php foreach ($course_disciplines as $disc): ?>
                            <option value="<?= $disc['id'] ?>"
                                <?= ($exam && $exam['discipline_id'] == $disc['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($disc['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </optgroup>
                <?php endforeach; ?>
            </select>
            <small style="color: #666;">Вы можете выбрать дисциплину за любой курс для ввода результатов задним числом</small>
        </label>

        <label>Дата экзамена:
            <input type="date" name="exam_date" 
                   value="<?= $exam ? $exam['exam_date'] : date('Y-m-d') ?>" required>
        </label>

        <label>Оценка:
            <select name="grade" required>
                <option value="">Выберите оценку</option>
                <option value="5" <?= ($exam && $exam['grade'] == 5) ? 'selected' : '' ?>>5 (Отлично)</option>
                <option value="4" <?= ($exam && $exam['grade'] == 4) ? 'selected' : '' ?>>4 (Хорошо)</option>
                <option value="3" <?= ($exam && $exam['grade'] == 3) ? 'selected' : '' ?>>3 (Удовлетворительно)</option>
                <option value="2" <?= ($exam && $exam['grade'] == 2) ? 'selected' : '' ?>>2 (Неудовлетворительно)</option>
            </select>
        </label>

        <button type="submit">Сохранить</button>
        <a href="exams.php?student_id=<?= $student_id ?>">Отмена</a>
    </form>
</body>
</html>

