<?php
$pdo = new PDO('sqlite:database/database.sqlite');
$stmt = $pdo->query('SELECT COUNT(*) FROM complaints');
echo 'Complaints: ' . ($stmt ? $stmt->fetchColumn() : 'table missing') . PHP_EOL;

$stmt = $pdo->query('SELECT COUNT(*) FROM electrical_complaints');
echo 'Electrical: ' . ($stmt ? $stmt->fetchColumn() : 'table missing') . PHP_EOL;
