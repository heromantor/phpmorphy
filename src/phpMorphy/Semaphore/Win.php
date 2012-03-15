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

class phpMorphy_Semaphore_Win implements phpMorphy_Semaphore_SemaphoreInterface {
    const DIR_NAME = 'phpmorphy_semaphore';
    const USLEEP_TIME = 100000; // 0.1s
    const MAX_SLEEP_TIME = 5000000; // 5sec

    protected $dir_path;

    function __construct($key) {
        $this->dir_path = $this->getTempDir() . DIRECTORY_SEPARATOR . self::DIR_NAME . "_$key";

        register_shutdown_function(array($this, 'unlock'));
    }

    protected function getTempDir() {
        if(false === ($result = getenv('TEMP'))) {
            if(false === ($result = getenv('TMP'))) {
                throw new phpMorphy_Exception("Can`t get temporary directory");
            }
        }

        return $result;
    }

    function lock() {
        for($i = 0; $i < self::MAX_SLEEP_TIME; $i += self::USLEEP_TIME) {
            if(!file_exists($this->dir_path)) {
                if(false !== @mkdir($this->dir_path, 0644)) {
                    return true;
                }
            }

            usleep(self::USLEEP_TIME);
        }

        throw new phpMorphy_Exception("Can`t acquire semaphore");
    }

    function unlock() {
        @rmdir($this->dir_path);
    }

    function remove() {
    }
}