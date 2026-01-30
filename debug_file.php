<?php
$path = __DIR__.'/public/test.txt';
echo "Checking path: $path\n";
echo "File exists: " . (file_exists($path) ? 'YES' : 'NO') . "\n";
echo "Is readable: " . (is_readable($path) ? 'YES' : 'NO') . "\n";
echo "Current user: " . get_current_user() . "\n";
echo "Effective user ID: " . posix_geteuid() . "\n";
