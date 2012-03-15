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

class phpMorphy_Dict_GramTab_ConstStorage_Factory {
    const SPECIALS_LANG = 'specials';

    protected static function getLangMap() {
        if(false === ($map = include(__DIR__ . '/data/lang_map.php'))) {
            throw new Exception("Can`t open langs map file");
        }

        return $map;
    }

    /**
     * @return phpMorphy_Dict_GramTab_ConstStorage_Specials
     */
    static function getSpecials() {
        static $cache;

        if(null === $cache) {
            $cache = self::create(self::SPECIALS_LANG);
        }

        return $cache;
    }

    /**
     * @param string $lang
     * @return phpMorphy_GramTab_Const_Helper
     */
    static function create($lang) {
        $map = self::getLangMap();

        $lang = strtolower($lang);
        $file = isset($map[$lang]) ? $map[$lang] : $map[false];
        $filePath = __DIR__ . '/data/' . $file;
        $loader = new phpMorphy_Dict_GramTab_ConstStorage_Loader($filePath);
        $is_specials = $lang === self::SPECIALS_LANG;
        $clazz = $is_specials ?
                'phpMorphy_Dict_GramTab_ConstStorage_Specials' :
                'phpMorphy_Dict_GramTab_ConstStorage';

        $helper = new $clazz($loader);

        if(!$is_specials) {
            try {
                //throw new Exception("1");
                $helper->merge(self::getSpecials());
            } catch (Exception $e) {
                throw new Exception(
                    "Can`t inject special values to '" .
                        $helper->getLanguage() . "' [" . $lang . "] lang: " . $e->getMessage()
                );
            }
        }

        return $helper;
    }

    static function getAllHelpers() {
        $result = array();
        $lang_map = self::getLangMap();
        $created_files = array();

        foreach($lang_map as $lang => $file) {
            if(!isset($created_files[$file])) {
                $result[] = self::create($lang);
                $created_files[$file] = 1;
            }
        }

        return $result;
    }
}