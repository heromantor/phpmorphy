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

class phpMorphy_Util_Fs {
    /**
     * @static
     * @param string $rootDir
     * @param string $matchRegExp
     * @param Closure|string $fn
     * @param bool $isRecursive
     * @return int
     */
    static function applyToEachFile($rootDir, $matchRegExp, $fn, $isRecursive = true) {
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($rootDir));
        $count = 0;

        foreach($iterator as $item) {
            if($iterator->isDot()) continue;
            $path = $item->getPathName();

            if(preg_match($matchRegExp, $path)) {
                ++$count;

                if(false === $fn($path)) {
                    break;
                }
            }
        }

        return $count;
    }

    /**
     * Delete empty directories (directories which contains only directories) starting from $dir
     * @static
     * @param string $dir
     * @param Closure|string|null $log
     * @return void
     */
    static function deleteEmptyDirectories($dir, $log = null) {
        $iterator = new DirectoryIterator($dir);
        $files_in_dir = 0;

        foreach($iterator as $node) {
            if($iterator->isDot()) continue;
            $pathname = $iterator->getPathName();
            $filename = $iterator->getFilename();

            if($iterator->isDir()) {
                 $files_in_dir += self::deleteEmptyDirectories(
                     $iterator->getPathName(),
                     $log
                 );
            } else {
                $files_in_dir++;
            }
        }

        if($files_in_dir == 0) {
            if(null !== $log) {
                $log("Remove empty dir '$dir'");
            }

            rmdir($dir);
        }

        return $files_in_dir;
    }
}