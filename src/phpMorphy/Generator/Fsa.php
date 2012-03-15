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

class phpMorphy_Generator_Fsa {
    /**
     * @param string $outputDirectory
     * @return void
     */
    static function generate($outputDirectory) {
        $helpers_ary = array('Sparse', 'Tree');
        $storage_ary = array('File', 'Mem', 'Shm');

        $tpl = new phpMorphy_Generator_Template(__DIR__ . '/Fsa/tpl');

        foreach ($helpers_ary as $helper_name) {
            $helper_class = "phpMorphy_Generator_Fsa_Helper" . ucfirst($helper_name);

            foreach ($storage_ary as $storage_name) {
                $storage_class = "phpMorphy_Generator_StorageHelper_" . ucfirst($storage_name);
                $helper = new $helper_class($tpl, new $storage_class());

                $result = $tpl->get('fsa', array('helper' => $helper));

                $file_path =
                        $outputDirectory . DIRECTORY_SEPARATOR .
                        phpMorphy_Loader::classNameToFilePath($helper->getClassName());

                @mkdir(dirname($file_path), 0744, true);
                file_put_contents($file_path, $result);
            }
        }
    }
}