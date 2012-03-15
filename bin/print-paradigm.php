#!/usr/bin/env php
<?php
set_include_path(__DIR__ . '/../src/' . PATH_SEPARATOR . get_include_path());
require('phpMorphy.php');

define('WORD_NOT_FOUND', 1);

if($argc < 2) {
    die("Usage $argv[0] WORD [LANG] [ENCODING]" . PHP_EOL);
}

$word = $argv[1];

$lang = $argc > 2 ? $argv[2] : 'ru_RU';

$dir = __DIR__ . '/../dicts/';
$dir .= $argc > 3 ? "/{$argv[3]}" : 'utf-8';

$opts = array(
    'storage' => PHPMORPHY_STORAGE_FILE,
    'predict_by_suffix' => true,
    'predict_by_db' => true,
);

$morphy = new phpMorphy($dir, $lang, $opts);
$encoding = $morphy->getEncoding();
$formatter = new phpMorphy_Paradigm_Formatter();

$word = iconv('utf-8', $encoding, $word);
$word = mb_strtoupper($word, $encoding);
$result = $morphy->findWord($word);

$predict_text = 'DICT';
if($morphy->getLastPredictionType() == phpMorphy::PREDICT_BY_DB) {
    $predict_text = 'PREDICT_BY_DB';
} else if($morphy->getLastPredictionType() == phpMorphy::PREDICT_BY_SUFFIX) {
    $predict_text = 'PREDICT_BY_SUFFIX';
}

echo "Paradigms for $word($predict_text):" . PHP_EOL;
if(false === $result) {
    echo 'NOT FOUND' . PHP_EOL;
    exit(WORD_NOT_FOUND);
}

$para_no = 1;
foreach($result as $paradigm) {
    printf("  Paradigm %2d.\n%s", $para_no++, $formatter->format($paradigm, '  '));
}
