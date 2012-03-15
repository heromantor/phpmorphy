#!/usr/bin/env php
<?php
if($argc < 2) {
    die("Usage {$argv[0]} FILE [OUTPUT_DIR]");
}

set_include_path(__DIR__ . '/../../src/' . PATH_SEPARATOR . get_include_path());
require('phpMorphy.php');
require('PHPUnit/Util/File.php');

define('DEFAULT_TESTS_DIR', PHPMORPHY_DIR . '/../tests');

$phpunit_bin = '/usr/bin/env phpunit';
$phpunit_args = '--bootstrap ' . escapeshellarg(DEFAULT_TESTS_DIR . '/bootstrap.php');
$remove_class_prefix = 'phpMorphy_';
$input_file = (string)$argv[1];
$output_dir = $argc > 2 ? (string)$argv[2] : DEFAULT_TESTS_DIR . '/unit';
//////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////

$output_dir = realpath($output_dir);
$output_dir = rtrim($output_dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

$classes = PHPUnit_Util_File::getClassesInFile($input_file);
foreach($classes as $class_name => $class_descriptor) {
    echo "==> Generate test for $class_name class\n";
    
    $stripped_class_name = substr($class_name, strlen($remove_class_prefix));
    
    $test_class_path = phpMorphy_Loader::classNameToFilePath($stripped_class_name);

    $test_file_path =
            $output_dir .
            preg_replace('/\.php$/u', '', $test_class_path) .
            'Test.php';
    
    $test_class_name = 'test_' . $stripped_class_name;

    $args = array(
        '--skeleton-test',
        $class_name,
        $input_file,
        $test_class_name,
        $test_file_path
    );

    $cmd =
        $phpunit_bin . ' ' .
        ('' !== $phpunit_args ? ($phpunit_args . ' ') : '') .
        implode(' ', array_map('escapeshellarg', $args));

    @mkdir(dirname($test_file_path));
    
    passthru($cmd);
}
