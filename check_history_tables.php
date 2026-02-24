<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'mpfm';
$conn = new mysqli($host, $user, $pass, $db);

$result = $conn->query('SHOW TABLES LIKE "%history%"');
echo "History tables:\n";
while($row = $result->fetch_row()) {
    echo "- " . $row[0] . "\n";
}
?>
