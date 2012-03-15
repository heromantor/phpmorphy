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

class phpMorphy_UserDict_EncodingConverter {
    private
        /** @var bool */
        $is_affect,
        /* @var string */
        $internal_encoding,
        /* @var string */
        $morphy_encoding,
        /* @var string */
        $morphy_case,
        /* @var string */
        $internal_case;

    /**
     * @param string $morphyEncoding
     * @param MB_CASE_UPPER|MB_CASE_LOWER $morphyCase
     * @param phpMorphy_MorphyInterface $morphy
     * @param string $internalEncoding
     * @param MB_CASE_UPPER|MB_CASE_LOWER $internalCase
     */
    function __construct($morphyEncoding, $morphyCase, $internalEncoding, $internalCase = MB_CASE_UPPER) {
        if(!$this->checkEncoding($morphyEncoding)) {
            throw new phpMorphy_Exception("Invalid morphy encoding '$morphyEncoding'");
        }

        if(!$this->checkEncoding($internalEncoding)) {
            throw new phpMorphy_Exception("Invalid internal encoding '$internalEncoding'");
        }

        $this->internal_encoding = (string)$internalEncoding;
        $this->morphy_encoding = (string)$morphyEncoding;
        $this->morphy_case = $morphyCase == MB_CASE_UPPER ? MB_CASE_UPPER : MB_CASE_LOWER;
        $this->internal_case = $internalCase == MB_CASE_UPPER ? MB_CASE_UPPER : MB_CASE_LOWER;

        $this->is_affect =
            !($this->internal_encoding === $this->morphy_encoding &&
            $this->internal_case === $this->morphy_case);
    }

    function isAffect() {
        return $this->is_affect;
    }
    
    /**
     * @param string $encoding
     * @return void
     */
    private function checkEncoding($encoding) {
        static $encodings;
        $encodings = isset($encodings) ? $encodings : array_map('strtolower', mb_list_encodings());

        return in_array($encoding, $encodings, true);
    }

    /**
     * Convert encoding from internal to morphy
     * @param string $string
     * @return string
     */
    function toMorphy($string) {
        return $this->convert(
            $string,
            $this->internal_encoding,
            $this->morphy_encoding,
            $this->morphy_case
        );
    }

    /**
     * Convert encoding from morphy to internal
     * @param string $string
     * @param bool $withCase
     * @return string
     */
    function toInternal($string, $withCase = false) {
        return $this->convert(
            $string,
            $this->morphy_encoding,
            $this->internal_encoding,
            $this->internal_case === $this->morphy_case ? null : $this->internal_case
        );
    }

    /**
     * @param string $string
     * @param string $from
     * @param string $to
     * @param int|null $case
     * @return string
     */
    protected function convert($string, $from, $to, $case = null) {
        if(null === $string) {
            return null;
        }

        if($from !== $to) {
            $string = mb_convert_encoding($string, $to, $from);
        }

        if(null !== $case) {
            $string = mb_convert_case($string, $case, $to);
        }

        return $string;
    }
}