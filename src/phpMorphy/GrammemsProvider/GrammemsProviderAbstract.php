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

abstract class phpMorphy_GrammemsProvider_GrammemsProviderAbstract implements phpMorphy_GrammemsProvider_GrammemsProviderInterface {
    protected
        /** @var string[] */
        $all_grammems,
        /** @var array */
        $grammems = array();

    function __construct() {
        $this->all_grammems = $this->flatizeArray($this->getAllGrammemsGrouped());
    }

    /**
     * @abstract
     * @return array
     */
    abstract function getAllGrammemsGrouped();

    /**
     * @param string $partOfSpeech
     * @param array $names
     * @return phpMorphy_GrammemsProvider_GrammemsProviderAbstract
     */
    function includeGroups($partOfSpeech, $names) {
        $grammems = $this->getAllGrammemsGrouped();
        $names = array_flip((array)$names);

        foreach(array_keys($grammems) as $key) {
            if(!isset($names[$key])) {
                unset($grammems[$key]);
            }
        }

        $this->grammems[$partOfSpeech] = $this->flatizeArray($grammems);

        return $this;
    }

    /**
     * @param string $partOfSpeech
     * @param array $names
     * @return phpMorphy_GrammemsProvider_GrammemsProviderAbstract
     */
    function excludeGroups($partOfSpeech, $names) {
        $grammems = $this->getAllGrammemsGrouped();

        foreach((array)$names as $key) {
            unset($grammems[$key]);
        }

        $this->grammems[$partOfSpeech] = $this->flatizeArray($grammems);

        return $this;
    }

    /**
     * @param string $partOfSpeech
     * @return phpMorphy_GrammemsProvider_GrammemsProviderAbstract
     */
    function resetGroups($partOfSpeech) {
        unset($this->grammems[$partOfSpeech]);
        return $this;
    }

    /**
     * @return phpMorphy_GrammemsProvider_GrammemsProviderAbstract
     */
    function resetGroupsForAll() {
        $this->grammems = array();
        return $this;
    }

    /**
     * @static
     * @param array $array
     * @return array
     */
    static function flatizeArray($array) {
        return call_user_func_array('array_merge', $array);
    }

    /**
     * @param string $partOfSpeech
     * @return array
     */
    function getGrammems($partOfSpeech) {
        if(isset($this->grammems[$partOfSpeech])) {
            return $this->grammems[$partOfSpeech];
        } else {
            return $this->all_grammems;
        }
    }
}