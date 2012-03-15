#!/usr/bin/env php
<?php
set_include_path(__DIR__ . '/../../src/' . PATH_SEPARATOR . get_include_path());
require('phpMorphy.php');

$current_year = date('Y');
$marker_text = 'This file is part of phpMorphy project';
$copyright_text = <<<EOS
/*
* This file is part of phpMorphy project
*
* Copyright (c) 2007-$current_year Kamaev Vladimir <heromantor@users.sourceforge.net>
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

EOS;

phpMorphy_Util_Fs::applyToEachFile(
    getcwd(),
    '~\.php$~',
    function($path) use($copyright_text, $marker_text) {
        $content = file_get_contents($path);
        if(substr($content, 0, 5) !== '<' . '?php' || strpos($content, $marker_text) !== false) {
            echo "Skip $path\n";
            return;
        }

        $content = '<' . '?php' . PHP_EOL . $copyright_text . substr($content, 5);;
        file_put_contents($path, $content);
    },
    true
);