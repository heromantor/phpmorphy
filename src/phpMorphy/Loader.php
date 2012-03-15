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

class phpMorphy_Loader {
    /** @var string */
    protected $root_path;

    /**
     * @param string $rootPath
     */
    function __construct($rootPath) {
        $this->root_path = (string)$rootPath;
    }
    
    /**
     * @param string $class
     * @return string
     */
    static function classNameToFilePath($class) {
        return str_replace('_', DIRECTORY_SEPARATOR, $class) . '.php';
    }

    /**
     * @param string $filePath
     * @return string
     */
    static function filePathToClassName($filePath) {
        return str_replace(DIRECTORY_SEPARATOR, '_', basename($filePath, '.php'));
    }

    /**
     * @param string $class
     * @return bool
     */
    function loadClass($class) {
        if(class_exists($class, false) || interface_exists($class, false)) {
            return false;
        }

        if(substr($class, 0, 9) !== 'phpMorphy') {
            return false;
        }

        $file_path = $this->getRootPath() . DIRECTORY_SEPARATOR .
                     $this->classNameToFilePath($class);

        if(!file_exists($file_path)) {
            return false;
        }

        require($file_path);

        return true;
    }

    /**
     * @return string
     */
    function getRootPath() {
        return $this->root_path;
    }
}