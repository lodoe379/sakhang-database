<?php
try {
    $p = new PDO('pgsql:host=127.0.0.1;port=9800;dbname=myapp', 'postgres', '123');
    // Change column type to text so TablePlus doesn't crash when sorting
    $p->exec('ALTER TABLE furniture_logs ALTER COLUMN items TYPE text');
    echo "Successfully altered items column to text.\n";
} catch(Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
