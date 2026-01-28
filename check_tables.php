<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'mpfm';
$conn = new mysqli($host, $user, $pass, $db);

echo "=== PLAN_TASKS TABLE ===\n";
$result = $conn->query('DESCRIBE plan_tasks');
while($row = $result->fetch_assoc()) {
    echo "- " . $row['Field'] . " (" . $row['Type'] . ") " . ($row['Null'] === 'NO' ? 'NOT NULL' : 'NULLABLE') . "\n";
}

echo "\n=== TASKS TABLE ===\n";
$result = $conn->query('DESCRIBE tasks');
while($row = $result->fetch_assoc()) {
    echo "- " . $row['Field'] . " (" . $row['Type'] . ") " . ($row['Null'] === 'NO' ? 'NOT NULL' : 'NULLABLE') . "\n";
}
?>
