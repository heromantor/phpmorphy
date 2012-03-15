#!/usr/bin/env php
<?php
set_include_path(__DIR__ . '/../../src/' . PATH_SEPARATOR . get_include_path());
require('phpMorphy.php');

if($argc < 3) {
    echo "Usage $argv[0] IN_XML OUT_XML";
    exit(1);
}

try {
    $source = new phpMorphy_Dict_Source_Xml($argv[1]);
    $mapping = array();
    $total_models = 0;

    foreach($source->getFlexias() as $flexia_model) {
        $hash = '';

        foreach($flexia_model->getFlexias() as $flexia) {
            $prefix = $flexia->getPrefix();
            $suffix = $flexia->getSuffix();

            $hash .= "<$prefix>$suffix|";
        }

        $mapping[$hash] = 1;
        ++$total_models;

        echo "$total_models done\n";
    }

    echo "orig = $total_models, new = " . count($mapping) . PHP_EOL;
} catch (Exception $e) {
    echo $e;
    exit(1);
}
