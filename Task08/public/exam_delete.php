<?php
require 'config.php';
$db = new Database('../data/db.sqlite');
$pdo = $db->getConnection();

$id = $_GET['id'] ?? null;
$student_id = $_GET['student_id'] ?? null;

if ($id) {
    $stmt = $pdo->prepare('DELETE FROM Exams WHERE id = ?');
    $stmt->execute([$id]);
}

if ($student_id) {
    header("Location: exams.php?student_id=$student_id");
} else {
    header('Location: index.php');
}

