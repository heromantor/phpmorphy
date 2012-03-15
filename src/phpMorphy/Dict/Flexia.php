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

class phpMorphy_Dict_Flexia {
    protected
        $prefix,
        $suffix,
        $ancode_id;

    function __construct($prefix, $suffix, $ancodeId) {
        //phpMorphy_Dict_Ancode::checkAncodeId($ancodeId, "Invalid ancode specified for flexia");

        $this->setPrefix($prefix);
        $this->setSuffix($suffix);
        $this->setAncodeId($ancodeId);
    }

    function setPrefix($prefix) { $this->prefix = (string)$prefix; }
    function setSuffix($suffix) { $this->suffix = (string)$suffix; }
    function setAncodeId($id) { $this->ancode_id = $id; }

    function getPrefix() { return $this->prefix; }
    function getSuffix() { return $this->suffix; }
    function getAncodeId() { return $this->ancode_id; }

    function __toString() {
        return phpMorphy_Dict_ModelsFormatter::create()->formatFlexia($this);
    }
}