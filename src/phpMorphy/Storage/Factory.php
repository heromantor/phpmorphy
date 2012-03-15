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

class phpMorphy_Storage_Factory {
    const STORAGE_FILE = 'file';
    const STORAGE_MEM = 'mem';
    const STORAGE_SHM = 'shm';

    protected
        /** @var phpMorphy_Shm_CacheInterface */
        $shm_cache,
        /** @var array */
        $shm_options;

    /**
     * @param array $shmOptions
     */
    function __construct($shmOptions = array()) {
        $this->shm_options = $shmOptions;
    }

    /**
     * @return phpMorphy_Shm_CacheInterface
     */
    function getShmCache() {
        if(!isset($this->shm_cache)) {
            $this->shm_cache = $this->createShmCache($this->shm_options);
        }

        return $this->shm_cache;
    }

    /**
     * @throws phpMorphy_Exception
     * @param string|int $type One of STORAGE_FILE, STORAGE_MEM, STORAGE_SHM constants from phpMorphy_Storage_Factory
     * @param string $fileName
     * @param bool $isLazy
     * @return phpMorphy_Storage_File|phpMorphy_Storage_Mem|phpMorphy_Storage_Proxy|phpMorphy_Storage_Shm
     */
    function create($type, $fileName, $isLazy) {
        if(!$this->isTypeSupported($type)) {
            throw new phpMorphy_Exception("Invalid storage type $type specified");
        }

        if($isLazy) {
            return new phpMorphy_Storage_Proxy($type, $fileName, $this);
        }

        switch($type) {
            case self::STORAGE_FILE:
                return new phpMorphy_Storage_File($fileName);
            case self::STORAGE_MEM:
                return new phpMorphy_Storage_Mem($fileName);
            case self::STORAGE_SHM:
                return new phpMorphy_Storage_Shm($fileName, $this->getShmCache());
            default:
                throw new phpMorphy_Exception("Invalid storage type $type specified");
        }
    }

    /**
     * @param array $options
     * @return phpMorphy_Shm_CacheInterface
     */
    protected function createShmCache($options) {
        return new phpMorphy_Shm_Cache($options, !empty($options['clear_on_create']));
    }

    /**
     * @param string|int $type
     * @return bool
     */
    protected function isTypeSupported($type) {
        return in_array(
            $type,
            array(
                self::STORAGE_FILE,
                self::STORAGE_MEM,
                self::STORAGE_SHM,
            )
        );
    }
}