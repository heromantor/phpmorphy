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


class phpMorphy_Util_Collection_Typed extends phpMorphy_Util_Collection_Decorator {
    /**
     * @var array
     */
    static private $INTERNAL_TYPES = array(
        'int'   => 'integer', 'integer' => 'integer',
        'bool'  => 'boolean', 'boolean' => 'boolean',
        'float' => 'double', 'double'   => 'double',
        'string'=> 'string',
        'array' => 'array',
        'null' => 'NULL'
    );

    private
        /** @var string */
        $valid_type,
        /** @var boool */
        $is_pod,
        /** @var bool */
        $allow_null
        ;

    /**
     * @param phpMorphy_Util_Collection_CollectionInterface $inner
     * @param string $validType
     *
     */
    function __construct(phpMorphy_Util_Collection_CollectionInterface $inner, $validType, $allowNull = false) {
        parent::__construct($inner);

        $this->allow_null = (bool)$allowNull;

        $lower_type = strtolower((string)$validType);

        if(isset(self::$INTERNAL_TYPES[$lower_type])) {
            $this->is_pod = true;
            $this->valid_type = self::$INTERNAL_TYPES[$lower_type];
        } else {
            $this->is_pod = false;
            $this->valid_type = $validType;
        }
    }

    /**
     * @param mixed $value
     * @return void
     */
    function append($value) {
        $this->assertType($value);
        parent::append($value);
    }

    function offsetSet($offset, $value) {
        $this->assertType($value);
        parent::offsetSet($offset, $value);
    }

    protected function assertType($value) {
        if($this->is_pod) {
            if(gettype($value) === $this->valid_type || (null === $value && $this->allow_null)) {
                return true;
            }
        } else {
            if($value instanceof $this->valid_type || (null === $value && $this->allow_null)) {
                return true;
            }
        }

        throw new phpMorphy_Exception(
            "Invalid type '" . gettype($value) . "', expected '" . $this->valid_type . "'"
        );
    }
}