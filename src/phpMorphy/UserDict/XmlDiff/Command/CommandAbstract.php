<?php
/*
* This file is part of phpMorphy project
*
* Copyright (c) 2007-2012 Kamaev Vladimir <heromantor@users.sourceforge.net>
*
*     This library is free software; you can redistribute it and/or
* modify it under the terms of the GNU Lesser General Public
* License as published by the Free Software Foundation; either
* version 2 of the License, or (at your option) any later version.
*
*     This library is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
* Lesser General Public License for more details.
*
*     You should have received a copy of the GNU Lesser General Public
* License along with this library; if not, write to the
* Free Software Foundation, Inc., 59 Temple Place - Suite 330,
* Boston, MA 02111-1307, USA.
*/


abstract class phpMorphy_UserDict_XmlDiff_Command_CommandAbstract {
    const MATCH_ONLY_LEMMAS = true;

    /** @var phpMorphy_UserDict_EncodingConverter */
    protected $encoding_converter;
    /** @var phpMorphy_UserDict_PatternMatcher */
    protected $pattern_matcher;
    /** @var phpMorphy_UserDict_XmlDiff_ParadigmContainer */
    protected $paradigms_container;
    /** @var phpMorphy_MorphyInterface */
    protected $morphy;

    function __construct(
        phpMorphy_UserDict_EncodingConverter $encodingConverter,
        phpMorphy_UserDict_PatternMatcher $patternMatcher,
        phpMorphy_MorphyInterface $morphy
    ) {
        $this->encoding_converter = $encodingConverter;
        $this->pattern_matcher = $patternMatcher;
        $this->morphy = $morphy;
    }

    /**
      * @param phpMorphy_Paradigm_ParadigmInterface[] $paradigms
      * @param phpMorphy_UserDict_Pattern $pattern
      * @param int &$formIndex
      * @return phpMorphy_WordForm_WordFormInterface
      */
     protected function findSuitableFormByPattern($paradigms, phpMorphy_UserDict_Pattern $pattern, &$formIndex = null) {
         return $this->pattern_matcher->findSuitableFormByPattern(
             $paradigms,
             $pattern,
             self::MATCH_ONLY_LEMMAS,
             $formIndex
         );
     }

     /**
      * @param string $string
      * @return string
      */
     private function toInternalEncoding($string) {
         return $this->encoding_converter->toInternal($string);
     }

     /**
      * @param string $string
      * @return string
      */
     private function toMorphyEncoding($string) {
         return $this->encoding_converter->toMorphy($string);
     }

    /**
     * @param phpMorphy_Paradigm_ParadigmInterface $paradigm
     * @param array $visited
     * @return void
     */
    protected function appendParadigmRecursive(
        phpMorphy_Paradigm_ParadigmInterface $paradigm,
        &$visited = array()
    ) {
        $this->paradigms_container->append($paradigm);

        $paradigm_set = $this->morphy->findWord($paradigm->getAllForms(), phpMorphy::IGNORE_PREDICT);

        foreach($paradigm_set as $paradigm_collection) {
            foreach($paradigm_collection as $found_paradigm) {
                $paradigm_hash = $found_paradigm->getHash();

                if(!isset($visited[$paradigm_hash])) {
                    $visited[$paradigm_hash] = true;

                    $this->appendParadigmRecursive(
                        $this->normalizeMorphyParadigmEncoding($found_paradigm),
                        $visited
                    );
                }
            }
        }
    }

    /**
     * @param phpMorphy_Paradigm_ParadigmInterface $paradigm
     * @return phpMorphy_Paradigm_ParadigmInterface
     */
    protected function normalizeMorphyParadigmEncoding(phpMorphy_Paradigm_FsaBased $paradigm) {
        if(!$this->encoding_converter->isAffect()) {
            return $paradigm;
        }
        
        $result = new phpMorphy_Paradigm_ArrayBased();

        for($i = 0, $c = count($paradigm); $i < $c; $i++) {
            $ary = $paradigm->getWordFormAsArray($i);

            foreach($ary as &$v) {
                if(is_string($v)) {
                    $v = $this->toInternalEncoding($v);
                }
            }

            $result->append(new phpMorphy_WordForm_WordForm($ary));
        }

        return $result;
    }

    /**
     * @param string $word
     * @return phpMorphy_Paradigm_ParadigmInterface[]
     */
    protected function findWordMorphy($word) {
        if(false !== ($paradigms = $this->morphy->findWord($this->toMorphyEncoding($word), phpMorphy::IGNORE_PREDICT))) {
            $result = array();
            foreach($paradigms as $paradigm) {
                $result[] = $this->normalizeMorphyParadigmEncoding($paradigm);
            }

            return $result;
        }

        return false;
    }

    /**
     * @param string $word
     * @return phpMorphy_Paradigm_ParadigmInterface[]
     */
    protected function findWordInternal($word, &$indices = null) {
        return $this->paradigms_container->findWord($word, self::MATCH_ONLY_LEMMAS, $indices);
    }
}