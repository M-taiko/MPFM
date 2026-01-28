<?php
echo "PHP Version: " . PHP_VERSION . "\n";
echo "PHP CLI Version: " . shell_exec('php -v | head -1') . "\n";
echo "Loaded Extensions:\n";
print_r(get_loaded_extensions());
echo "\n\nmbstring loaded: " . (extension_loaded('mbstring') ? 'YES' : 'NO') . "\n";
