<?php
function explode_std($string) {
    return explode("\0", $string);
}

function explode_pascal2($string, $maxLen) {
    $result = array();
    
    for($i = 0; $i < $maxLen;) {
	    $l = ord($string[$i]);
	    $result[] = substr($string, $i + 1, $l);
	    $i += $l + 1;
    }
    
    return $result;
}

function explode_pascal($string, $maxLen) {
    $result = array();
    $fmt = '';
    
    for($i = 0; $i < $maxLen;) {
        $l = ord($string[$i]);
        //$fmt .= "C/a{$l}v$i/";
	    $i += $l + 1;
    }
    
//    var_dump($fmt, $i, $maxLen); die;
    return 1;//unpack($fmt, $string);
}

$var1 = "abc\0def\0eee\0";
$var2 = "\x03abc\x03def\x03eee";
$max_len = strlen($var2);


$b = microtime(true);
for($i = 0; $i < 1e4; $i++) {
    //$r = explode_std($var1);
    $r = explode_pascal($var2, $max_len);
}

$e = microtime(true);

echo $e-$b, PHP_EOL;
var_dump($r);
