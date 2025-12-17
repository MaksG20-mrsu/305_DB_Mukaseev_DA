<?php
require 'config.php';
$db = new Database('../data/db.sqlite');
$pdo = $db->getConnection();

$student = null;
if (isset($_GET['id'])) {
    $stmt = $pdo->prepare('SELECT * FROM Students WHERE id = ?');
    $stmt->execute([$_GET['id']]);
    $student = $stmt->fetch();
}

$groups = $pdo->query("SELECT * FROM Groups ORDER BY group_number")->fetchAll();
$specializations = $pdo->query("SELECT * FROM Specializations ORDER BY name")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id']) && $_POST['id']) {
        $stmt = $pdo->prepare(
            'UPDATE Students SET 
                last_name = ?, 
                first_name = ?, 
                middle_name = ?, 
                group_id = ?, 
                specialization_id = ?, 
                gender = ?, 
                birth_date = ?, 
                enrollment_year = ? 
             WHERE id = ?'
        );
        $stmt->execute([
            $_POST['last_name'],
            $_POST['first_name'],
            $_POST['middle_name'] ?: null,
            $_POST['group_id'],
            $_POST['specialization_id'],
            $_POST['gender'],
            $_POST['birth_date'] ?: null,
            $_POST['enrollment_year'],
            $_POST['id']
        ]);
    } else {
        $stmt = $pdo->prepare(
            'INSERT INTO Students (last_name, first_name, middle_name, group_id, specialization_id, gender, birth_date, enrollment_year) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $_POST['last_name'],
            $_POST['first_name'],
            $_POST['middle_name'] ?: null,
            $_POST['group_id'],
            $_POST['specialization_id'],
            $_POST['gender'],
            $_POST['birth_date'] ?: null,
            $_POST['enrollment_year']
        ]);
    }
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title><?= $student ? 'Редактировать' : 'Добавить' ?> студента</title>
    <style>
        body { font-family: Arial; margin: 20px; }
        form { max-width: 500px; }
        label { display: block; margin-top: 10px; font-weight: bold; }
        input, select { width: 100%; padding: 8px; box-sizing: border-box; }
        .radio-group { display: flex; gap: 20px; margin-top: 5px; }
        .radio-group label { font-weight: normal; display: flex; align-items: center; gap: 5px; }
        .radio-group input[type="radio"] { width: auto; }
        button { margin-top: 20px; padding: 10px 20px; background-color: #4CAF50; color: white; border: none; cursor: pointer; }
        a { margin-left: 10px; padding: 10px 20px; background-color: #999; color: white; text-decoration: none; }
    </style>
</head>
<body>
    <h1><?= $student ? 'Редактировать' : 'Добавить' ?> студента</h1>
    
    <form method="POST">
        <?php if ($student): ?>
            <input type="hidden" name="id" value="<?= $student['id'] ?>">
        <?php endif; ?>
        
        <label>Фамилия:
            <input type="text" name="last_name" value="<?= htmlspecialchars($student['last_name'] ?? '') ?>" required>
        </label>
        
        <label>Имя:
            <input type="text" name="first_name" value="<?= htmlspecialchars($student['first_name'] ?? '') ?>" required>
        </label>
        
        <label>Отчество:
            <input type="text" name="middle_name" value="<?= htmlspecialchars($student['middle_name'] ?? '') ?>">
        </label>
        
        <label>Группа:
            <select name="group_id" required>
                <option value="">Выберите группу</option>
                <?php foreach ($groups as $group): ?>
                    <option value="<?= $group['id'] ?>" <?= ($student && $student['group_id'] == $group['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($group['group_number']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
        
        <label>Направление подготовки:
            <select name="specialization_id" required>
                <option value="">Выберите направление</option>
                <?php foreach ($specializations as $spec): ?>
                    <option value="<?= $spec['id'] ?>" <?= ($student && $student['specialization_id'] == $spec['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($spec['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
        
        <label>Пол:
            <div class="radio-group">
                <label>
                    <input type="radio" name="gender" value="М" <?= (!$student || $student['gender'] == 'М') ? 'checked' : '' ?> required>
                    Мужской
                </label>
                <label>
                    <input type="radio" name="gender" value="Ж" <?= ($student && $student['gender'] == 'Ж') ? 'checked' : '' ?>>
                    Женский
                </label>
            </div>
        </label>
        
        <label>Дата рождения:
            <input type="date" name="birth_date" value="<?= $student['birth_date'] ?? '' ?>">
        </label>
        
        <label>Год поступления:
            <input type="number" name="enrollment_year" min="2000" max="2030" value="<?= $student['enrollment_year'] ?? date('Y') ?>" required>
        </label>
        
        <button type="submit">Сохранить</button>
        <a href="index.php">Отмена</a>
    </form>
</body>
</html>

