<?php
echo "=== POSTGRESQL DATABASES ===\n";
try {
    $pgsql = new PDO('pgsql:host=127.0.0.1;port=9800;dbname=postgres', 'postgres', '123');
    $stmt = $pgsql->query("SELECT datname FROM pg_database WHERE datistemplate = false");
    $dbs = $stmt->fetchAll(PDO::FETCH_COLUMN);
    foreach ($dbs as $db) {
        echo "\nDB: $db\n";
        try {
            $dbConn = new PDO("pgsql:host=127.0.0.1;port=9800;dbname=$db", 'postgres', '123');
            $stmt2 = $dbConn->query("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public'");
            $tables = $stmt2->fetchAll(PDO::FETCH_COLUMN);
            foreach ($tables as $table) {
                if (in_array($table, ['complaints', 'electrical_complaints'])) {
                    $count = $dbConn->query("SELECT COUNT(*) FROM \"$table\"")->fetchColumn();
                    echo "  $table: $count\n";
                }
            }
        } catch (Exception $e) {
            echo "  Cannot connect: " . $e->getMessage() . "\n";
        }
    }
} catch (Exception $e) {
    echo "PgSQL Error: " . $e->getMessage() . "\n";
}
