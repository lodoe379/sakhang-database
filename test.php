<?php
$pgsql = new PDO('pgsql:host=127.0.0.1;port=9800;dbname=myapp', 'postgres', '123');
$stmt = $pgsql->query('SELECT * FROM electrical_complaints');
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
