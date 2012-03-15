#!/usr/bin/env php
<?php
set_include_path(__DIR__ . '/../../src/' . PATH_SEPARATOR . get_include_path());
require('phpMorphy.php');

if($argc < 3) {
    echo "Usage " . $argv[0] . " MORPH_DATA_FILE OUT_DIR";
    exit;
}

$file = $argv[1];
$out_dir = $argv[2];

try {
    $factory = new phpMorphy_Storage_Factory();
    $graminfo = phpMorphy_GramInfo_GramInfoAbstract::create($factory->create(PHPMORPHY_STORAGE_FILE, $file, false), false);
    
    $out_file = $out_dir . '/morph_data_header_cache.' . strtolower($graminfo->getLocale()) . '.bin';
    
    file_put_contents(
        $out_file,
        '<' . "?php" . PHP_EOL . "return " .
            var_export(
                $graminfo->getHeader(),
                true
            ) .
        ";" . PHP_EOL
    );
} catch (Exception $e) {
    echo $e;
    exit(1);
}
