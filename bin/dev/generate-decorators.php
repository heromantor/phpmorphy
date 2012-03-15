#!/usr/bin/env php
<?php
set_include_path(__DIR__ . '/../../src/' . PATH_SEPARATOR . get_include_path());
require('phpMorphy.php');

$longopts = array(
    "generate-from-file:",
    "decoratee:",
    "decorator:",
    "regenerate"
);

if(false === ($opts = getopt("", $longopts))) {
    print_usage();
}

if(!(isset($opts['decoratee']) || isset($opts['generate-from-file']) || isset($opts['regenerate']))) {
    print_usage();
}

$out_dir = PHPMORPHY_DIR;

if(isset($opts['generate-from-file'])) {
    $input_file = $opts['generate-from-file'];

    if(false === $input_file) {
        print_usage();
    }

    generate_decorators_from_file($input_file, $out_dir, 'logger');
} else if(isset($opts['decoratee'])) {
    if(false === $opts['decorator'] || false === $opts['decoratee']) {
        print_usage();
    }

    $decoratee = $opts['decoratee'];
    $decorator = $opts['decorator'];

    generate_decorator($decoratee, $decorator, $out_dir, 'logger');
} else if(isset($opts['regenerate'])) {
    $root_dir = PHPMORPHY_DIR . '/phpMorphy';

    phpMorphy_Util_Fs::applyToEachFile(
        $root_dir,
        '/Decorator\.php$/',
        function($path) use ($out_dir) {
            regenerate_decorator(
                $path,
                $out_dir,
                'logger'
            );
        }
    );
} else {
    print_usage();
}

function generate_decorators_from_file($fileName, $outDir, $log) {
    $file_descriptor = phpMorphy_Generator_PhpFileParser::parseFile($fileName);

    foreach($file_descriptor->classes as $class_desc) {
        $class = $class_desc->name;

        $decorator = phpMorphy_Generator_Decorator_Generator::getDecoratorClassName($class);
        generate_decorator($class, $decorator, $outDir, $log);
    }
}

function generate_decorator($class, $decorator, $outDir, $log) {
    $new_file_path =
            $outDir . DIRECTORY_SEPARATOR .
            phpMorphy_Loader::classNameToFilePath($decorator);

    $header = phpMorphy_Generator_Decorator_PhpDocHelper::parseHeaderPhpDoc(
        @file_get_contents($new_file_path)
    );

    if(!$header->auto_regenerate && is_readable($new_file_path)) {
        $log("- Skip generating $decorator decorator (auto_regenerate disabled, or not exist)");
        return;
    }

    // try generate decorators on class
    $code = '<' . '?php' . PHP_EOL;

    $code .= phpMorphy_Generator_Decorator_Generator::generate($class, $decorator);

    @mkdir(dirname($new_file_path), 0744, true);
    file_put_contents($new_file_path, $code);

    $log("+ $decorator generated");
}

function regenerate_decorator($fileName, $outDir, $log) {
    $content = file_get_contents($fileName);

    $header = phpMorphy_Generator_Decorator_PhpDocHelper::parseHeaderPhpDoc($content);

    if(false === $header->decoratee_class) {
        $log("- Skip $fileName (decorate_class not specified)");
        return;
    }

    if(false === $header->decorator_class) {
        $log("- Skip $fileName (decorator_class not specified)");
        return;
    }

    generate_decorator(
        $header->decoratee_class,
        $header->decorator_class,
        $outDir,
        $log
    );
}

function print_usage() {
    global $argv;
    die("Usage {$argv[0]} [--generate FILE_NAME] [--regenerate] [--decoratee=DECORATEE_NAME --decorator=DECORATOR_NAME]");
}

function logger($msg) {
    fprintf(STDERR, $msg . PHP_EOL);
}
