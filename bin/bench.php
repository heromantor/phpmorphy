#!/usr/bin/env php
<?php
error_reporting(E_ALL | E_STRICT);

define('REMOVE_DUPLICATES', true);
define('PATH_TO_MYSTEM', __DIR__ . '/../../mystem/mystem');
define('DICTS_DIR', __DIR__ . '/../dicts');
define('MORPHY_2X_DIR', __DIR__ . '/../../phpmorphy-php-2.5');
define('MORPHY_2X_DICTS_DIR', MORPHY_2X_DIR . '/dicts');

if($argc < 2) {
    die("Usage {$argv[0]} TEXT_FILE [ENCODING] [FILE_ENCODING]" . PHP_EOL);
}

$cwd = getcwd();
$text_file = $argv[1];
$encoding = $argc > 2 ? $argv[2] : 'utf-8';
$file_encoding = $argc > 3 ? $argv[3] : 'utf-8';
$lang = 'rus';

$morphy_ver = getenv('PHPMORPHY_VER');
if($morphy_ver !== "0.2") {
    set_include_path(__DIR__ . '/../src/' . PATH_SEPARATOR . get_include_path());
    require('phpMorphy.php');
} else {
    require_once(MORPHY_2X_DIR . '/src/common.php');
}

$dict_dir = PHPMORPHY_DIR . '/../dicts/' . $encoding;

$words = load_words($text_file, REMOVE_DUPLICATES, $encoding, $file_encoding);

echo "Total words " . (REMOVE_DUPLICATES ? "(unique)" : '') . " = " . count($words) . PHP_EOL;

//print_memory_usage();
//bench_mystem($words, $encoding, PATH_TO_MYSTEM);
bench_porter($words, $encoding);
//bench_enchant($words);
//bench_pspell($words);

//print_memory_usage();
bench_morphy_dict($words, $encoding, $dict_dir, $lang, PHPMORPHY_STORAGE_FILE);
bench_morphy_dict($words, $encoding, $dict_dir, $lang, PHPMORPHY_STORAGE_SHM);
bench_morphy_dict($words, $encoding, $dict_dir, $lang, PHPMORPHY_STORAGE_MEM);

bench_morphy($words, $encoding, $dict_dir, $lang, PHPMORPHY_STORAGE_FILE, false);
bench_morphy($words, $encoding, $dict_dir, $lang, PHPMORPHY_STORAGE_FILE, true);

bench_morphy($words, $encoding, $dict_dir, $lang, PHPMORPHY_STORAGE_SHM, false);
bench_morphy($words, $encoding, $dict_dir, $lang, PHPMORPHY_STORAGE_SHM, true);

//print_memory_usage();
bench_morphy($words, $encoding, $dict_dir, $lang, PHPMORPHY_STORAGE_MEM, false);
bench_morphy($words, $encoding, $dict_dir, $lang, PHPMORPHY_STORAGE_MEM, true);

//file_put_contents(__DIR__ . '/.bench.words.txt', implode(PHP_EOL, $words));
print_memory_usage();
/////////////////////////////////////////////////////////////////

function print_memory_usage() {
    printf("%dKb allocated\n", memory_get_usage() >> 10);
}

function load_words($filePath, $unique, $fileEncoding) {
   $contents = @iconv($fileEncoding, 'utf-8', file_get_contents($filePath));
   preg_match_all('/[\s\"\']([А-ЯЁа-яё+-]+)/u', file_get_contents($filePath), $result);

   $converted = array_map('trim', $result[0]);

   return $unique ? array_unique($converted) : $converted;
}

function convert_words(&$words, $encoding, $case) {
    foreach($words as &$word) {
        $word = iconv('utf-8', $encoding, mb_convert_case($word, $case, 'utf-8'));
    }
}

