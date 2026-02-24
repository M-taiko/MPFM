<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'mpfm';
$conn = new mysqli($host, $user, $pass, $db);

echo "=== TASK_STATE_HISTORIES TABLE ===\n";
$result = $conn->query('DESCRIBE task_state_histories');
while($row = $result->fetch_assoc()) {
    echo "- " . $row['Field'] . " (" . $row['Type'] . ")\n";
}
?>
