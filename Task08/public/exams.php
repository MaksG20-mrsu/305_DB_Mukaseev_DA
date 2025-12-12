<?php
require 'config.php';
$db = new Database('../data/db.sqlite');
$pdo = $db->getConnection();

$student_id = $_GET['student_id'] ?? null;
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

$stmt = $pdo->prepare(
    'SELECT e.*, d.name as discipline_name, d.course_number
     FROM Exams e
     JOIN Disciplines d ON e.discipline_id = d.id
     WHERE e.student_id = ?
     ORDER BY e.exam_date ASC'
);
$stmt->execute([$student_id]);
$exams = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Результаты экзаменов - <?= htmlspecialchars($student['last_name'] . ' ' . $student['first_name']) ?></title>
    <style>
        body { font-family: Arial; margin: 20px; }
        table { border-collapse: collapse; width: 100%; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #FF9800; color: white; }
        .actions { white-space: nowrap; }
        a { margin: 2px; padding: 5px 10px; background-color: #2196F3; color: white; text-decoration: none; border-radius: 3px; font-size: 12px; }
        a.delete { background-color: #f44336; }
        a.add { background-color: #4CAF50; padding: 10px 20px; display: inline-block; }
    </style>
</head>
<body>
    <h1>Результаты экзаменов: <?= htmlspecialchars($student['last_name'] . ' ' . $student['first_name']) ?></h1>
    <p><strong>Группа:</strong> <?= htmlspecialchars($student['group_number']) ?> | 
       <strong>Направление:</strong> <?= htmlspecialchars($student['specialization_name']) ?> | 
       <strong>Год поступления:</strong> <?= $student['enrollment_year'] ?></p>
    
    <table>
        <tr>
            <th>Дата экзамена</th>
            <th>Дисциплина</th>
            <th>Курс</th>
            <th>Оценка</th>
            <th>Действия</th>
        </tr>
        <?php foreach ($exams as $exam): ?>
        <tr>
            <td><?= date('d.m.Y', strtotime($exam['exam_date'])) ?></td>
            <td><?= htmlspecialchars($exam['discipline_name']) ?></td>
            <td><?= $exam['course_number'] ?></td>
            <td><?= $exam['grade'] ?></td>
            <td class="actions">
                <a href="exam_form.php?id=<?= $exam['id'] ?>&student_id=<?= $student_id ?>">Редактировать</a>
                <a href="exam_delete.php?id=<?= $exam['id'] ?>&student_id=<?= $student_id ?>" class="delete" onclick="return confirm('Удалить экзамен?')">Удалить</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    
    <a href="exam_form.php?student_id=<?= $student_id ?>" class="add">+ Добавить экзамен</a>
    <br><br>
    <a href="index.php">← Вернуться к списку студентов</a>
</body>
</html>

