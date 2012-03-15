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

class phpMorphy_UnicodeHelper_MultiByteFixed extends phpMorphy_UnicodeHelper_UnicodeHelperAbstract {
    protected
        $char_size;

    protected function __construct($encoding, $charSize) {
        parent::__construct($encoding);
        $this->char_size = (int)$charSize;
    }

    function getFirstCharSize($str) {
        return $this->char_size;
    }

    function strrev($str) {
        return implode('', array_reverse(str_split($str, $this->char_size)));
    }

    protected function strlenImpl($str) {
        return $GLOBALS['__phpmorphy_strlen']($str) / $this->char_size;
    }

    function clearIncompleteCharsAtEnd($str) {
        $len = $GLOBALS['__phpmorphy_strlen']($str);
        $mod = $len % $this->char_size;

        if($mod > 0) {
            //return $GLOBALS['__phpmorphy_substr']($str, 0, floor($len / $this->size) * $this->size);
            return $GLOBALS['__phpmorphy_substr']($str, 0, $len - $mod);
        }

        return $str;
    }
}