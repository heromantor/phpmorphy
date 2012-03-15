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

class phpMorphy_Aot_GramTab_Reader extends IteratorIterator {
    const TOKENS_SEPARATOR = ' ';
    const INTERNAL_ENCODING = 'utf-8';

    private
        $factory,
        $encoding;

    function __construct($fileName, $encoding, phpMorphy_Aot_GramTab_GramInfoFactory $factory) {
        parent::__construct($this->createIterators($fileName, $encoding));

        $this->factory = $factory;
        $this->encoding = $encoding;
    }

    protected function createIterators($fileName, $encoding) {
        return new phpMorphy_Util_Iterator_Filter(
            new SplFileObject($fileName),
            function($item) {
                // skip comments
                $item = trim($item);
                return strlen($item) && substr($item, 0, 2) != '//';
            }
        );
    }

    function current() {
        $line = trim(parent::current());
        // split by ' '(space) and \t
        $line = preg_replace('~[\x20\x09]+~', ' ', $line);

        $result = explode(self::TOKENS_SEPARATOR, $line);
        $items_count = count($result);

        if($items_count < 3) {
            throw new phpMorphy_Aot_GramTab_Exception("Can`t split [$line] line, too few tokens");
        }
        
        return $this->processTokens($result);
    }

    protected function processTokens($tokens) {
        return $this->factory->create(
            isset($tokens[2]) ? iconv($this->encoding, self::INTERNAL_ENCODING, $tokens[2]) : '',
            isset($tokens[3]) ? iconv($this->encoding, self::INTERNAL_ENCODING, $tokens[3]) : '',
            iconv($this->encoding, self::INTERNAL_ENCODING, $tokens[0])
        );
    }
}