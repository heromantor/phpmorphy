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


class phpMorphy_Util_Collection_ArrayBased implements phpMorphy_Util_Collection_CollectionInterface {
    protected
        /** @var array */
        $data;

    /**
     * @param Traversable|null $values
     */
    function __construct($values = null) {
        $this->clear();

        if(null !== $values) {
            $this->import($values);
        }
    }

    /**
     * @return array
     */
    function getData() {
        return $this->data;
    }

    /**
     * @return Iterator
     */
    function getIterator() {
        return new ArrayIterator($this->data);
    }

    /**
     * @throws phpMorphy_Exception
     * @param Traversable $values
     * @return void
     */
    function import($values) {
        if($values instanceof Traversable || is_array($values)) {
            foreach($values as $v) {
                $this->append($v);
            }
        } else {
            throw new phpMorphy_Exception("Vlues not implements Traversable interface");
        }
    }

    /**
     * @param mixed $value
     * @return void
     */
    function append($value) {
        $this->data[] = $value;
    }

    /**
     * @return void
     */
    function clear() {
        $this->data = array();
    }

    /**
     * @param int $offset
     * @return bool
     */
    function offsetExists($offset) {
        return array_key_exists($offset, $this->data);
    }

    /**
     * @throws phpMorphy_Exception
     * @param int $offset
     * @return mixed
     */
    function offsetGet($offset) {
        if(!$this->offsetExists($offset)) {
            throw new phpMorphy_Exception("Invalid offset($offset) given");
        }

        return $this->data[$offset];
    }

    /**
     * @param int $offset
     * @param mixed $value
     * @return void
     */
    function offsetSet($offset, $value) {
        if(null === $offset) {
            $this->append($value);
        } else {
            $this->data[$offset] = $value;
        }
    }

    /**
     * @param int $offset
     * @return void
     */
    function offsetUnset($offset) {
        array_splice($this->data, $offset, 1);
    }

    /**
     * @return int
     */
    function count() {
        return count($this->data);
    }
}