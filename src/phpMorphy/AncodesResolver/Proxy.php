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

class phpMorphy_AncodesResolver_Proxy extends phpMorphy_AncodesResolver_Decorator {
    private
        /** @var string */
        $class,
        /** @var array */
        $args;

    /**
     * @param string $class
     * @param array $ctorArgs
     */
    function __construct($class, array $ctorArgs) {
        $this->class = (string)$class;
        $this->args = $ctorArgs;

        $this->actAsProxy();
    }

    protected function proxyInstantiate() {
        $result = $this->instantiateClass($this->class, $this->args);
        unset($this->args);
        unset($this->class);
        return $result;
    }
}