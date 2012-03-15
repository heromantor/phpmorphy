#!/usr/bin/env php
<?php
set_include_path(__DIR__ . '/../../src/' . PATH_SEPARATOR . get_include_path());
require('phpMorphy.php');

$morphy_dir = PHPMORPHY_DIR;

try {
    phpMorphy_Generator_GramInfo::generate($morphy_dir);
} catch (Exception $e) {
    echo $e;
    exit(1);
}
