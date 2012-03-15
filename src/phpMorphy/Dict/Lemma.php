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

class phpMorphy_Dict_Lemma {
    protected
        $id,
        $base,
        $flexia_id,
        $accent_id,
        $prefix_id,
        $ancode_id;

    function __construct($base, $flexiaId, $accentId) {
        $this->base = (string)$base;
        $this->flexia_id = (int)$flexiaId;
        $this->accent_id = (int)$accentId;

        if($this->flexia_id < 0) {
            throw new Exception("flexia_id must be positive int");
        }

        if($this->accent_id < 0) {
            throw new Exception("accent_id must be positive int");
        }
    }

    function setId($id) {
        $this->id = $id;
    }

    function getId() {
        return $this->id;
    }

    function hasId() {
        return null !== $this->getId();
    }

    function setPrefixId($prefixId) {
        if(is_null($prefixId)) {
            throw new phpMorphy_Exception("NULL prefix_id specified");
        }

        $this->prefix_id = (int)$prefixId;

        if($this->prefix_id < 0) {
            throw new phpMorphy_Exception("prefix_id must be positive int");
        }
    }

    function setAncodeId($ancodeId) {
        if(is_null($ancodeId)) {
            throw new phpMorphy_Exception("NULL id specified");
        }

        $this->ancode_id = $ancodeId;
    }

    function getBase() { return $this->base; }
    function getFlexiaId() { return $this->flexia_id; }
    function getAccentId() { return $this->accent_id; }
    function getPrefixId() { return $this->prefix_id; }
    function getAncodeId() { return $this->ancode_id; }

    function hasPrefixId() { return isset($this->prefix_id); }
    function hasAncodeId() { return isset($this->ancode_id); }

    function __toString() {
        return phpMorphy_Dict_ModelsFormatter::create()->formatLemma($this);
    }
}