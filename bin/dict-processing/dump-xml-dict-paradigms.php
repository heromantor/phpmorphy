#!/usr/bin/env php
<?php
set_include_path(__DIR__ . '/../../src/' . PATH_SEPARATOR . get_include_path());
require('phpMorphy.php');

if($argc < 2) {
    die("Usage {$argv[0]} DICT.xml\n");
}

$xml = $argv[1];
$formatter = new phpMorphy_Paradigm_Formatter();

$source = new phpMorphy_Dict_Source_Xml($xml);

$flexias = remap_to_ids($source->getFlexias());
$ancodes = remap_to_ids($source->getAncodes());
$prefixes = remap_to_ids($source->getPrefixes());

echo "$xml: {", PHP_EOL;

$para_no = 1;
/** @var phpMorphy_Dict_Lemma $lemma */
foreach($source->getLemmas() as $lemma) {
    $common_grammems = array();
    if($lemma->hasAncodeId()) {
        /** @var phpMorphy_Dict_Ancode $common_ancode */
        $common_ancode = $ancodes[$lemma->getAncodeId()];
        $common_grammems = $common_ancode->getGrammems();
    }

    $flexia_model = $flexias[$lemma->getFlexiaId()];
    $paradigm = new phpMorphy_Paradigm_ArrayBased(false);

    /** @var phpMorphy_Dict_Flexia $flexia */
    foreach($flexia_model as $flexia) {
        /** @var phpMorphy_Dict_Ancode $ancode */
        $ancode = $ancodes[$flexia->getAncodeId()];

        $wf = new phpMorphy_WordForm_WordForm();
        $wf->setBase($lemma->getBase());
        $wf->setFormPrefix($flexia->getPrefix());
        $wf->setSuffix($flexia->getSuffix());
        $wf->setCommonGrammems($common_grammems);
        $wf->setPartOfSpeech($ancode->getPartOfSpeech());
        $wf->setFormGrammems($ancode->getGrammems());
        
        $paradigm->append($wf);
    }

    printf("  Paradigm %2d.\n%s", $para_no++, $formatter->format($paradigm, '  '));
}

echo '}' . PHP_EOL;

function remap_to_ids($ary) {
    $result = array();

    foreach($ary as $value) {
        $result[$value->getId()] = $value;
    }

    return $result;
}