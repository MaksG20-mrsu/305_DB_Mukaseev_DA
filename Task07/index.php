<?php

$dbPath = __DIR__ . '/students.db';
$dsn = 'sqlite:' . $dbPath;

try {
    $pdo = new PDO($dsn);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection error: " . $e->getMessage());
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

$selectedGroup = $_GET['group'] ?? '';

if (!empty($selectedGroup) && !in_array($selectedGroup, $activeGroups)) {
    $selectedGroup = '';
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

if (!empty($selectedGroup)) {
    $sql .= " AND g.group_number = :group_number";
    $params['group_number'] = $selectedGroup;
}

$sql .= " ORDER BY g.group_number, 
    SUBSTR(s.full_name, 1, INSTR(s.full_name || ' ', ' ') - 1), 
    s.full_name";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>РЎРїРёСЃРѕРє СЃС‚СѓРґРµРЅС‚РѕРІ</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 1400px;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            text-align: center;
        }
        .filter-section {
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f9f9f9;
            border-radius: 5px;
        }
        label {
            font-weight: bold;
            margin-right: 10px;
        }
        select {
            padding: 8px;
            font-size: 14px;
            border: 1px solid #ddd;
            border-radius: 4px;
            min-width: 200px;
        }
        button {
            padding: 8px 16px;
            font-size: 14px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-left: 10px;
        }
        button:hover {
            background-color: #0056b3;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #007bff;
            color: white;
            font-weight: bold;
        }
        tr:hover {
            background-color: #f5f5f5;
        }
        .no-data {
            text-align: center;
            padding: 20px;
            color: #666;
        }
        .stats {
            margin-top: 20px;
            padding: 10px;
            background-color: #e9ecef;
            border-radius: 4px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>РЎРїРёСЃРѕРє СЃС‚СѓРґРµРЅС‚РѕРІ</h1>
        
        <div class="filter-section">
            <form method="GET" action="">
                <label for="group">Р¤РёР»СЊС‚СЂ РїРѕ РіСЂСѓРїРїРµ:</label>
                <select name="group" id="group">
                    <option value="">Р’СЃРµ РіСЂСѓРїРїС‹</option>
                    <?php if (!empty($activeGroups)): ?>
                        <?php foreach ($activeGroups as $groupNumber): ?>
                            <option value="<?= htmlspecialchars($groupNumber) ?>" 
                                <?= ($selectedGroup === $groupNumber) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($groupNumber) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
                <button type="submit">РџСЂРёРјРµРЅРёС‚СЊ С„РёР»СЊС‚СЂ</button>
            </form>
        </div>

        <?php if (empty($students)): ?>
            <div class="no-data">
                <p>РЎС‚СѓРґРµРЅС‚С‹ РЅРµ РЅР°Р№РґРµРЅС‹.</p>
            </div>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>РќРѕРјРµСЂ РіСЂСѓРїРїС‹</th>
                        <th>РќР°РїСЂР°РІР»РµРЅРёРµ РїРѕРґРіРѕС‚РѕРІРєРё</th>
                        <th>Р¤РРћ</th>
                        <th>РџРѕР»</th>
                        <th>Р”Р°С‚Р° СЂРѕР¶РґРµРЅРёСЏ</th>
                        <th>РќРѕРјРµСЂ СЃС‚СѓРґРµРЅС‡РµСЃРєРѕРіРѕ Р±РёР»РµС‚Р°</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $student): ?>
                        <tr>
                            <td><?= htmlspecialchars($student['group_number']) ?></td>
                            <td><?= htmlspecialchars($student['specialization']) ?></td>
                            <td><?= htmlspecialchars($student['full_name']) ?></td>
                            <td><?= htmlspecialchars($student['gender']) ?></td>
                            <td><?= htmlspecialchars($student['birth_date']) ?></td>
                            <td><?= htmlspecialchars($student['student_id']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <div class="stats">
                <strong>Р’СЃРµРіРѕ СЃС‚СѓРґРµРЅС‚РѕРІ: <?= count($students) ?></strong>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
