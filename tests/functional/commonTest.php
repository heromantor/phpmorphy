<?php
class test_Functional_Common extends PHPUnit_Framework_TestCase {
    protected
        /**
         * @var phpMorphy_MorphyInterface
         */
        $morphy;

    function setUp() {
        $opts = array(
            'storage' => phpMorphy::STORAGE_FILE,
            'predict_by_suffix' => true,
            'predict_by_db' => true,
        );
        
        $this->morphy = new phpMorphy(
            __DIR__ . '/../../dicts/utf-8',
            'ru_RU',
            $opts
        );
    }

    function testVerySimple() {
        //$this->assertLemma('мама', 'мама');
        $this->assertLemma('мыла', 'мыть', 'мыло');
        $this->assertLemma('раму', 'рама', 'рам');
    }

    function testPredictByKnownSuffix() {
        $this->assertLemma('кластера', 'кластер');
        $this->assertNotPredicted();
        $this->assertLemma('мегакластера', 'мегакластер');
        $this->assertPredictedByKnownSuffix();
    }

    function testPredictByKnownSuffixWithSuffix() {
        $this->assertLemma('наикрасивейшего', 'красивый');
        $this->assertNotPredicted();
        $this->assertLemma('пренаикрасивейшего', 'прекрасивый');
        $this->assertPredictedByKnownSuffix();
    }

    function testNotSplitParadigms() {
        $paradigms = $this->morphy->findWord($this->toMorphyEncoding('айда'));

        $this->assertEquals(1, count($paradigms));
    }

    function testFindWord() {
        $paradigms = $this->morphy->findWord($this->toMorphyEncoding('мыла'));
        $this->assertEquals(2, count($paradigms));

        foreach($paradigms as $paradigm) {
            $all_forms = $paradigm->getAllForms();
            foreach($paradigm as $wf) {
                $word = $wf->getWord();
                $this->assertTrue(in_array($word, $all_forms), "$word failed");
            }
        }

        foreach($paradigms as $paradigm) {
            $all_forms = $paradigm->getAllForms();
            for($i = 0; $i < count($paradigm); $i++) {
                $word = $paradigm->getWordForm($i)->getWord();
                $this->assertTrue(in_array($word, $all_forms), "$word failed");
            }
        }
    }

    function testFindWord_GetFoundWord() {
        $paradigm = $this->morphy->findWord($this->toMorphyEncoding('программе'));
        $this->assertEquals(1, count($paradigm));
        $paradigm = $paradigm[0];

        $this->assertEquals(2, count($paradigm->getFoundWordForm()));
    }

    protected function toMorphyEncoding($string) {
        return mb_strtoupper($string, 'utf-8');
    }

    protected function assertLemma($lemma) {
        $expected = func_get_args();
        array_shift($expected);

        $this->assertEqualsArrays(
            $expected,
            $this->morphy->lemmatize($this->toMorphyEncoding($lemma))
        );
    }

    protected function assertEqualsArrays($expected, $actual) {
        $this->normalizeArray($expected);
        $this->normalizeArray($actual);

        $msg = "Morphy returns " .
               (false === $actual ? 'FALSE' : implode(', ', $actual)) . ' but ' .
               (false === $expected ? 'FALSE' : implode(', ', $expected)) . ' expected';
        
        $this->assertEquals($expected, $actual, $msg);
    }

    protected function normalizeArray(&$array) {
        if(false !== $array) {
            $old_encoding = mb_internal_encoding();
            mb_internal_encoding('utf-8');

            $array = array_map('mb_strtoupper', array_values((array)$array));
            sort($array);
            
            mb_internal_encoding($old_encoding);
        }
    }

    protected function assertNotPredicted() {
        $this->assertFalse($this->morphy->isLastPredicted(), "Expect for word exists in dictionary");
    }

    protected function assertPredictedByKnownSuffix() {
        $this->assertEquals(
            phpMorphy::PREDICT_BY_SUFFIX,
            $this->morphy->getLastPredictionType(),
            "Expect for prediction by known suffix"
        );
    }

    protected function assertPredictedBySuffix() {
        $this->assertEquals(
            phpMorphy::PREDICT_BY_DB,
            $this->morphy->getLastPredictionType(),
            "Expect for prediction by suffix"
        );
    }
}