function bench_enchant($words) {
    // TODO: check return values!!!
    echo "Bench Enchant: ";
	$tag = 'ru_RU';

	$r = enchant_broker_init();
	
	if (!enchant_broker_dict_exists($r,$tag)) {
		echo "$tag dict not supported by enchant\n";
		return false;
	}
	
	$d = enchant_broker_request_dict($r, $tag);

    $not_found = 0;
    $b = microtime(true);
    foreach($words as $word) {
//        if(false === enchant_dict_quick_check($d, $word/*, $sugg*/)) { // this cause segfault
        if(false === enchant_dict_check($d, $word)) {
            enchant_dict_suggest($d, $word);
            $not_found++;
        }
    }
    $e = microtime(true);
    
    printf("time = %0.2f sec, words per second = %0.2f, not found = %d\n", $e - $b, count($words) / ($e - $b), $not_found);
    
	enchant_broker_free_dict($d);
	
	enchant_broker_free($r);
}

function bench_pspell($words) {
    // TODO: check return values!!!
    echo "Bench Pspell: ";
	$tag = 'ru';

	$pspell_link = pspell_new($tag, "", "", "",
                           (PSPELL_FAST|PSPELL_RUN_TOGETHER));
                           
    $not_found = 0;
    $b = microtime(true);
    foreach($words as $word) {
        if(false === pspell_check($pspell_link, $word)) {
            pspell_suggest($pspell_link, $word);
            $not_found++;
        }
    }
    $e = microtime(true);
    
    printf("time = %0.2f sec, words per second = %0.2f, not found = %d\n", $e - $b, count($words) / ($e - $b), $not_found);
}

function bench_morphy($words, $encoding, $dictDir, $lang, $storage, $useBulk, $usePrediction = true) {
    $opts = array(
        'storage' => $storage,
        'predict_by_suffix' => $usePrediction,
        'predict_by_db' => false,//$usePrediction,
    );

    $bundle = new phpMorphy_FilesBundle($dictDir, $lang);
    $morphy = new phpMorphy($bundle, $opts);
    $unicode = phpMorphy_UnicodeHelper_UnicodeHelperAbstract::getHelperForEncoding($morphy->getEncoding());

    echo "Bench phpMorphy[$encoding][$storage][" . ($useBulk ? 'BULK' : 'SINGLE') . "] : ";

    convert_words($words, $encoding, MB_CASE_UPPER);

    $predicted = 0;
    $b = microtime(true);

    if($useBulk) {
        $morphy->getBaseForm($words);
    } else {
        foreach($words as $word) {
            //$unicode->strrev($word); mb_strtoupper($word, 'utf-8');
            //strtr($word, $replace);
            //strrev($word);
            //mb_strtolower($word, 'utf-8');
            $lemma = $morphy->getBaseForm($word);
            
            if($morphy->isLastPredicted()) {
                $predicted++;
            }
        }
    }
    
    $e = microtime(true);
    
    printf("time = %0.2f sec, words per second = %0.2f, predicted = %d\n", $e - $b, count($words) / ($e - $b), $predicted);
}

function bench_morphy_dict($words, $encoding, $dictDir, $lang, $storage) {
    $opts = array(
        'storage' => $storage,
        'predict_by_suffix' => false,
        'predict_by_db' => false,
    );

    $bundle = new phpMorphy_FilesBundle($dictDir, $lang);
    $morphy = new phpMorphy($bundle, $opts);

    echo "Bench phpMorphy - Dict[$encoding][$storage]: ";

    convert_words($words, $encoding, MB_CASE_UPPER);

    $fsa = $morphy->getCommonMorphier()->getFinder()->getFsa();
    $root = $fsa->getRootTrans();
    $predicted = 0;
    $b = microtime(true);

    foreach($words as $word) {
        $result = $fsa->walk($root, $word, true);
    }

    $e = microtime(true);

    printf("time = %0.2f sec, words per second = %0.2f, predicted = %d\n", $e - $b, count($words) / ($e - $b), $predicted);
}

function bench_porter($words, $encoding) {
    echo "Bench snowball[$encoding]: ";

    convert_words($words, $encoding, MB_CASE_LOWER);

    $stemmer = new Lingua_Stem_Ru($encoding, true);

    $b = microtime(true);
    foreach($words as $word) {
        $lemma = $stemmer->stem_word($word);
        //var_dump($word, $lemma);
    }
    $e = microtime(true);
    
    printf("time = %0.2f sec, words per second = %0.2f\n", $e - $b, count($words) / ($e - $b));
}

