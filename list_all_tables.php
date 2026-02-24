<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$tables = DB::select('SHOW TABLES');
echo "All tables:\n";
foreach($tables as $table) {
    $tableName = array_values((array)$table)[0];
    if (strpos(strtolower($tableName), 'history') !== false || strpos(strtolower($tableName), 'state') !== false) {
        echo "- " . $tableName . "\n";
    }
}
?>
