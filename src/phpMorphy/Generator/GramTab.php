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

class phpMorphy_Generator_GramTab {
    /**
     * @param string $outputHeaderFile
     * @param string $outputCppFile
     * @return void
     */
    static function generateCpp($outputHeaderFile, $outputCppFile) {
        $tpl = new phpMorphy_Generator_Template(__DIR__ . '/GramTab/tpl/cpp');
        $helpers = phpMorphy_Dict_GramTab_ConstStorage_Factory::getAllHelpers();

        $declaration = $tpl->get('declaration', array('helpers' => $helpers));
        $definition = $tpl->get('definition', array('helpers' => $helpers, 'header_file' => $outputHeaderFile));

        file_put_contents($outputHeaderFile, $declaration);
        file_put_contents($outputCppFile, $definition);
    }

    /**
     * @param string $outputFile
     * @return void
     */
    static function generatePhp($outputFile) {
        $tpl = new phpMorphy_Generator_Template(__DIR__ . '/GramTab/tpl/php');
        $consts = phpMorphy_Dict_GramTab_ConstStorage_Factory::getAllHelpers();
        $helper = new phpMorphy_Generator_GramTab_HelperPhp();

        $content = $tpl->get(
            'gramtab',
            array(
                 'helper' => $helper,
                 'all_constants' => $consts
            )
        );

        @mkdir(dirname($outputFile), 0744, true);
        file_put_contents($outputFile, $content);
    }
}