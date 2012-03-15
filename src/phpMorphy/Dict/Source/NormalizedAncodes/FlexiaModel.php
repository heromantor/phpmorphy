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


class phpMorphy_Dict_Source_NormalizedAncodes_FlexiaModel extends phpMorphy_Dict_FlexiaModelDecorator {
    protected $manager;

    function __construct(phpMorphy_Dict_Source_NormalizedAncodes_AncodesManager $manager, phpMorphy_Dict_FlexiaModel $inner) {
        parent::__construct($inner);
        $this->manager = $manager;
    }

    function getIterator() {
        $that = $this;

        return new phpMorphy_Util_Iterator_Transform(
            parent::getIterator(),
            function(phpMorphy_Dict_Flexia $flexia) use ($that) {
                return $that->__decorate($flexia);
            }
        );
    }

    function offsetGet($offset) {
        return $this->__decorate(parent::offsetGet($offset));
    }

    function __decorate(phpMorphy_Dict_Flexia $flexia) {
        return new phpMorphy_Dict_Source_NormalizedAncodes_Flexia($this->manager, $flexia);
    }
}