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

class phpMorphy_UnicodeHelper_Utf16 extends phpMorphy_UnicodeHelper_UnicodeHelperAbstract {
    protected
        $int_format_string;

    protected function __construct($encoding, $isBigEndian) {
        parent::__construct($encoding);

        $this->int_format_string = $isBigEndian ? 'n' : 'v';
    }

    function getFirstCharSize($str) {
        list(, $ord) = unpack($this->int_format_string, $str);

        return $ord >= 0xD800 && $ord <= 0xDFFF ? 4 : 2;
    }

    function strrev($str) {
        $result = array();

        $count = $GLOBALS['__phpmorphy_strlen']($str) / 2;
        $fmt = $this->int_format_string . $count;

        $words = array_reverse(unpack($fmt, $str));

        for($i = 0; $i < $count; $i++) {
            $ord = $words[$i];

            if($ord >= 0xD800 && $ord <= 0xDFFF) {
                // swap surrogates
                $t = $words[$i];
                $words[$i] = $words[$i + 1];

                $i++;
                $words[$i] = $t;
            }
        }

        array_unshift($words, $fmt);

        return call_user_func_array('pack', $words);
    }

    function clearIncompleteCharsAtEnd($str) {
        $strlen = $GLOBALS['__phpmorphy_strlen']($str);

        if($strlen & 1) {
            $strlen--;
            $str = $GLOBALS['__phpmorphy_substr']($str, 0, $strlen);
        }

        if($strlen < 2) {
            return '';
        }

        list(, $ord) = unpack($this->int_format_string, $GLOBALS['__phpmorphy_substr']($str, -2, 2));

        if($this->isSurrogate($ord)) {
            if($strlen < 4) {
                return '';
            }

            list(, $ord) = unpack($this->int_format_string, $GLOBALS['__phpmorphy_substr']($str, -4, 2));

            if($this->isSurrogate($ord)) {
                // full surrogate pair
                return $str;
            } else {
                return $GLOBALS['__phpmorphy_substr']($str, 0, -2);
            }
        }

        return $str;
    }

    protected function strlenImpl($str) {
        $count = $GLOBALS['__phpmorphy_strlen']($str) / 2;
        $fmt = $this->int_format_string . $count;

        foreach(unpack($fmt, $str) as $ord) {
            if($ord >= 0xD800 && $ord <= 0xDFFF) {
                $count--;
            }
        }

        return $count;
    }

    protected function isSurrogate($ord) {
        return $ord >= 0xD800 && $ord <= 0xDFFF;
    }
}