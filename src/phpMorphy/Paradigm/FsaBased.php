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

class phpMorphy_Paradigm_FsaBased implements phpMorphy_Paradigm_ParadigmInterface {
    const IS_USE_FORMS_CASHE = false;
    
    protected
        /** @var string */
        $word,
        /** @var array */
        $annot,
        /** @var phpMorphy_Helper */
        $helper,
        /** @var phpMorphy_WordForm_WithFormNo[] */
        //$cached_forms = array(),
        /** @var array */
        $forms_array,
        /** @var string */
        $cached_base,
        /** @var string */
        $cached_pseudo_root,
        /** @var string[] */
        $all_forms,
        /** @var int */
        $found_form_indices;

    /**
     * @param string $word
     * @param array $annot
     * @param phpMorphy_Helper $helper
     *
     */
    function __construct($word, $annot, phpMorphy_Helper $helper) {
        $this->word = (string)$word;
        $this->annot = array($annot);

        $this->helper = $helper;

        unset($this->forms_array);
        unset($this->found_form_indices);
    }

    /**
     * @return string
     */
    function getHash() {
        if(0 == $this->count()) {
            return 0;
        }

        $base = $this->getWordFormAsArray(0);
        
        return
            $base['common_prefix'] . '|' .
            $base['base'] . '|' .
            $this->annot[0]['offset'];
    }
    
    /**
     * @return string
     */
    function getPseudoRoot() {
        if(!isset($this->cached_pseudo_root)) {
            list($this->cached_pseudo_root) = $this->helper->getPseudoRoot($this->word, $this->annot);
        }

        return $this->cached_pseudo_root;
    }

    /**
     * @return string
     */
    function getBaseForm() {
        if(!isset($this->cached_base)) {
            list($this->cached_base) = $this->helper->getBaseForm($this->word, $this->annot);
        }

        return $this->cached_base;
    }

    /**
     * @return string
     */
    function getLemma() {
        return $this->getBaseForm();
    }

    /**
     * @return string[]
     */
    function getAllForms() {
        if(!isset($this->all_forms)) {
            $this->all_forms = $this->helper->getAllForms($this->word, $this->annot);
        }

        return $this->all_forms;
    }

    /**
     * @throws phpMorphy_Exception
     * @param int $index
     * @return phpMorphy_WordForm_WithFormNo
     */
    function getWordForm($index) {
        if(self::IS_USE_FORMS_CASHE) {
            if(!isset($this->cached_forms[$index])) {
                $this->cached_forms[$index] = $this->createWordForm($this->getWordFormAsArray($index), $index);
            }

            return $this->cached_forms[$index];
        } else {
            return $this->createWordForm($this->getWordFormAsArray($index), $index);
        }
    }

    function getWordFormAsArray($index) {
        if(!$this->offsetExists($index)) {
            throw new phpMorphy_Exception("Invalid index '$index' given");
        }

        return $this->forms_array[$index];
    }

    /**
     * @param string $word
     * @param int $form_no
     * @param string|int $ancode
     * @return phpMorphy_WordForm_WithFormNo
     */
    protected function createWordForm($data, $form_no) {
        $word_form = new phpMorphy_WordForm_WithFormNo($form_no);
        $word_form->assigmFromArray($data);

        return $word_form;
    }

    /**
     * @return array
     */
    protected function initializeFormsArray() {
        if(!isset($this->forms_array)) {
            $found_form_indices = array();
            $paradigm = $this->helper->getParadigmData($this->word, $this->annot, $found_form_indices);

            $this->found_form_indices = $found_form_indices[0];
            $this->forms_array = $paradigm[0];
        }

        return $this->forms_array;
    }

    /**
     * @return phpMorphy_WordForm_WithFormNo[]
     */
    function getFoundWordForm() {
        $low = $this->found_form_indices['low'];
        $high = $this->found_form_indices['high'];
        $result = array();

        for($c = $high + 1; $low < $c; $low++) {
            $result[] = $this->getWordForm($low);
        }

        return $result;
    }

    /**
     * @param string[]|int[] $grammems
     * @return bool
     */
    function hasGrammems($grammems) {
        settype($grammems, 'array');

        foreach($this as $wf) {
            if($wf->hasGrammems($grammems)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string[]|int[] $grammems
     * @return phpMorphy_WordForm_WithFormNo[]
     */
    function getWordFormsByGrammems($grammems) {
        settype($grammems, 'array');
        $result = array();

        foreach($this as $wf) {
            if($wf->hasGrammems($grammems)) {
                $result[] = $wf;
            }
        }

        return $result;
//        return count($result) ? $result : false;
    }

    /**
     * @param string[]|int[] $poses
     * @return bool
     */
    function hasPartOfSpeech($poses) {
        settype($poses, 'array');

        foreach($this as $wf) {
            if(in_array($wf->getPartOfSpeech(), $poses, true)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string[]|int[] $poses
     * @return phpMorphy_WordForm_WithFormNo[]
     */
    function getWordFormsByPartOfSpeech($poses) {
        settype($poses, 'array');
        $result = array();

        foreach($this as $wf) {
            if(in_array($wf->getPartOfSpeech(), $poses, true)) {
                $result[] = $wf;
            }
        }

        return $result;
//        return count($result) ? $result : false;
    }

    /**
     * @return int
     */
    function count() {
        return count($this->forms_array);
    }

    /**
     * @param int $offset
     * @return bool
     */
    function offsetExists($offset) {
        return isset($this->forms_array[$offset]);
    }

    /**
     * @throws phpMorphy_Exception
     * @param int $offset
     * @param mixed $value
     * @return void
     */
    function offsetSet($offset, $value) {
        throw new phpMorphy_Exception(__CLASS__ . " is not mutable");
    }

    /**
     * @param int $offset
     * @return phpMorphy_WordForm_WithFormNo
     */
    function offsetGet($offset) {
        return $this->getWordForm($offset);
    }
    
    /**
     * @throws phpMorphy_Exception
     * @param int $off
     * @return void
     */
    function offsetUnset($offset) {
        throw new phpMorphy_Exception(__CLASS__ . " is not mutable");
    }

    /**
     * @return ArrayIterator
     */
    function getIterator() {
        $result = array();
        for($i = 0, $c = $this->count(); $i < $c; $i++) {
            $result[] = $this->getWordForm($i);
        }

        return new ArrayIterator($result);
    }

    function __get($name) {
        switch($name) {
            case 'forms_array':
                $this->initializeFormsArray();
                return $this->forms_array;
            case 'found_form_indices':
                $this->initializeFormsArray();
                return $this->found_form_indices;
        }

        throw new phpMorphy_Exception("Unknown property '$name'");
    }
}