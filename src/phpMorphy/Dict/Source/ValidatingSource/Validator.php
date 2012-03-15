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



class phpMorphy_Dict_Source_ValidatingSource_Validator implements phpMorphy_Dict_Source_ValidatingSource_ValidatorInterface {
    protected
        /**
         * @var phpMorphy_Dict_Source_ValidatingSource_ValidatorSection
         */
         $flexias,
        /**
         * @var phpMorphy_Dict_Source_ValidatingSource_ValidatorSection
         */
         $accents,
        /**
         * @var phpMorphy_Dict_Source_ValidatingSource_ValidatorSection
         */
         $sessions,
        /**
         * @var phpMorphy_Dict_Source_ValidatingSource_ValidatorSection
         */
         $prefixes,
        /**
         * @var phpMorphy_Dict_Source_ValidatingSource_ValidatorSection
         */
         $ancodes,
        /**
         * @var phpMorphy_Dict_Source_ValidatingSource_ValidatorSection
         */
         $poses,
        /**
         * @var phpMorphy_Dict_Source_ValidatingSource_ValidatorSection
         */
         $grammems;

    public function __construct() {
        $this->accents = $this->createSectionValidator();
        $this->ancodes = $this->createSectionValidator();
        $this->flexias = $this->createSectionValidator();
        $this->grammems = $this->createSectionValidator();
        $this->poses = $this->createSectionValidator();
        $this->prefixes = $this->createSectionValidator();
        $this->sessions = $this->createSectionValidator();
    }

    protected function createSectionValidator() {
        return new phpMorphy_Dict_Source_ValidatingSource_ValidatorSection();
    }

    public function getPosesValidator() {
        return $this->poses;
    }

    public function getGrammemsValidator() {
        return $this->grammems;
    }

    public function getFlexiasValidator() {
        return $this->flexias;
    }

    public function getAccentsValidator() {
        return $this->accents;
    }

    public function getSessionsValidator() {
        return $this->sessions;
    }

    public function getPrefixesValidator() {
        return $this->prefixes;
    }

    public function getAncodesValidator() {
        return $this->ancodes;
    }

    function validateFlexiaId($id) { return $this->flexias->hasId($id); }
    function validateAccentId($id) { return $this->accents->hasId($id); }
    function validateSessionId($id) { return $this->sessions->hasId($id); }
    function validatePrefixId($id) { return $this->prefixes->hasId($id); }
    function validateAncodeId($id) { return $this->ancodes->hasId($id); }
    function validatePartOfSpeechId($id) {  return $this->poses->hasId($id); }
    function validateGrammemId($id) { return $this->grammems->hasId($id); }
}