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

abstract class phpMorphy_UnicodeHelper_UnicodeHelperAbstract implements phpMorphy_UnicodeHelper_UnicodeHelperInterface {
    protected static
        /* @var bool */
        $HAS_ICONV_EXTENSION,
        /* @var bool */
        $HAS_MULTIBYTE_EXTENSION,
        /* @var string */
        $STRLEN_FUNCTION_NAME,
        /* @var array */
        $INSTANCES_CACHE = array()
        ;

    /* @var string */
    protected $encoding;

    /**
     * @param string $encoding
     */
    protected function __construct($encoding) {
        $this->encoding = (string)$encoding;

        if(!isset(self::$HAS_ICONV_EXTENSION) || !isset(self::$HAS_MULTIBYTE_EXTENSION)) {
            if(false !== (self::$HAS_ICONV_EXTENSION = extension_loaded('iconv'))) {
                self::$STRLEN_FUNCTION_NAME = 'iconv_strlen';
            } else if(false !== (self::$HAS_MULTIBYTE_EXTENSION = extension_loaded('mbstring'))) {
                self::$STRLEN_FUNCTION_NAME = 'mb_strlen';
            }
        }
    }

    /**
     * @static
     * @param string $encoding
     * @return phpMorphy_UnicodeHelper_UnicodeHelperInterface
     */
    static function getHelperForEncoding($encoding) {
        $encoding = $GLOBALS['__phpmorphy_strtolower']($encoding);

        if(!isset(self::$INSTANCES_CACHE[$encoding])) {
            self::$INSTANCES_CACHE[$encoding] = self::doCreate($encoding);;
        }

        return self::$INSTANCES_CACHE[$encoding];
    }

    /**
     * @static
     * @throws phpMorphy_Exception
     * @param  $encoding
     * @return phpMorphy_UnicodeHelper_UnicodeHelperInterface
     */
    protected static function doCreate($encoding) {
        if(preg_match('~^(utf|ucs)(-)?([0-9]+)(-)?(le|be)?$~', $encoding, $matches)) {
            $utf_type = $matches[1];
            $utf_base = (int)$matches[3];
            $endiannes = '';

            switch($utf_type) {
                case 'utf':
                    if(!in_array($utf_base, array(8, 16, 32))) {
                        throw new phpMorphy_Exception('Invalid utf base');
                    }

                    break;
                case 'ucs':
                    if(!in_array($utf_base, array(2, 4))) {
                        throw new phpMorphy_Exception('Invalid ucs base');
                    }

                    break;
                default: throw new phpMorphy_Exception('Internal error');
            }

            if($utf_base > 8 || 'ucs' === $utf_type) {
                if(isset($matches[5])) {
                    $endiannes = $matches[5] == 'be' ? 'be' : 'le';
                } else {
                    $tmp = pack('L', 1);
                    $endiannes = ord($tmp[0]) == 0 ? 'be' : 'le';
                }
            }


            if($utf_type === 'ucs' || ($utf_type === 'utf' && $utf_base == 32)) {
                return new phpMorphy_UnicodeHelper_MultiByteFixed($encoding, $utf_base / 8);
            } else if($utf_type === 'utf' && $utf_base == 8) {
                return new phpMorphy_UnicodeHelper_Utf8($encoding);
            } else if($utf_type === 'utf' && $utf_base == 16) {
                return new phpMorphy_UnicodeHelper_Utf16($encoding, $endiannes === 'be');
            } else {
                throw new phpMorphy_Exception("Unknown utf like encoding: '$encoding'");
            }
        } else {
            return new phpMorphy_UnicodeHelper_Singlebyte($encoding);
        }
    }

    /**
     * @param string $string
     * @return int
     */
    function strlen($string) {
        if(isset(self::$STRLEN_FUNCTION_NAME)) {
            $foo = self::$STRLEN_FUNCTION_NAME;
            return $foo($string, $this->encoding);
        } else {
            return $this->strlenImpl($string);
        }
    }

    /**
     * @abstract
     * @param string $str
     * @return int
     */
    protected abstract function strlenImpl($str);
}