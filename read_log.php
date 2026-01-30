<?php
$logFile = 'storage/logs/laravel.log';
if (!file_exists($logFile)) {
    echo "Log file not found.\n";
    exit;
}
$lines = file($logFile);
echo implode("", array_slice($lines, -50));
