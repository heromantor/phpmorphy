#!/usr/bin/env php
<?php
set_include_path(__DIR__ . '/../src/' . PATH_SEPARATOR . get_include_path());
require('phpMorphy.php');

$user_dict_xml = PHPMORPHY_DIR . '/../bin/user_dict.xml';
$outXmlFilePath = PHPMORPHY_DIR . '/../bin/out.xml';
$morphy_dict_dir = PHPMORPHY_DIR . '/../dicts/utf-8';
$morphy_dict_lang = 'ru_RU';
$verbose = true;

/**
 *
 * @param string $dir
 * @param string $lang
 * @return phpMorphy_MorphyInterface
 */
function createMorphy($dir, $lang) {
    $opts = array(
        'storage' => PHPMORPHY_STORAGE_MEM,
        'predict_by_suffix' => true,
        'predict_by_db' => true,
    );

    return new phpMorphy($dir, $lang, $opts);
}

$morphy = createMorphy($morphy_dict_dir, $morphy_dict_lang);

$encoding_converter = new phpMorphy_UserDict_EncodingConverter(
    $morphy->getEncoding(), $morphy->isInUpperCase() ? MB_CASE_UPPER : MB_CASE_LOWER,
    'utf-8', MB_CASE_UPPER
);

//$errors_handler = new phpMorphy_UserDict_Log_ErrorsHandlerException();
$errors_handler = new phpMorphy_UserDict_Log_ErrorsHandlerPass();

$observer = new phpMorphy_UserDict_Log_CLI(
    $verbose,
    $errors_handler,
    $encoding_converter
);

$b = microtime(true);

if(1) {
    phpMorphy_UserDict_XmlDiff_Generator::convertFromXmlToXml(
        $user_dict_xml,
        $outXmlFilePath,
        $morphy,
        $observer,
        $encoding_converter
    );
}

$e = microtime(true);

printf("Time taken: %0.2f\n", $e - $b);
