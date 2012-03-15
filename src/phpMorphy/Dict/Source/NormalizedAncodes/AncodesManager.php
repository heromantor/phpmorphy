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



class phpMorphy_Dict_Source_NormalizedAncodes_AncodesManager {
    private
        $ancodes_map = array(),
        $poses_map = array(),
        $grammems_map = array(),
        $ancodes = array(),
        $helper
        ;

    function __construct(phpMorphy_Dict_Source_SourceInterface $source) {
        $this->helper = phpMorphy_Dict_GramTab_ConstStorage_Factory::create($source->getLanguage());

        foreach($source->getAncodes() as $ancode) {
            $this->ancodes[] = $this->createAncode($ancode);
        }
    }

    protected function registerAncodeId($ancodeId) {
        if(!isset($this->ancodes_map[$ancodeId])) {
            $new_id = count($this->ancodes_map);

            $this->ancodes_map[$ancodeId] = $new_id;
        }

        return $this->ancodes_map[$ancodeId];
    }

    protected function registerPos($pos, $isPredict) {
        $pos = mb_convert_case($pos, MB_CASE_UPPER, 'utf-8');

        if(!isset($this->poses_map[$pos])) {
            $pos_id = $this->helper->getPartOfSpeechIdByName($pos);

            $this->poses_map[$pos] = $this->createPos($pos_id, $pos, $isPredict);
        }

        return $this->poses_map[$pos]->getId();
    }

    protected function createPos($id, $name, $isPredict) {
        return new phpMorphy_Dict_PartOfSpeech($id, $name, $isPredict);
    }

    protected function createGrammem($id, $name, $shift) {
        return new phpMorphy_Dict_Grammem($id, $name, $shift);
    }

    protected function registerGrammems(array $it) {
        $result = array();

        foreach($it as $grammem) {
            $grammem = mb_convert_case($grammem, MB_CASE_UPPER, 'utf-8');

            if(!isset($this->grammems_map[$grammem])) {
                $grammem_id = $this->helper->getGrammemIdByName($grammem);
                $shift = $this->helper->getGrammemShiftByName($grammem);

                $this->grammems_map[$grammem] = $this->createGrammem($grammem_id, $grammem, $shift);
            }

            $result[] = $this->grammems_map[$grammem]->getId();
        }

        return $result;
    }

    function getAncodesMap() {
        return $this->ancodes_map;
    }

    function getPosesMap() {
        return $this->poses_map;
    }

    function getGrammemsMap() {
        return $this->grammems_map;
    }

    function resolveAncode($ancodeId) {
        if(!isset($this->ancodes_map[$ancodeId])) {
            throw new Exception("Unknown ancode_id '$ancodeId' given");
        }

        return $this->ancodes_map[$ancodeId];
    }

    function getAncodes() {
        return $this->ancodes;
    }

    function getAncode($ancodeId, $resolve = true) {
        $ancode_id = $resolve ? $this->resolveAncode($ancodeId) : (int)$ancodeId;

        return $this->ancodes[$ancode_id];
    }


    protected function createAncode(phpMorphy_Dict_Ancode $ancode) {
        return new phpMorphy_Dict_Source_NormalizedAncodes_Ancode(
            $this->registerAncodeId($ancode->getId()),
            $ancode->getId(),
            $this->registerPos($ancode->getPartOfSpeech(), $ancode->isPredict()),
            $this->registerGrammems($ancode->getGrammems()),
            $ancode->getId()
        );
    }
};