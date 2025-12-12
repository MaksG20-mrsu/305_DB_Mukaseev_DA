<?php

$dbPath = __DIR__ . '/students.db';
$dsn = 'sqlite:' . $dbPath;

try {
    $pdo = new PDO($dsn);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Ошибка подключения к базе данных: " . $e->getMessage() . "\n");
}

$currentYear = (int)date('Y');

$stmt = $pdo->prepare("
    SELECT DISTINCT group_number 
    FROM groups 
    WHERE graduation_year <= :current_year 
    ORDER BY group_number
");
$stmt->execute(['current_year' => $currentYear]);
$activeGroups = $stmt->fetchAll(PDO::FETCH_COLUMN);

if (empty($activeGroups)) {
    echo "В базе данных не найдено действующих групп.\n";
    exit(0);
}

echo "\n=== Доступные действующие группы ===\n";
foreach ($activeGroups as $index => $groupNumber) {
    echo ($index + 1) . ". $groupNumber\n";
}
echo "\n";

echo "Введите номер группы для фильтрации (или нажмите Enter для всех групп): ";
$input = trim(fgets(STDIN));

$selectedGroup = null;
if (!empty($input)) {
    if (!in_array($input, $activeGroups)) {
        echo "Ошибка: Неверный номер группы. Запустите скрипт снова.\n";
        exit(1);
    }
    $selectedGroup = $input;
    echo "\nФильтрация по группе: $selectedGroup\n";
} else {
    echo "\nОтображение всех действующих групп\n";
}

$sql = "
    SELECT 
        g.group_number,
        g.specialization,
        s.full_name,
        s.gender,
        s.birth_date,
        s.student_id
    FROM students s
    INNER JOIN groups g ON s.group_id = g.id
    WHERE g.graduation_year <= :current_year
";

$params = ['current_year' => $currentYear];

if ($selectedGroup !== null) {
    $sql .= " AND g.group_number = :group_number";
    $params['group_number'] = $selectedGroup;
}

$sql .= " ORDER BY g.group_number, 
    SUBSTR(s.full_name, 1, INSTR(s.full_name || ' ', ' ') - 1), 
    s.full_name";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($students)) {
    echo "\nСтуденты не найдены.\n";
    exit(0);
}

displayStudentsTable($students);

function displayStudentsTable($students) {
    $widths = [
        'group_number' => max(12, mb_strlen('Номер группы')),
        'specialization' => max(40, mb_strlen('Направление подготовки')),
        'full_name' => max(30, mb_strlen('ФИО')),
        'gender' => max(6, mb_strlen('Пол')),
        'birth_date' => max(12, mb_strlen('Дата рождения')),
        'student_id' => max(18, mb_strlen('Номер билета'))
    ];
    
    foreach ($students as $student) {
        $widths['group_number'] = max($widths['group_number'], mb_strlen($student['group_number']));
        $widths['specialization'] = max($widths['specialization'], mb_strlen($student['specialization']));
        $widths['full_name'] = max($widths['full_name'], mb_strlen($student['full_name']));
        $widths['gender'] = max($widths['gender'], mb_strlen($student['gender']));
        $widths['birth_date'] = max($widths['birth_date'], mb_strlen($student['birth_date']));
        $widths['student_id'] = max($widths['student_id'], mb_strlen($student['student_id']));
    }
    
    $totalWidth = 1 + array_sum($widths) + (count($widths) * 2) + (count($widths) - 1) + 1;
    
    echo "\n" . str_repeat('═', $totalWidth) . "\n";
    
    echo "║ " . str_pad('Номер группы', $widths['group_number'], ' ', STR_PAD_RIGHT) . " ";
    echo "│ " . str_pad('Направление подготовки', $widths['specialization'], ' ', STR_PAD_RIGHT) . " ";
    echo "│ " . str_pad('ФИО', $widths['full_name'], ' ', STR_PAD_RIGHT) . " ";
    echo "│ " . str_pad('Пол', $widths['gender'], ' ', STR_PAD_RIGHT) . " ";
    echo "│ " . str_pad('Дата рождения', $widths['birth_date'], ' ', STR_PAD_RIGHT) . " ";
    echo "│ " . str_pad('Номер билета', $widths['student_id'], ' ', STR_PAD_RIGHT) . " ║\n";
    
    echo "╠" . str_repeat('═', $widths['group_number'] + 2) . "╪";
    echo str_repeat('═', $widths['specialization'] + 2) . "╪";
    echo str_repeat('═', $widths['full_name'] + 2) . "╪";
    echo str_repeat('═', $widths['gender'] + 2) . "╪";
    echo str_repeat('═', $widths['birth_date'] + 2) . "╪";
    echo str_repeat('═', $widths['student_id'] + 2) . "╣\n";
    
    foreach ($students as $student) {
        echo "║ " . str_pad($student['group_number'], $widths['group_number'], ' ', STR_PAD_RIGHT) . " ";
        echo "│ " . str_pad($student['specialization'], $widths['specialization'], ' ', STR_PAD_RIGHT) . " ";
        echo "│ " . str_pad($student['full_name'], $widths['full_name'], ' ', STR_PAD_RIGHT) . " ";
        echo "│ " . str_pad($student['gender'], $widths['gender'], ' ', STR_PAD_RIGHT) . " ";
        echo "│ " . str_pad($student['birth_date'], $widths['birth_date'], ' ', STR_PAD_RIGHT) . " ";
        echo "│ " . str_pad($student['student_id'], $widths['student_id'], ' ', STR_PAD_RIGHT) . " ║\n";
    }
    
    echo str_repeat('═', $totalWidth) . "\n";
    echo "\nВсего студентов: " . count($students) . "\n";
}
