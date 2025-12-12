<?php

if (!is_dir('./data')) {
    mkdir('./data', 0777, true);
}

try {
    $pdo = new PDO('sqlite:./data/db.sqlite');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $sql = file_get_contents('./db_init.sql');
    
    $pdo->exec($sql);
    echo "База данных успешно инициализирована!\n";
    echo "Создан файл: " . realpath('./data/db.sqlite') . "\n";
    
} catch (PDOException $e) {
    die("Ошибка: " . $e->getMessage() . "\n");
}

