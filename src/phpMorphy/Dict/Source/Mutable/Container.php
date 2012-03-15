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

class phpMorphy_Dict_Source_Mutable_Container implements IteratorAggregate, Countable {
    private
        $use_identity,
        $container = array(),
        $refcount = array(),
        $items_map;

    function __construct($useIdentity) {
        $this->use_identity = (bool)$useIdentity;
        $this->clear();
    }

    function append($model, $reuseIfExists) {
        if(null !== $this->items_map) {
            $identity_string = serialize($model);

            if($reuseIfExists) {
                if(isset($this->items_map[$identity_string])) {
                    $model = $this->items_map[$identity_string];
                    $this->refcount[$model->getId()]++;
                    return $model;
                }
            }
        }

        $new_model = clone $model;
        $id = count($this->container) + 1;
        $new_model->setId($id);

        $this->container[$id] = $new_model;
        $this->refcount[$id] = 1;

        if(null !== $this->items_map) {
            $this->items_map[$identity_string] = $new_model;
        }

        return $new_model;
    }

    function getById($id) {
        if(!$this->hasId($id)) {
            throw new phpMorphy_Exception("Can`t find model with '$id' id");
        }

        return $this->container[$id];
    }

    function deleteById($id, $holdUnused = true) {
        if(!$this->hasId($id)) {
            throw new phpMorphy_Exception("Can`t find model with '$id' id");
        }

        if($this->refcount[$id] > 0) {
            $this->refcount[$id]--;
        } else {
            if(!$holdUnused) {
                unset($this->container[$id]);
                unset($this->refcount[$id]);
            } else {
                throw new phpMorphy_Exception("Can`t delete model with '$id' id, while it in use");
            }
        }
    }

    function deleteUnused() {
        foreach($this->refcount as $id => $refcount) {
            if($refcount < 1) {
                $this->deleteById($id, false);
            }
        }
    }

    function clear() {
        $this->container = array();
        $this->refcount = array();
        $this->items_map = $this->use_identity ? array() : null;
    }

    function getIterator() {
        return new ArrayIterator($this->container);
    }

    function hasId($id) {
        return isset($this->container[$id]);
    }

    function count() {
        return count($this->container);
    }
}