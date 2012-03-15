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

class phpMorphy_Aot_GramTab_File extends phpMorphy_Util_Collection_Immutable {
    protected $collection;

    function __construct($fileName, $encoding, phpMorphy_Aot_GramTab_GramInfoFactory $factory) {
        $this->collection = $this->createStorageCollection();

        parent::__construct($this->collection);

        $this->parse($this->createReader($fileName, $encoding, $factory));
    }

    protected function createStorageCollection() {
        return new phpMorphy_Util_Collection_ArrayBased();
    }

    protected function createReader($fileName, $encoding, phpMorphy_Aot_GramTab_GramInfoFactory $factory) {
        return new phpMorphy_Aot_GramTab_Reader($fileName, $encoding, $factory);
    }

    protected function parse(Iterator $it) {
        foreach($it as $value) {
            if(!$value instanceof phpMorphy_Aot_GramTab_GramInfo) {
                throw new phpMorphy_Aot_GramTab_Exception("Invalid value returned from reader");
            }

            $this->collection[$value->getAncodeId()] = $value;
        }
    }
}