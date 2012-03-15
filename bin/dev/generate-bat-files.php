#!/usr/bin/env php
<?php
function generate_contents($php_file_path) {
    $php_file_path = basename($php_file_path);
    return "@%PHPRC%/php -f %~pd0\\$php_file_path -- %*" . PHP_EOL;
}

foreach(glob(__DIR__ . '/*.php') as $path) {
    $bat_path = preg_replace('/\\.php$/', '.bat', $path);
    
    file_put_contents($bat_path, generate_contents($path));
}