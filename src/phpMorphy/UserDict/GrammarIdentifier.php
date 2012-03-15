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

class phpMorphy_UserDict_GrammarIdentifier {
    const ANY_PART_OF_SPEECH_TAG = '*';

    protected
        /* @var string */
        $pos,
        /* @var string[] */
        $grammems;

    /**
     * @param string|null $pos
     * @param string[] $grammems
     */
    function __construct($pos, $grammems) {
        $this->pos = isset($pos) ? trim($pos) : null;
        $this->grammems = (array)$grammems;

        if($this->pos === self::ANY_PART_OF_SPEECH_TAG) {
            $this->pos = null;
        }
    }

    /**
     * @param string|null $pos
     * @param string $grammems
     * @return phpMorphy_UserDict_GrammarIdentifier
     */
    static function constructFromPosAndGrammems($pos, $grammems) {
        $grammems = array_filter(array_map('trim', explode(',', $grammems)), 'strlen');

        $clazz = __CLASS__;
        return new $clazz(
            $pos,
            $grammems
        );
    }

    /**
     *
     * @param string $string
     * @return phpMorphy_UserDict_GrammarIdentifier
     */
    static function constructFromString($string) {
        $string = trim($string);

        $sp_pos = strpos($string, ' ');

        $grammems_string = '';
        $pos = null;

        if(false === $sp_pos) {
            $pos = $string;
        } else {
            $pos = substr($string, 0, $sp_pos);
            $grammems_string = trim(substr($string, $sp_pos + 1));
        }

        return self::constructFromPosAndGrammems(
            $pos,
            $grammems_string
        );
    }

    /**
     *
     * @param string $pos
     * @param string[] $grammems
     * @return phpMorphy_UserDict_GrammarIdentifier
     */
    static function construct($pos, $grammems) {
        $clazz = __CLASS__;

        return new $clazz($pos, (array)$grammems);
    }

    /**
     * @return bool
     */
    function hasPartOfSpeech() {
        return null !== $this->getPartOfSpeech();
    }

    /**
     * @return string
     */
    function getPartOfSpeech() {
        return $this->pos;
    }

    /**
     * @return string[]
     */
    function getGrammems() {

        return $this->grammems;
    }

    /**
     * @param string $partOfSpeech
     * @param string[] $grammems
     * @return bol
     */
    function match($partOfSpeech, array $grammems) {
        /*
        $partOfSpeech = EncodingConverter::defaultCase($partOfSpeech);
        $grammems = array_map(array('EncodingConverter', 'defaultCase'), $grammems);
        */

        if($this->hasPartOfSpeech()) {
            if($this->getPartOfSpeech() !== $partOfSpeech) {
                return false;
            }
        }

        if(count($this->getGrammems()) && count(array_diff($this->getGrammems(), $grammems))) {
            return false;
        }

        return true;
    }

    /**
     * @return string
     */
    function  __toString() {
        $string = $this->hasPartOfSpeech() ?
            $this->getPartOfSpeech() :
            '*';

        $grammems = $this->getGrammems();
        if(count($grammems)) {
            $string .= ' ' . implode(',', $grammems);
        }

        return $string;
    }
}