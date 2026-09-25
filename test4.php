<?php
echo "=== POSTGRES DB ===\n";
$p = new PDO('pgsql:host=127.0.0.1;port=9800;dbname=postgres', 'postgres', '123');
$s = $p->query("SELECT * FROM electrical_complaints");
print_r($s ? $s->fetchAll(PDO::FETCH_ASSOC) : "Error or Empty\n");

echo "\n=== MYAPP DB ===\n";
$p2 = new PDO('pgsql:host=127.0.0.1;port=9800;dbname=myapp', 'postgres', '123');
$s2 = $p2->query("SELECT * FROM electrical_complaints");
print_r($s2 ? $s2->fetchAll(PDO::FETCH_ASSOC) : "Error or Empty\n");

echo "\n=== SQLITE DB ===\n";
try {
    $s3 = new PDO('sqlite:database/database.sqlite');
    $st = $s3->query("SELECT * FROM electrical_complaints");
    print_r($st ? $st->fetchAll(PDO::FETCH_ASSOC) : "Error or Empty\n");
} catch (Exception $e) {
    echo $e->getMessage() . "\n";
}
