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

class phpMorphy_Storage_File extends phpMorphy_Storage_StorageAbstract  {
    function getType() { return phpMorphy_Storage_Factory::STORAGE_FILE; }

    function getFileSize() {
        if(false === ($stat = fstat($this->resource))) {
            throw new phpMorphy_Exception('Can`t invoke fstat for ' . $this->file_name . ' file');
        }

        return $stat['size'];
    }

    function readUnsafe($offset, $len) {
        if(0 !== fseek($this->resource, $offset)) {
            throw new phpMorphy_Exception("Can`t seek to $offset offset");
        }

        return fread($this->resource, $len);
    }

    function open($fileName) {
        if(false === ($fh = fopen($fileName, 'rb'))) {
            throw new phpMorphy_Exception("Can`t open $this->file_name file");
        }

        return $fh;
    }
}