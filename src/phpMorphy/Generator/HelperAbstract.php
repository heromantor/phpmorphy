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

abstract class phpMorphy_Generator_HelperAbstract {
    /* @var phpMorphy_Generator_Template */
    protected $tpl;
    /* @var phpMorphy_Generator_StorageHelperInterface */
    protected $storage;

    /**
     * @param phpMorphy_Generator_Template $tpl
     * @param phpMorphy_Generator_StorageHelperInterface $storage
     */
    function __construct(phpMorphy_Generator_Template $tpl, phpMorphy_Generator_StorageHelperInterface $storage) {
        $this->tpl = $tpl;
        $this->storage = $storage;
    }

    /**
     * @return phpMorphy_Generator_StorageHelperInterface
     */
    function getStorage() {
        return $this->storage;
    }

    /**
     * @param string $str
     * @param string $suffix
     * @return void
     */
    function out($str, $suffix) {
        if(strlen($str)) {
            echo $str, $suffix;
        }
    }
}