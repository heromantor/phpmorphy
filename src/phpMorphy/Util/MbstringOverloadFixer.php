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

class phpMorphy_Util_MbstringOverloadFixer {
    /**
     * i need byte oriented string functions
     * with namespaces support we only need overload string functions in current namespace
     * but currently use this ugly hack.
     * @param string $prefix
     * @return void
     */
    private static function exportToGlobal($prefix) {
        $GLOBALS['__phpmorphy_strlen'] = "{$prefix}strlen";
        $GLOBALS['__phpmorphy_strpos'] = "{$prefix}strpos";
        $GLOBALS['__phpmorphy_strrpos'] = "{$prefix}strrpos";
        $GLOBALS['__phpmorphy_substr'] = "{$prefix}substr";
        $GLOBALS['__phpmorphy_strtolower'] = "{$prefix}strtolower";
        $GLOBALS['__phpmorphy_strtoupper'] = "{$prefix}strtoupper";
        $GLOBALS['__phpmorphy_substr_count'] = "{$prefix}substr_count";
    }

    /**
     * @return void
     */
    static function fix() {
        if(
            extension_loaded('mbstring') &&
            2 == (ini_get('mbstring.func_overload') & 2)
        ) {
            self::exportToGlobal('mb_orig_');
        } else {
            self::exportToGlobal('');
        }
    }
}