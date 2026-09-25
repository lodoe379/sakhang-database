<?php
try {
    $p = new PDO('sqlite:C:\Users\Tenzin Lodoe\Downloads\Sakhang-Database\database\database.sqlite');
    $st = $p->query("SELECT * FROM electrical_complaints");
    print_r($st ? $st->fetchAll(PDO::FETCH_ASSOC) : "Error or Empty\n");
} catch (Exception $e) {
    echo $e->getMessage() . "\n";
}
