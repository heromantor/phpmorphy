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

class phpMorphy_Generator_Fsa_HelperSparse extends phpMorphy_Generator_Fsa_HelperAbstract {
    /**
     * @return string
     */
    function getType() {
        return 'Sparse';
    }

    /**
     * @return string
     */
    function checkEmpty($var) {
        return "($var & 0x0200)";
    }

    /**
     * @return string
     */
    function getRootTransOffset() {
        return $this->getOffsetInFsa($this->getTransSize());
    }

    /**
     * @return string
     */
    function getDest($var) {
        return "(($var) >> 10) & 0x3FFFFF";
    }

    /**
     * @return string
     */
    function getAnnotIdx($var) {
        return "(($var & 0xFF) << 22) | (($var >> 10) & 0x3FFFFF)";
    }

    /**
     * @return string
     */
    function getIndexByTrans($transVar, $charVar) {
        return "(($transVar >> 10) & 0x3FFFFF) + $charVar + 1";
    }

    /**
     * @return string
     */
    function getAnnotIndexByTrans($transVar) {
        return "($transVar >> 10) & 0x3FFFFF";
    }
}