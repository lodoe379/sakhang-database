<?php
echo "Laravel default DB count: ";
$laravel = new PDO('pgsql:host=127.0.0.1;port=9800;dbname=postgres', 'postgres', '123');
$stmt = $laravel->query("SELECT COUNT(*) FROM electrical_complaints");
echo $stmt ? $stmt->fetchColumn() : 'Error';
echo "\n";

echo "myapp DB count: ";
$myapp = new PDO('pgsql:host=127.0.0.1;port=9800;dbname=myapp', 'postgres', '123');
$stmt2 = $myapp->query("SELECT COUNT(*) FROM electrical_complaints");
echo $stmt2 ? $stmt2->fetchColumn() : 'Error';
echo "\n";

echo "sqlite DB count: ";
$sqlite = new PDO('sqlite:database/database.sqlite');
$stmt3 = $sqlite->query("SELECT COUNT(*) FROM electrical_complaints");
echo $stmt3 ? $stmt3->fetchColumn() : 'Error';
echo "\n";
