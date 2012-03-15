#!/usr/bin/env php
<?php
set_include_path(__DIR__ . '/../../src/' . PATH_SEPARATOR . get_include_path());
require('phpMorphy.php');

if($argc < 3) {
    echo "Usage " . $argv[0] . " MWZ_FILE OUT_DIR" . PHP_EOL;
    exit;
}

$mwz_file = $argv[1];
$out_dir = $argv[2];

@mkdir($out_dir, 0744, true);

try {
    $source = new phpMorphy_Dict_Source_Mrd($mwz_file);
    $out = $out_dir . '/' . $source->getLanguage() . ".xml";
    
    $writer = new phpMorphy_Dict_Writer_Xml($out);
    $writer->setObserver(new phpMorphy_Dict_Writer_Observer_Standart('log_msg'));
    $writer->write($source);
} catch (Exception $e) {
    echo $e;
    exit(1);
}

function log_msg($msg) {
    echo $msg, PHP_EOL;
}
