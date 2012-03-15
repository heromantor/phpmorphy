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

class phpMorphy_UnicodeHelper_Utf8 extends phpMorphy_UnicodeHelper_UnicodeHelperAbstract {
    protected static $TAILS_LENGTH_MAP = array(
        0,0,0,0,0,0,0,0, 0,0,0,0,0,0,0,0,
        0,0,0,0,0,0,0,0, 0,0,0,0,0,0,0,0,
        0,0,0,0,0,0,0,0, 0,0,0,0,0,0,0,0,
        0,0,0,0,0,0,0,0, 0,0,0,0,0,0,0,0,
        0,0,0,0,0,0,0,0, 0,0,0,0,0,0,0,0,
        0,0,0,0,0,0,0,0, 0,0,0,0,0,0,0,0,
        0,0,0,0,0,0,0,0, 0,0,0,0,0,0,0,0,
        0,0,0,0,0,0,0,0, 0,0,0,0,0,0,0,0,
        0,0,0,0,0,0,0,0, 0,0,0,0,0,0,0,0,
        0,0,0,0,0,0,0,0, 0,0,0,0,0,0,0,0,
        0,0,0,0,0,0,0,0, 0,0,0,0,0,0,0,0,
        0,0,0,0,0,0,0,0, 0,0,0,0,0,0,0,0,
        1,1,1,1,1,1,1,1, 1,1,1,1,1,1,1,1,
        1,1,1,1,1,1,1,1, 1,1,1,1,1,1,1,1,
        2,2,2,2,2,2,2,2, 2,2,2,2,2,2,2,2,
        3,3,3,3,3,3,3,3, 4,4,4,4,5,5,0,0
    );

    function getFirstCharSize($str) {
        return 1 + self::$TAILS_LENGTH_MAP[ord($str[0])];
    }

    function strrev($str) {
        preg_match_all('/./us', $str, $matches);
        return implode('', array_reverse($matches[0]));
        /*
        $result = array();

        for($i = 0, $c = $GLOBALS['__phpmorphy_strlen']($str); $i < $c;) {
            $len = 1 + $this->tails_length[ord($str[$i])];

            $result[] = $GLOBALS['__phpmorphy_substr']($str, $i, $len);

            $i += $len;
        }

        return implode('', array_reverse($result));
        */
    }

    function clearIncompleteCharsAtEnd($str) {
        $strlen = $GLOBALS['__phpmorphy_strlen']($str);

        if(!$strlen) {
            return '';
        }

        $ord = ord($str[$strlen - 1]);

        if(($ord & 0x80) == 0) {
            return $str;
        }

        for($i = $strlen - 1; $i >= 0; $i--) {
            $ord = ord($str[$i]);

            if(($ord & 0xC0) == 0xC0) {
                $diff = $strlen - $i;
                $seq_len = self::$TAILS_LENGTH_MAP[$ord] + 1;

                $miss = $seq_len - $diff;

                if($miss) {
                    return $GLOBALS['__phpmorphy_substr']($str, 0, -($seq_len - $miss));
                } else {
                    return $str;
                }
            }
        }

        return '';
    }

    protected function strlenImpl($str) {
        preg_match_all('/./us', $str, $matches);
        return count($matches[0]);
    }
}