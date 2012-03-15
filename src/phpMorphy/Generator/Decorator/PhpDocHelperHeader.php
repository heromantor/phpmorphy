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

class phpMorphy_Generator_Decorator_PhpDocHelperHeader {
    public
        $auto_regenerate = false,
        $generated_at = false,
        $decoratee_class = false,
        $decorator_class = false;

    protected function __construct() {
    }

    /**
     * @static
     * @param  $string
     * @return void
     */
    static function constructFromString($string) {
        $lines = explode("\n", $string);
        $obj = new phpMorphy_Generator_Decorator_PhpDocHelperHeader;
        $any_found = false;

        array_walk(
            $lines,
            function ($line) use ($obj, $any_found) {
                $line = ltrim(trim($line, " \t*"), '@');
                if(false !== ($pos = strpos($line, ' '))) {
                    $key = strtolower(substr($line, 0, $pos));

                    if(preg_match('/^decorator-/', $key)) {
                        $key = str_replace('-', '_', substr($key, 10));
                        $value = ltrim(substr($line, $pos));

                        $obj->$key = $value;
                        $any_found = true;
                    }
                }
            }
        );

        $obj->auto_regenerate = strtolower($obj->auto_regenerate) === 'true';
        $obj->generated_at = false !== $obj->generated_at ?
            strtotime($obj->generated_at) :
            false;

        return $obj;
    }
}