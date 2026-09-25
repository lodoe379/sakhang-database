<?php
echo "=== SQLITE TABLES ===\n";
try {
    $sqlite = new PDO('sqlite:database/database.sqlite');
    $stmt = $sqlite->query("SELECT name FROM sqlite_master WHERE type='table'");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    foreach ($tables as $table) {
        $count = $sqlite->query("SELECT COUNT(*) FROM \"$table\"")->fetchColumn();
        echo "$table: $count\n";
    }
} catch (Exception $e) {
    echo "SQLite Error: " . $e->getMessage() . "\n";
}

echo "\n=== POSTGRESQL TABLES ===\n";
try {
    $pgsql = new PDO('pgsql:host=127.0.0.1;port=9800;dbname=myapp', 'postgres', '123');
    $stmt = $pgsql->query("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public'");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    foreach ($tables as $table) {
        $count = $pgsql->query("SELECT COUNT(*) FROM \"$table\"")->fetchColumn();
        echo "$table: $count\n";
    }
} catch (Exception $e) {
    echo "PgSQL Error: " . $e->getMessage() . "\n";
}
