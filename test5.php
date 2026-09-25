<?php
$p = new PDO('pgsql:host=127.0.0.1;port=9800;dbname=postgres', 'postgres', '123');
$dbs = $p->query("SELECT datname FROM pg_database")->fetchAll(PDO::FETCH_COLUMN);
print_r($dbs);

foreach($dbs as $db) {
    try {
        $conn = new PDO("pgsql:host=127.0.0.1;port=9800;dbname=$db", 'postgres', '123');
        $tables = $conn->query("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public'")->fetchAll(PDO::FETCH_COLUMN);
        if (in_array('electrical_complaints', $tables)) {
            $count = $conn->query("SELECT COUNT(*) FROM electrical_complaints")->fetchColumn();
            echo "DB: $db -> electrical_complaints count: $count\n";
        }
    } catch(Exception $e) {}
}
