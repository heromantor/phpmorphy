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

abstract class phpMorphy_GrammemsProvider_ForFactoryAbstract extends phpMorphy_GrammemsProvider_GrammemsProviderAbstract {
    protected
        $encoded_grammems;

    function __construct($encoding) {
        $this->encoded_grammems = $this->encodeGrammems($this->getGrammemsMap(), $encoding);

        parent::__construct();
    }

    abstract function getGrammemsMap();

    function getAllGrammemsGrouped() {
        return $this->encoded_grammems;
    }

    protected function encodeGrammems($grammems, $encoding) {
        $from_encoding = $this->getSelfEncoding();

        if($from_encoding == $encoding) {
            return $grammems;
        }

        $result = array();

        foreach($grammems as $key => $ary) {
            $new_key = iconv($from_encoding, $encoding, $key);
            $new_value = array();

            foreach($ary as $value) {
                $new_value[] = iconv($from_encoding, $encoding, $value);
            }

            $result[$new_key] = $new_value;
        }

        return $result;
    }
}