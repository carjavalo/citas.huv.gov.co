<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$json = File::get(base_path('database/data_export.json'));
$data = json_decode($json, true);

DB::statement('SET FOREIGN_KEY_CHECKS=0;');

foreach ($data as $table => $rows) {
    if (empty($rows)) continue;
    
    echo "Importing table: $table (" . count($rows) . " rows)\n";
    
    // Truncate table
    DB::table($table)->truncate();
    
    // Insert in chunks
    $chunks = array_chunk($rows, 100);
    foreach ($chunks as $chunk) {
        // Convert objects to arrays
        $chunkData = array_map(function($item) {
            return (array) $item;
        }, $chunk);
        
        DB::table($table)->insert($chunkData);
    }
}

DB::statement('SET FOREIGN_KEY_CHECKS=1;');

echo "Data imported successfully!\n";