function filter_word_for_mystem($word) {
    // TODO: hack!
    global $encoding;

    $word = @iconv($encoding, 'utf-8//IGNORE', $word);
    $word = preg_replace('/^[^А-Яа-я]+$/u', '', $word);
    $word = iconv('utf-8', $encoding, $word);

    return $word;
}

function bench_mystem_proc_open($words, $encoding, $pathToMystem) {
    $cmd = $pathToMystem . " -e $encoding -nl";
    $descriptors = array(
        0 => array("pipe", "r"),  // stdin
        1 => array("pipe", "w"),  // stdout
    );
    $pipes = array();
    
    if(false === ($handle = proc_open($cmd, $descriptors, $pipes))) {
        die("Can`t invoke mystem(cmd = '$cmd')");
    }

    foreach($words as $word) {
        fwrite($pipes[0], $word . PHP_EOL);
        $lemmas = explode('|', rtrim(fgets($pipes[1])));
    }

    foreach($pipes as $pipe) {
        fclose($pipe);
    }
    proc_close($handle);
}

function bench_mystem_exec($words, $encoding, $pathToMystem) {
    $temp_dir = sys_get_temp_dir();
    $in_temp_name = tempnam($temp_dir, 'mystem');
    $out_temp_name = tempnam($temp_dir, 'mystem');

    file_put_contents($in_temp_name, implode(PHP_EOL, $words));

    $cmd = $pathToMystem . " -e $encoding -n " . escapeshellarg($in_temp_name);

    exec($cmd, $output, $code);

    if(!is_array($output) || !count($output)) {
        die("Can`t invoke mystem(cmd = '$cmd')");
    }

    $result = array();
    foreach($output as $line) {
        //$line = str_replace('?', '', $line);

        if(false === ($pos = strpos($line, '{'))) {
            continue;
        }

        $word = substr($line, 0, $pos);
        $lemmas = explode('|', substr($line, $pos + 1, -1));
    }
}

function bench_mystem($words, $encoding, $pathToMystem) {
    $words = array_filter(
        array_map('filter_word_for_mystem', $words),
        'strlen'
    );

    if(!is_executable($pathToMystem)) {
        echo "Can`t execute mystem binary '$pathToMystem'\n";
        return;
    }

    echo "Bench mystem[$encoding]: ";
    $b = microtime(true);
    if(1) {
        bench_mystem_exec($words, $encoding, $pathToMystem);
    } else {
        bench_mystem_proc_open($words, $encoding, $pathToMystem);
    }
    $e = microtime(true);

    printf("time = %0.2f sec, words per second = %0.2f\n", $e - $b, count($words) / ($e - $b));
}

class Lingua_Stem_Ru 
{
    var $VERSION = "0.02";
    var $Stem_Caching = false;
    var $Stem_Cache = array();
    var $VOWEL = '/аеиоуыэюя/';
    var $PERFECTIVEGROUND = '/((ив|ивши|ившись|ыв|ывши|ывшись)|((?<=[ая])(в|вши|вшись)))$/';
    var $REFLEXIVE = '/(с[яь])$/';
    var $ADJECTIVE = '/(ее|ие|ые|ое|ими|ыми|ей|ий|ый|ой|ем|им|ым|ом|его|ого|еых|ую|юю|ая|яя|ою|ею)$/';
    var $PARTICIPLE = '/((ивш|ывш|ующ)|((?<=[ая])(ем|нн|вш|ющ|щ)))$/';
    var $VERB = '/((ила|ыла|ена|ейте|уйте|ите|или|ыли|ей|уй|ил|ыл|им|ым|ены|ить|ыть|ишь|ую|ю)|((?<=[ая])(ла|на|ете|йте|ли|й|л|ем|н|ло|но|ет|ют|ны|ть|ешь|нно)))$/';
    var $NOUN = '/(а|ев|ов|ие|ье|е|иями|ями|ами|еи|ии|и|ией|ей|ой|ий|й|и|ы|ь|ию|ью|ю|ия|ья|я)$/';
    var $RVRE = '/^(.*?[аеиоуыэюя])(.*)$/';
    var $DERIVATIONAL = '/[^аеиоуыэюя][аеиоуыэюя]+[^аеиоуыэюя]+[аеиоуыэюя].*(?<=о)сть?$/';
    var $STEP2_RE = '/и$/';
    var $STEP3_RE = '/ость?$/';
    var $STEP4_1_RE = '/ь$/';
    var $STEP4_2_RE = '/ейше?/';
    var $STEP4_3_RE = '/нн$/';
    var $replacements = array();
    protected $prepare_words;
    protected $encoding;

