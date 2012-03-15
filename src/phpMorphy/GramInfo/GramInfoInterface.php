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

interface phpMorphy_GramInfo_GramInfoInterface {
    /**
     * Returns langugage for graminfo file
     * @return string
     */
    function getLocale();

    /**
     * Return encoding for graminfo file
     * @return string
     */
    function getEncoding();

    /**
    * @return bool
    * TODO: implement this latter in dict
    */
    function isInUpperCase();

    /**
     * Return size of character
     *   (cp1251(or any single byte encoding) - 1
     *   utf8 - 1
     *   utf16 - 2
     *   utf32 - 4
     *   etc..
     * @return int
     */
    function getCharSize();

    /**
     * Return end of string value (usually string with \0 value of char_size + 1 length)
     * @return string
     */
    function getEnds();

    /**
     * Reads graminfo header
     *
     * @param int $offset
     * @return array
     */
    function readGramInfoHeader($offset);

    /**
     * Returns size of header struct
     * @return int
     */
    function getGramInfoHeaderSize();

    /**
     * Read ancodes section for header retrieved with readGramInfoHeader
     *
     * @param array $info
     * @return array
     */
    function readAncodes($info);

    /**
     * Read flexias section for header retrieved with readGramInfoHeader
     *
     * @param array $info
     * @return array
     */
    function readFlexiaData($info);

    /**
     * Read all graminfo headers offsets, which can be used latter for readGramInfoHeader method
     * @return int[]
     */
    function readAllGramInfoOffsets();

    /**
     * @abstract
     * @return array
     */
    function getHeader();

    /**
     * @abstract
     * @return array
     */
    function readAllPartOfSpeech();

    /**
     * @abstract
     * @return array
     */
    function readAllGrammems();

    /**
     * @abstract
     * @return array
     */
    function readAllAncodes();
}