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

class phpMorphy_Aot_GramTab_GramInfo {

    protected
        /** @var bool */
        $is_predict,
        /** @var string */
        $ancode_id,
        /** @var string */
        $pos,
        /** @var string[] */
        $grammems;

    /**
     * @param string|null $partOfSpeech
     * @param string[] $grammems
     * @param string $ancodeId
     * @param bool $isPredict
     *
     */
    function __construct($partOfSpeech, $grammems, $ancodeId, $isPredict) {
/*
        if(strlen($ancode) != 2) {
            throw new phpMorphy_Aot_GramTab_Exception("Invalid ancode '$ancode' given, ancode length must be 2 bytes long");
        }
*/

        $this->ancode_id = $ancodeId;
        $this->pos = $partOfSpeech;
        $this->is_predict = (bool)$isPredict;

        $this->grammems = (array)$grammems;
    }

    function getPartOfSpeech() {
        return $this->pos;
    }

    function getPartOfSpeechLong() {
        return $this->pos;
    }

    function getAncodeId() {
        return $this->ancode_id;
    }

    function getGrammems() {
        return $this->grammems;
    }

    function isPartOfSpeechProductive() {
        return $this->is_predict;
    }
};