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

abstract class phpMorphy_Storage_StorageAbstract implements phpMorphy_Storage_StorageInterface {
    protected
        /** @var string */
        $file_name,
        /** @var mixed */
        $resource;

    /**
     * @param string $fileName
     */
    function __construct($fileName) {
        $this->file_name = (string)$fileName;
        $this->resource = $this->open($fileName);
    }

    /**
     * @return string
     */
    function getFileName() {
        return $this->file_name;
    }

    /**
     * @return mixed
     */
    function getResource() {
        return $this->resource;
    }

    /**
     * @return string
     */
    function getTypeAsString() {
        return (string)$this->getType();
    }

    /**
     * @throws phpMorphy_Exception
     * @param int $offset
     * @param int $len
     * @param bool $exactLength
     * @return string
     */
    function read($offset, $len, $exactLength = true) {
        if ($offset >= $this->getFileSize()) {
            throw new phpMorphy_Exception(
                "Can`t read $len bytes beyond end of '" . $this->getFileName()
                . "' file, offset = $offset, file_size = " . $this->getFileSize());
        }

        try {
            $result = $this->readUnsafe($offset, $len);
        } catch (Exception $e) {
            throw new phpMorphy_Exception(
                "Can`t read $len bytes at $offset offset, from '" . $this->getFileName()
                . "' file: " . $e->getMessage());
        }

        if ($exactLength && $GLOBALS['__phpmorphy_strlen']($result) < $len) {
            throw new phpMorphy_Exception(
                "Can`t read $len bytes at $offset offset, from '" . $this->getFileName()
                . "' file");
        }

        return $result;
    }

    /**
     * @abstract
     * @param int $offset
     * @param int $len
     * @return string
     */
    abstract function readUnsafe($offset, $len);

    /**
     * @abstract
     * @return string|int
     */
    abstract function getType();

    /**
     * Open $fileName and returns that resource
     * @abstract
     * @param string $fileName
     * @return mixed
     */
    abstract protected function open($fileName);
}