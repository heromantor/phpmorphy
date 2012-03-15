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

class phpMorphy_AncodesResolver_ToDialingAncodes implements phpMorphy_AncodesResolver_AncodesResolverInterface {
    protected
        $ancodes_map,
        $reverse_map;

    function __construct(phpMorphy_Storage_StorageInterface $ancodesMap) {
        if(false === ($this->ancodes_map = unserialize($ancodesMap->read(0, $ancodesMap->getFileSize())))) {
            throw new phpMorphy_Exception("Can`t open phpMorphy => Dialing ancodes map");
        }

        $this->reverse_map = array_flip($this->ancodes_map);
    }

    function unresolve($ancode) {
        if(!isset($ancode)) {
            return null;
        }

        if(!isset($this->reverse_map[$ancode])) {
            throw new phpMorphy_Exception("Unknwon ancode found '$ancode'");
        }

        return $this->reverse_map[$ancode];
    }

    function resolve($ancodeId) {
        if(!isset($ancodeId)) {
            return null;
        }

        if(!isset($this->ancodes_map[$ancodeId])) {
            throw new phpMorphy_Exception("Unknwon ancode id found '$ancodeId'");
        }

        return $this->ancodes_map[$ancodeId];
    }
}