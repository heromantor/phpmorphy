#!/usr/bin/env php
<?php
set_include_path(__DIR__ . '/../../src/' . PATH_SEPARATOR . get_include_path());
require('phpMorphy.php');

$root_dir = PHPMORPHY_DIR . '/phpMorphy';
$out_dir = PHPMORPHY_DIR;

phpMorphy_Util_Fs::applyToEachFile(
    $root_dir,
    '/\.php$/',
    function($path) use ($out_dir) {
        convert_file(
            $path,
            $out_dir,
            'logger'
        );
    }
);

phpMorphy_Util_Fs::deleteEmptyDirectories(
    $root_dir,
    'logger'
);

function convert_file($path, $outDir, $log) {
    global $out_dir;

    $lines = array_map('rtrim', file($path));

    try {
        $descriptor = phpMorphy_Generator_PhpFileParser::parseFile($path);
    } catch (Exception $e) {
        throw new phpMorphy_Exception("Can`t parse '$path': " . $e->getMessage());
    }

    if(count($descriptor->classes) < 1) {
        return;
    }

    $first_class = $descriptor->classes[0];
    $first_significant_line = null === $first_class->phpDoc ?
            $first_class->startLine :
            $first_class->phpDoc->startLine;
    
    $header = array_slice($lines, 0, $first_significant_line - 1);
    $header = implode(PHP_EOL, $header);

    $out_files = array();
    
    $classes_count = count($descriptor->classes);
    foreach($descriptor->classes as $class_descriptor) {
        $class_name = $class_descriptor->name;
        
        $out_path =
                $outDir . DIRECTORY_SEPARATOR .
                phpMorphy_Loader::classNameToFilePath($class_name);

        if($out_path !== $path || $classes_count > 1) {
            $log("New class $class_name");
            $out_files[$out_path] = 1;

            $lines_count = $class_descriptor->endLine - $class_descriptor->startLine + 1;
            $content = implode(
                PHP_EOL,
                array_merge(
                    array(
                        $header,
                        @$class_descriptor->phpDoc->text,
                    ),
                    array_slice($lines, $class_descriptor->startLine - 1, $lines_count)
                )
            );
            
            @mkdir(dirname($out_path), 0744, true);
            file_put_contents($out_path, $content);
        }
    }

    if(count($out_files) && !isset($out_files[$path])) {
        $log("Delete unused file $path");
        @unlink($path);
    }
}

function logger($msg) {
    fprintf(STDERR, $msg . PHP_EOL);
}
