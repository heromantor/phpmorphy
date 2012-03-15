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

class phpMorphy_Storage_Proxy extends phpMorphy_Storage_Decorator {
    protected
        /** @var string */
        $file_name,
        /** @var string */
        $type,
        /** @var phpMorphy_Storage_Factory */
        $factory;

    /**
     * @param string $type
     * @param string $fileName
     * @param phpMorphy_Storage_Factory $factory
     */
    function __construct($type, $fileName, phpMorphy_Storage_Factory $factory) {
        $this->file_name = (string)$fileName;
        $this->type = $type;
        $this->factory = $factory;

        $this->actAsProxy(
            /*
            function() use ($type, $fileName, $factory) {
                return $factory->create($type, $fileName, false);
            }
             */
        );
    }

    protected function proxyInstantiate() {
        $result = $this->factory->create($this->type, $this->file_name, false);

        unset($this->file_name);
        unset($this->type);
        unset($this->factory);

        return $result;
    } 
}