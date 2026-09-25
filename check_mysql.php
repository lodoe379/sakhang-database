<?php
echo "=== MYSQL TABLES ===\n";
try {
    $mysql = new PDO('mysql:host=127.0.0.1;port=3306', 'root', '');
    $stmt = $mysql->query("SHOW DATABASES");
    $dbs = $stmt->fetchAll(PDO::FETCH_COLUMN);
    foreach ($dbs as $db) {
        echo "DB: $db\n";
        if ($db === 'information_schema' || $db === 'mysql' || $db === 'performance_schema' || $db === 'sys') continue;
        
        $mysql->exec("USE `$db`");
        $stmt2 = $mysql->query("SHOW TABLES");
        $tables = $stmt2->fetchAll(PDO::FETCH_COLUMN);
        foreach ($tables as $table) {
            $count = $mysql->query("SELECT COUNT(*) FROM `$table`")->fetchColumn();
            echo "  $table: $count\n";
        }
    }
} catch (Exception $e) {
    echo "MySQL Error: " . $e->getMessage() . "\n";
}
