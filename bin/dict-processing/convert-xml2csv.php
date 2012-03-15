#!/usr/bin/env php
<?php
set_include_path(__DIR__ . '/../../src/' . PATH_SEPARATOR . get_include_path());
require('phpMorphy.php');

if($argc < 3) {
    echo "Usage " . $argv[0] . " XML_FILE OUT_DIR" . PHP_EOL;
    exit;
}

$xml_file = $argv[1];
$out_dir = $argv[2];

@mkdir($out_dir, 0744, true);

try {
    $source = new phpMorphy_Dict_Source_Xml($xml_file);
    $out = $out_dir . '/' . $source->getLanguage() . ".xml";
    $writer = new phpMorphy_Dict_Writer_Csv(
        get_abs_filename('part_of_speech.csv'),
        get_abs_filename('grammems.csv'),
        get_abs_filename('ancodes.csv'),
        get_abs_filename('flexia_models.csv'),
        get_abs_filename('prefixes.csv'),
        get_abs_filename('lemmas.csv')
    );

    $writer->setObserver(new phpMorphy_Dict_Writer_Observer_Standart('log_msg'));
    $writer->write($source);
} catch (Exception $e) {
    die((string)$e);
}

function get_abs_filename($name) {
    return $GLOBALS['out_dir'] . DIRECTORY_SEPARATOR . $name;
}

function log_msg($msg) {
    echo $msg, PHP_EOL;
}
