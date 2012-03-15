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

interface phpMorphy_Storage_StorageInterface {
    /**
     * Returns name of file
     * @return string
     */
    function getFileName();

    /**
     * Returns size of file in bytes
     * @abstract
     * @return int
     */
    function getFileSize();

    /**
     * Returns resource of this storage
     * @return mixed
     */
    function getResource();

    /**
     * Returns type of this storage
     * @return string
     */
    function getTypeAsString();

    /**
     * Reads $len bytes from $offset offset
     *
     * @throws phpMorphy_Exception
     * @param int $offset Read from this offset
     * @param int $length How many bytes to read
     * @param bool $exactLength If this set to true, then exception thrown when we read less than $len bytes
     * @return string
     */
    function read($offset, $length, $exactLength = true);
}