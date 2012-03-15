#!/usr/bin/env php
<?php
set_include_path(__DIR__ . '/../../src/' . PATH_SEPARATOR . get_include_path());
require('phpMorphy.php');

$gramtab_consts_file = PHPMORPHY_DIR . '/phpMorphy/GramTab/gramtab_consts.php';

try {
    phpMorphy_Generator_GramTab::generatePhp($gramtab_consts_file);
} catch (Exception $e) {
    echo $e;
    exit(1);
}
