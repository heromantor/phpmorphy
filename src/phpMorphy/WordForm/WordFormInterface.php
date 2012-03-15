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


interface phpMorphy_WordForm_WordFormInterface {
    /**
     * @return string
     */
    function getWord();

    /**
     * @return string
     */
    function getPartOfSpeech();

    /**
     * @return string[]
     */
    function getGrammems();

    /**
     * @return string
     */
    function getCommonPrefix();

    /**
     * @return string
     */
    function getFormPrefix();

    /**
     * @return string
     */
    function getSuffix();

    /**
     * @return string
     */
    function getBase();

    /**
     * @return string
     */
    function getFormGrammems();

    /**
     * @return string
     */
    function getCommonGrammems();

    /**
     * @return string
     */
    function getPrefix();

    /**
     * @param string[]|int[]|string|int $grammems
     * @return bool
     */
    function hasGrammems($grammems);

    /**
     * @abstract
     * @param string $base
     * @return void
     */
    function setBase($base);

    /**
     * @abstract
     * @param string $common_prefix
     * @return void
     */
    function setCommonPrefix($common_prefix);

    /**
     * @abstract
     * @param string $prefix
     * @return void
     */
    function setFormPrefix($prefix);

    /**
     * @abstract
     * @param string $suffix
     * @return void
     */
    function setSuffix($suffix);

    /**
     * @abstract
     * @param string $partOfSpeech
     * @return void
     */
    function setPartOfSpeech($partOfSpeech);

    /**
     * @abstract
     * @param string[] $grammems
     * @return void
     */
    function setFormGrammems(array $grammems);

    /**
     * @abstract
     * @param string[] $grammems
     * @return void
     */
    function setCommonGrammems(array $grammems);
}