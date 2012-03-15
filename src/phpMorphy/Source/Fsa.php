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

class phpMorphy_Source_Fsa implements phpMorphy_Source_SourceInterface {
    protected
        /** @var phpMorphy_Fsa_FsaInterface */
        $fsa,
        /** @var int */
        $root;

    /**
     * @param phpMorphy_Fsa_FsaInterface $fsa
     */
    function __construct(phpMorphy_Fsa_FsaInterface $fsa) {
        $this->fsa = $fsa;
        $this->root = $fsa->getRootTrans();
    }

    /**
     * @return phpMorphy_Fsa_FsaInterface
     */
    function getFsa() {
    	return $this->fsa;
    }

    /**
     * @param string $key
     * @return string|false
     */
    function getValue($key) {
        if(false === ($result = $this->fsa->walk($this->root, $key, true)) || !$result['annot']) {
            return false;
        }

        return $result['annot'];
    }
}