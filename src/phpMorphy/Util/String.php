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

class phpMorphy_Util_String {
    /**
     * Returns longest common substring among all strings
     * @param string[] $stringsArray
     * @param bool $isUtf
     * @param string $separatorChar
     * @return string|false
     */
    static function getLongestCommonSubstring(array $stringsArray, $isUtf8 = true, $separatorChar = "\0") {
        $strings_count = count($stringsArray);

        if($strings_count < 2) {
            return false;
        }

        $reg_modifiers = $isUtf8 ? "u" : '';
        $reg_format = '/(.{%d})' .
                      str_repeat('.*\x00.*\1', $strings_count - 1) .
                      "/$reg_modifiers";

        $lcs = false;
        $merged_strings = implode($separatorChar, $stringsArray);

        for($i = 1, $c = strlen($stringsArray[0]); $i < $c; $i++) {
            if(!preg_match(sprintf($reg_format, $i), $merged_strings, $matches)) {
                break;
            }

            $lcs = $matches[1];
        }

        return $lcs;
    }
}