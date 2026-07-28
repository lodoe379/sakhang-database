<?php
$host = '127.0.0.1';
$user = 'postgres';
$pass = 'secret';
$db = 'tashi';

try {
    // Connect to the default 'postgres' database first
    $pdo = new PDO("pgsql:host=$host;dbname=postgres", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if the database exists
    $stmt = $pdo->prepare("SELECT 1 FROM pg_database WHERE datname = ?");
    $stmt->execute([$db]);

    if ($stmt->fetch()) {
        echo "Database '$db' already exists.\n";
    } else {
        // Create the database
        $pdo->exec("CREATE DATABASE $db");
        echo "Database '$db' created successfully!\n";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
