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

class phpMorphy_AnnotDecoder_Factory {
    protected static $instances = array();

    protected
        $cache_common,
        $cache_predict,
        $eos;

    protected function __construct($eos) {
        $this->eos = $eos;
    }

    /**
     * @param string $eos
     * @return phpMorphy_AnnotDecoder_Factory
     */
    static function instance($eos) {
        if(!isset(self::$instances[$eos])) {
            self::$instances[$eos] = new phpMorphy_AnnotDecoder_Factory($eos);
        }

        return self::$instances[$eos];
    }

    function getCommonDecoder() {
        if(!isset($this->cache_common)) {
            $this->cache_common = new phpMorphy_AnnotDecoder_Common($this->eos);
        }

        return $this->cache_common;
    }

    function getPredictDecoder() {
        if(!isset($this->cache_predict)) {
            $this->cache_predict = new phpMorphy_AnnotDecoder_Predict($this->eos);
        }

        return $this->cache_predict;
    }
}