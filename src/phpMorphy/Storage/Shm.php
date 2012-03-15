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

class phpMorphy_Storage_Shm extends phpMorphy_Storage_StorageAbstract {
    protected
        $descriptor;

    function __construct($fileName, $shmCache) {
        $this->cache = $shmCache;

        parent::__construct($fileName);
    }

    function getFileSize() {
        return $this->descriptor->getFileSize();
    }

    function getType() { return phpMorphy_Storage_Factory::STORAGE_SHM; }

    function readUnsafe($offset, $len) {
        return shmop_read($this->resource['shm_id'], $this->resource['offset'] + $offset, $len);
    }

    function open($fileName) {
        $this->descriptor = $this->cache->get($fileName);

        return array(
            'shm_id' => $this->descriptor->getShmId(),
            'offset' => $this->descriptor->getOffset()
        );
    }
}