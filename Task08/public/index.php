<?php
require 'config.php';
$db = new Database('../data/db.sqlite');
$pdo = $db->getConnection();

$group_filter = $_GET['group_filter'] ?? '';

$query = "SELECT s.*, g.group_number, sp.name as specialization_name 
          FROM Students s
          JOIN Groups g ON s.group_id = g.id
          JOIN Specializations sp ON s.specialization_id = sp.id";

$params = [];
if ($group_filter) {
    $query .= " WHERE s.group_id = ?";
    $params[] = $group_filter;
}

$query .= " ORDER BY g.group_number, s.last_name, s.first_name";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$students = $stmt->fetchAll();

$groups = $pdo->query("SELECT * FROM Groups ORDER BY group_number")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Студенты - Управление группами и экзаменами</title>
    <style>
        body { font-family: Arial; margin: 20px; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #4CAF50; color: white; }
        .actions { white-space: nowrap; }
        a { margin: 2px; padding: 5px 10px; background-color: #2196F3; color: white; text-decoration: none; border-radius: 3px; font-size: 12px; }
        a.delete { background-color: #f44336; }
        a.exams { background-color: #FF9800; }
        a.add { background-color: #4CAF50; display: inline-block; margin: 20px 0; padding: 10px 20px; }
        .filter { margin-bottom: 20px; }
        .filter select, .filter button { padding: 8px; margin-right: 10px; }
        .filter button { background-color: #2196F3; color: white; border: none; cursor: pointer; padding: 8px 15px; }
    </style>
</head>
<body>
    <h1>Список студентов</h1>
    
    <div class="filter">
        <form method="GET" style="display: inline;">
            <label>Фильтр по группе:</label>
            <select name="group_filter">
                <option value="">Все группы</option>
                <?php foreach ($groups as $group): ?>
                    <option value="<?= $group['id'] ?>" <?= $group_filter == $group['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($group['group_number']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit">Применить</button>
            <?php if ($group_filter): ?>
                <a href="index.php" style="display: inline-block; margin-left: 10px;">Сбросить</a>
            <?php endif; ?>
        </form>
    </div>
    
    <table>
        <tr>
            <th>Группа</th>
            <th>Фамилия</th>
            <th>Имя</th>
            <th>Отчество</th>
            <th>Направление</th>
            <th>Пол</th>
            <th>Год поступления</th>
            <th>Действия</th>
        </tr>
        <?php foreach ($students as $student): ?>
        <tr>
            <td><?= htmlspecialchars($student['group_number']) ?></td>
            <td><?= htmlspecialchars($student['last_name']) ?></td>
            <td><?= htmlspecialchars($student['first_name']) ?></td>
            <td><?= htmlspecialchars($student['middle_name'] ?? '') ?></td>
            <td><?= htmlspecialchars($student['specialization_name']) ?></td>
            <td><?= htmlspecialchars($student['gender']) ?></td>
            <td><?= $student['enrollment_year'] ?></td>
            <td class="actions">
                <a href="student_form.php?id=<?= $student['id'] ?>">Редактировать</a>
                <a href="student_delete.php?id=<?= $student['id'] ?>" class="delete" onclick="return confirm('Удалить студента?')">Удалить</a>
                <a href="exams.php?student_id=<?= $student['id'] ?>" class="exams">Результаты экзаменов</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    
    <a href="student_form.php" class="add">+ Добавить студента</a>
</body>
</html>

