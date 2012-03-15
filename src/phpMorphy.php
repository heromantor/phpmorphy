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

if(!defined('PHPMORPHY_DIR')) {
    define('PHPMORPHY_DIR', __DIR__);
}

require_once(PHPMORPHY_DIR . '/phpMorphy/Loader.php');

spl_autoload_register(array(new phpMorphy_Loader(PHPMORPHY_DIR), 'loadClass'));

if(extension_loaded('morphy')) {
    throw new phpMorphy_Exception("todo: php extension not implemented");
} else {
    class phpMorphy extends phpMorphy_MorphyNative {
    }
}

define('PHPMORPHY_STORAGE_FILE', phpMorphy::STORAGE_FILE);
define('PHPMORPHY_STORAGE_MEM', phpMorphy::STORAGE_MEM);
define('PHPMORPHY_STORAGE_SHM', phpMorphy::STORAGE_SHM);
