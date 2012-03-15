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



class phpMorphy_Dict_Source_Mutable implements phpMorphy_Dict_Source_SourceInterface {
    private
        /**
         * @var string
         */
        $name,
        /**
         * @var string
         */
        $lang,
        /**
         * @var string
         */
        $description,
        /**
        * @var phpMorphy_Dict_Source_Mutable_Container
        */
        $ancodes,
        /**
        * @var phpMorphy_Dict_Source_Mutable_Container
        */
        $flexias,
        /**
        * @var phpMorphy_Dict_Source_Mutable_Container
        */
        $prefixes,
        /**
        * @var phpMorphy_Dict_Source_Mutable_Container
        */
        $lemmas,
        $is_dirty = false;

    function __construct() {
        $this->ancodes = $this->createContainer();
        $this->flexias = $this->createContainer();
        $this->prefixes = $this->createContainer();
        $this->lemmas = $this->createContainer();
    }

    private function createContainer($useIdentity = true) {
        return new phpMorphy_Dict_Source_Mutable_Container($useIdentity);
    }

    function setName($name) {
        $this->name = trim($name);
    }

    function setLanguage($lang) {
        $tokens = explode('_', $lang);

        if(count($tokens) != 2) {
            throw new phpMorphy_Exception("Invalid language '$lang'");
        }

        $this->lang = $lang;
    }

    function setDescription($desc) {
        $this->desc = trim($desc);
    }

    function appendAncode(phpMorphy_Dict_Ancode $ancode, $reuseIfExists = true) {
        $this->is_dirty = true;
        return $this->ancodes->append($ancode, $reuseIfExists);
    }

    function appendFlexiaModel(phpMorphy_Dict_FlexiaModel $model, $reuseIfExists = true, $check = true) {
        if($check && !$this->checkConsistencyForFlexiaModel($model)) {
            throw new phpMorphy_Exception("Flexia model in non consistent state");
        }

        $this->is_dirty = true;
        return $this->flexias->append($model, $reuseIfExists);
    }

    function appendPrefix(phpMorphy_Dict_PrefixSet $prefix, $reuseIfExists = true) {
        $this->is_dirty = true;
        return $this->prefixes->append($prefix, $reuseIfExists);
    }

    function appendLemma(phpMorphy_Dict_Lemma $lemma, $check = true) {
        if($check && !$this->checkConsistencyForLemma($lemma)) {
            throw new phpMorphy_Exception("Lemma in non consistent state");
        }

        $this->is_dirty = true;
        return $this->lemmas->append($lemma, true);
    }

    function deletePrefixSet($prefixSetId) {
        $this->prefixes->deleteById($prefixSetId);
    }

    function deleteAncode($ancodeId) {
        $this->ancodes->deleteById($ancodeId);
    }

    function deleteFlexiaModel($modelId) {
        $model = $this->flexias->getById($modelId);

        /* @var $flexia phpMorphy_Dict_Flexia */
        foreach($model as $flexia) {
            $this->deleteAncode($flexia->getAncodeId());
        }

        $this->flexias->deleteById($modelId);
    }

    function deleteLemma($lemmaId) {
        /* @var $lemma phpMorphy_Dict_Lemma */
        $lemma = $this->lemmas->getById($lemmaId);

        if($lemma->hasPrefixId()) {
            $this->deletePrefixSet($lemma->getPrefixId());
        }

        if($lemma->hasAncodeId()) {
            $this->deleteAncode($lemma->getAncodeId());
        }

        $this->deleteFlexiaModel($lemma->getFlexiaId());
        $this->lemmas->deleteById($lemmaId);
    }

    private function checkConsistencyForFlexiaModel(phpMorphy_Dict_FlexiaModel $model) {
        foreach($model->getFlexias() as $flexia) {
            if(!$this->ancodes->hasId($flexia->getAncodeId())) {
                return false;
            }
        }

        return true;
    }

    private function checkConsistencyForLemma(phpMorphy_Dict_Lemma $lemma) {
        if($lemma->hasAncodeId()) {
            if(!$this->ancodes->hasId($lemma->getAncodeId())) {
                return false;
            }
        }

        if($lemma->hasPrefixId()) {
            if(!$this->prefixes->hasId($lemma->getPrefixId())) {
                return false;
            }
        }

        if(!$this->flexias->hasId($lemma->getFlexiaId())) {
            return false;
        }

        return true;
    }

    function checkForConsistency(&$error = null) {
        foreach($this->flexias as $model) {
            if(!$this->checkConsistencyForFlexiaModel($model)) {
                $error = "FlexiaModel with '" . $model->getId() . "' id in non consistent state";

                return false;
            }
        }

        $i = 0;
        foreach($this->lemmas as $lemma) {
            if(!$this->checkConsistencyForLemma($lemma)) {
                $error = "Lemma with '$i' id in non consistent state";

                return false;
            }
            $i++;
        }

        return true;
    }

    function deleteUnusedModels() {
        $this->ancodes->deleteUnused();
        $this->flexias->deleteUnused();
        $this->prefixes->deleteUnused();
        $this->lemmas->deleteUnused();

        if(function_exists('gc_collect_cycles')) {
            gc_collect_cycles();
        }
    }

    function clearModels() {
        $this->ancodes->clear();
        $this->flexias->clear();
        $this->prefixes->clear();
        $this->lemmas->clear();
    }

    protected function checkForConsistencyInternal() {
        if($this->is_dirty) {
            $error = '';
            if(!$this->checkForConsistency($error)) {
                throw new phpMorphy_Exception("Source in inconsistent state: $error");
            }

            $this->is_dirty = false;
        }
    }

    function getName() {
        return $this->name;
    }

    function getLanguage() {
        if(null === $this->lang) {
            throw new phpMorphy_Exception("Language not specified");
        }

        return $this->lang;
    }

    function getDescription() {
        if(strlen($this->description)) {
            return $this->description;
        }

        return 'Source "' . $this->getName() . '" for "' . $this->getLanguage() . '" language';
    }

    /**
     * @param mixed $id
     * @return phpMorphy_Dict_FlexiaModel
     */
    function getFlexiaModelById($id) {
        return $this->flexias->getById($id);
    }

    /**
     * @param mixed $id
     * @return phpMorphy_Dict_PrefixSet
     */
    function getPrefixSetById($id) {
        return $this->prefixes->getById($id);
    }

    /**
     * @param mixed $id
     * @return phpMorphy_Dict_Ancode
     */
    function getAncodeById($id) {
        return $this->ancodes->getById($id);
    }

    function getAncodes() {
        $this->checkForConsistencyInternal();
        return $this->ancodes->getIterator();
    }

    function getFlexias() {
        $this->checkForConsistencyInternal();
        return $this->flexias->getIterator();
    }

    function getPrefixes() {
        $this->checkForConsistencyInternal();
        return $this->prefixes->getIterator();
    }

    function getAccents() {
        $this->checkForConsistencyInternal();
        return new ArrayIterator(array());
    }

    function getLemmas() {
        $this->checkForConsistencyInternal();
        return $this->lemmas->getIterator();
    }
}