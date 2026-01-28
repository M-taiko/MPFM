<?php
echo "PHP Version: " . PHP_VERSION . "\n";
echo "PHP Extensions: \n";
echo "mbstring: " . (extension_loaded('mbstring') ? 'YES' : 'NO') . "\n";
echo "json: " . (extension_loaded('json') ? 'YES' : 'NO') . "\n";
echo "pdo: " . (extension_loaded('pdo') ? 'YES' : 'NO') . "\n";
echo "Server: " . $_SERVER['SERVER_SOFTWARE'] . "\n";
?>