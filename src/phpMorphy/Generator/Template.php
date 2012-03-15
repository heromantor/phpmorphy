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

class phpMorphy_Generator_Template {
    /* @var string */
    protected $template_dir;

    /**
     * @param string $dir
     */
    function __construct($dir) {
        $this->template_dir = (string)$dir;
    }

    /**
     * @param string $templateFile
     * @param array $opts
     * @return string
     */
    function get($templateFile, $opts) {
        ob_start();

        extract($opts);

        $template_path = $this->template_dir . DIRECTORY_SEPARATOR . "$templateFile.tpl.php";
        if(!file_exists($template_path)) {
            throw new phpMorphy_Exception("Template '$template_path' not found");
        }

        include($template_path);

        $content = ob_get_contents();
        if(!ob_end_clean()) {
            throw new phpMorphy_Exception("Can`t invoke ob_end_clean()");
        }

        return $content;
    }
};