<?php
$file = 'migration_error.txt';
if (file_exists($file)) {
    $lines = file($file);
    foreach ($lines as $line) {
        if (strpos($line, 'PHP Warning') === false && strpos($line, 'Module "openssl"') === false) {
            echo $line;
        }
    }
} else {
    echo "File not found.";
}
