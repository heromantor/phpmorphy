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

abstract class phpMorphy_GramInfo_GramInfoAbstract implements phpMorphy_GramInfo_GramInfoInterface {
    const HEADER_SIZE = 128;

    protected
        /** @var mixed */
        $resource,
        /** @var array */
        $header,
        /** @var string */
        $ends,
        /** @var int */
        $ends_size;

    /**
     * @param mixed $resource
     * @param array $header
     */
    protected function __construct($resource, $header) {
        $this->resource = $resource;
        $this->header = $header;

        $this->ends = str_repeat("\0", $header['char_size'] + 1);
        $this->ends_size = $GLOBALS['__phpmorphy_strlen']($this->ends);
    }

    /**
     * @static
     * @throws phpMorphy_Exception
     * @param phpMorphy_Storage_StorageInterface $storage
     * @param bool $isLazy
     * @return phpMorphy_GramInfo_GramInfoInterface
     */
    static function create(phpMorphy_Storage_StorageInterface $storage, $isLazy) {
        if($isLazy) {
            return new phpMorphy_GramInfo_Proxy($storage);
        }

        $header = phpMorphy_GramInfo_GramInfoAbstract::readHeader(
            $storage->read(0, self::HEADER_SIZE)
        );

        if(!phpMorphy_GramInfo_GramInfoAbstract::validateHeader($header)) {
            throw new phpMorphy_Exception('Invalid graminfo format');
        }

        $storage_type = $storage->getTypeAsString();
        $clazz = 'phpMorphy_GramInfo_' . ucfirst($storage_type);

        return new $clazz($storage->getResource(), $header);
    }

    /**
     * @return string
     */
    function getLocale() {
        return $this->header['lang'];
    }

    /**
     * @return string
     */
    function getEncoding() {
        return $this->header['encoding'];
    }

    /**
     * @return bool
     */
    function isInUpperCase() {
        return null;
    }

    /**
     * @return int
     */
    function getCharSize() {
        return $this->header['char_size'];
    }

    /**
     * @return string
     */
    function getEnds() {
        return $this->ends;
    }

    /**
     * @return array
     */
    function getHeader() {
        return $this->header;
    }

    /**
     * @static
     * @param string $headerRaw
     * @return array
     */
    static protected function readHeader($headerRaw) {
        $header = unpack(
            'Vver/Vis_be/Vflex_count_old/' .
            'Vflex_offset/Vflex_size/Vflex_count/Vflex_index_offset/Vflex_index_size/' .
            'Vposes_offset/Vposes_size/Vposes_count/Vposes_index_offset/Vposes_index_size/' .
            'Vgrammems_offset/Vgrammems_size/Vgrammems_count/Vgrammems_index_offset/Vgrammems_index_size/' .
            'Vancodes_offset/Vancodes_size/Vancodes_count/Vancodes_index_offset/Vancodes_index_size/' .
            'Vchar_size/',
            $headerRaw
        );

        $offset = 24 * 4;
        $len = ord($GLOBALS['__phpmorphy_substr']($headerRaw, $offset++, 1));
        $header['lang'] = rtrim($GLOBALS['__phpmorphy_substr']($headerRaw, $offset, $len));

        $offset += $len;

        $len = ord($GLOBALS['__phpmorphy_substr']($headerRaw, $offset++, 1));
        $header['encoding'] = rtrim($GLOBALS['__phpmorphy_substr']($headerRaw, $offset, $len));

        return $header;
    }

    /**
     * @static
     * @param array $header
     * @return bool
     */
    static protected function validateHeader($header) {
        if(
            3 != $header['ver'] ||
            1 == $header['is_be']
        ) {
            return false;
        }

        return true;
    }

    /**
     * @param string $string
     * @return string
     */
    protected function cleanupCString($string) {
        if(false !== ($pos = $GLOBALS['__phpmorphy_strpos']($string, $this->ends))) {
            $string = $GLOBALS['__phpmorphy_substr']($string, 0, $pos);
        }

        return $string;
    }

    /**
     * @abstract
     * @param int $offset
     * @param int $count
     * @return int[]
     */
    abstract protected function readSectionIndex($offset, $count);

    /**
     * @param int $offset
     * @param int $count
     * @param int $total_size
     * @return int[]
     */
    protected function readSectionIndexAsSize($offset, $count, $total_size) {
        if(!$count) {
            return array();
        }

        $index = $this->readSectionIndex($offset, $count);
        $index[$count] = $index[0] + $total_size;

        for($i = 0; $i < $count; $i++) {
            $index[$i] = $index[$i + 1] - $index[$i];
        }

        unset($index[$count]);

        return $index;
    }
};