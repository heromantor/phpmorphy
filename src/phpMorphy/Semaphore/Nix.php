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

class phpMorphy_Semaphore_Nix implements phpMorphy_Semaphore_SemaphoreInterface {
    const DEFAULT_PERM = 0644;

    private $sem_id = false;

    function __construct($key) {
        if(false === ($this->sem_id = sem_get($key, 1, self::DEFAULT_PERM, true))) {
            throw new phpMorphy_Exception("Can`t get semaphore for '$key' key");
        }
    }

    function lock() {
        if(false === sem_acquire($this->sem_id)) {
            throw new phpMorphy_Exception("Can`t acquire semaphore");
        }
    }

    function unlock() {
        if(false === sem_release($this->sem_id)) {
            throw new phpMorphy_Exception("Can`t release semaphore");
        }
    }

    function remove() {
        sem_remove($this->sem_id);
    }
}