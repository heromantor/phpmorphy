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

class phpMorphy_GramInfo_HeaderCache extends phpMorphy_GramInfo_Decorator {
    protected
        /** @var array */
        $cache,
        /** @var string */
        $ends;

    /**
     * @param phpMorphy_GramInfo_GramInfoInterface $object
     * @param string $cacheFilePath
     *
     */
    function __construct(phpMorphy_GramInfo_GramInfoInterface $object, $cacheFilePath) {
        parent::__construct($object);

        $this->cache = $this->readCache($cacheFilePath);
        $this->ends = str_repeat("\0", $this->getCharSize() + 1);
    }

    /**
     * @throws phpMorphy_Exception
     * @param string $fileName
     * @return array
     */
    private function readCache($fileName) {
        if(!is_array($result = include($fileName))) {
            throw new phpMorphy_Exception("Can`t get header cache from '$fileName' file'");
        }

        return $result;
    }

    /**
     * @return string
     */
    function getLocale()  {
        return $this->cache['lang'];
    }

    /**
     * @return string
     */
    function getEncoding()  {
        return $this->cache['encoding'];
    }

    /**
     * @return int
     */
    function getCharSize()  {
        return $this->cache['char_size'];
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
        return $this->cache;
    }
}