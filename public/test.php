<?php
echo "Loaded php.ini: " . php_ini_loaded_file() . "<br>";
echo "Available drivers: " . implode(", ", PDO::getAvailableDrivers()) . "<br>";