    function __construct($encoding = null, $prepareWords = true) {
        $this->prepare_words = (bool)$prepareWords;
        
        if(null === $encoding) {
            $encoding = 'utf-8';
        }

        $this->encoding = $encoding;

        $encoding = strtolower($encoding);
        $is_utf8 = $encoding === 'utf-8';

        foreach(get_object_vars($this) as $prop_name => $prop_value) {
            if(strtoupper($prop_name) === $prop_name) {
                $this->$prop_name = iconv('utf-8', $encoding, $this->$prop_name);

                if($is_utf8) {
                    $this->$prop_name .= 'u';
                }
            }
        }

        $this->replacements = array(
          'e' => 'е',
          'yo' => 'ё',
          'n' => 'н',
        );

        foreach($this->replacements as &$value) {
            $value = iconv('utf-8', $encoding, $value);
        }
    }

    function s(&$s, $re, $to)
    {
        $orig = $s;
        $s = preg_replace($re, $to, $s);
        return $orig !== $s;
    }

    function m($s, $re)
    {
        return preg_match($re, $s);
    }

    function stem_word($word) 
    {
        if($this->prepare_words) {
            $word = mb_strtolower($word, $this->encoding);
            $word = str_replace($this->replacements['yo'], $this->replacements['e'], $word);
        }

        # Check against cache of stemmed words
        if ($this->Stem_Caching && isset($this->Stem_Cache[$word])) {
            return $this->Stem_Cache[$word];
        }
        $stem = $word;
        do {
          if (!preg_match($this->RVRE, $word, $p)) break;
          $start = $p[1];
          $RV = $p[2];
          if (!$RV) break;

          # Step 1
          if (!$this->s($RV, $this->PERFECTIVEGROUND, '')) {
              $this->s($RV, $this->REFLEXIVE, '');

              if ($this->s($RV, $this->ADJECTIVE, '')) {
                  $this->s($RV, $this->PARTICIPLE, '');
              } else {
                  if (!$this->s($RV, $this->VERB, ''))
                      $this->s($RV, $this->NOUN, '');
              }
          }

          # Step 2
          $this->s($RV, $this->STEP2_RE, '');

          # Step 3
          if ($this->m($RV, $this->DERIVATIONAL))
              $this->s($RV, $this->STEP3_RE, '');

          # Step 4
          if (!$this->s($RV, $this->STEP4_1_RE, '')) {
              $this->s($RV, $this->STEP4_2_RE, '');
              $this->s($RV, $this->STEP4_3_RE, $this->replacements['n']);
          }

          $stem = $start.$RV;
        } while(false);
        if ($this->Stem_Caching) $this->Stem_Cache[$word] = $stem;
        return $stem;
    }

    function stem_caching($parm_ref) 
    {
        $caching_level = @$parm_ref['-level'];
        if ($caching_level) {
            if (!$this->m($caching_level, '/^[012]$/')) {
                die(__CLASS__ . "::stem_caching() - Legal values are '0','1' or '2'. '$caching_level' is not a legal value");
            }
            $this->Stem_Caching = $caching_level;
        }
        return $this->Stem_Caching;
    }

    function clear_stem_cache() 
    {
        $this->Stem_Cache = array();
    }
}

