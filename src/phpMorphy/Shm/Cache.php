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

class phpMorphy_Shm_Cache implements phpMorphy_Shm_CacheInterface {
    const DEFAULT_MODE = 0644;
    const READ_BLOCK_SIZE = 8192;

    const SHM_SEGMENT_SIZE = 25165824; // 24Mb
    const SHM_SEGMENT_ID = 0x54358308;
    const SEMAPHORE_KEY = 0x54358309;
    const SHM_HEADER_MAX_SIZE = 32767;

    protected static $EXTENSION_PRESENT = null;

    protected
        $options,
        $semaphore,
        $segment
        ;

    function __construct($options = array(), $clear = false) {
        if(!isset(self::$EXTENSION_PRESENT)) {
            self::$EXTENSION_PRESENT = extension_loaded('shmop');
        }

        if(!self::$EXTENSION_PRESENT) {
            throw new phpMorphy_Exception("shmop extension needed");
        }

        $this->options = $options = $this->repairOptions($options);

        $this->semaphore = phpMorphy_Semaphore_SemaphoreFactory::create($options['semaphore_key'], $options['no_lock']);

        $this->segment = $this->getSegment($options['segment_id'], $options['segment_size']);

        if($clear) {
            $this->semaphore->remove();
            $this->initHeaderObject($this->segment);
        }
    }

    static function clearSemaphore($semaphoreId = null) {
        $semaphoreId = isset($semaphoreId) ? $semaphoreId : self::SEMAPHORE_KEY;

        $sem = phpMorphy_Semaphore_SemaphoreFactory::create($semaphoreId);
        return $sem->remove();
    }

    protected function repairOptions($options) {
        $defaults = array(
            'semaphore_key' => self::SEMAPHORE_KEY,
            'segment_id' => self::SHM_SEGMENT_ID,
            'segment_size' => self::SHM_SEGMENT_SIZE,
            'with_mtime' => false,
            'header_max_size' => self::SHM_HEADER_MAX_SIZE,
            'no_lock' => false,
        );

        return (array)$options + $defaults;
    }

    function close() {
        if(isset($this->segment)) {
            shmop_close($this->segment);
            $this->segment = null;
        }
    }

    protected function safeInvoke($filePath, $method) {
        $this->lock();

        try {
            $header = $this->readHeader();

            $result = $this->$method($filePath, $header);

            // writeHeader is atomic
            $this->writeHeader($this->segment, $header);

            $this->unlock();

            return $result;
        } catch (Exception $e) {
            $this->unlock();

            throw $e;
        }
    }

    protected function doGet($filePath, $header) {
        $result = array();
        foreach((array)$filePath as $file) {
            $result[$file] = $this->getSingleFile($header, $file);
        }

        if(!is_array($filePath)) {
            $result = $result[$filePath];
        }

        return $result;
    }

    function get($filePath) {
        if(!is_array($filePath)) {
            return $this->createFileDescriptor($this->safeInvoke($filePath, 'doGet'));
        } else {
            $result = array();

            foreach($this->safeInvoke($filePath, 'doGet') as $file => $item) {
                $result[$file] = $this->createFileDescriptor($item);
            }

            return $result;
        }
    }


    protected function getSingleFile($header, $filePath) {
        try {
            $fh = false;

            if(false !== $header->exists($filePath)) {
                $result = $header->lookup($filePath);

                if(!$this->options['with_mtime']) {
                    return $result;
                }

                if(false === ($mtime = filemtime($filePath))) {
                    throw new phpMorphy_Exception("Can`t get mtime attribute for '$filePath' file");
                }

                if($result['mtime'] === $mtime) {
                    return $result;
                }

                $fh = $this->openFile($filePath);

                // update
                $header->delete($filePath);
                $result = $header->register($filePath, $fh);

                $this->saveFile($fh, $result['offset']);

                fclose($fh);

                return $result;
            }

            // register
            $fh = $this->openFile($filePath);

            $result = $header->register($filePath, $fh);

            $this->saveFile($fh, $result['offset']);

            fclose($fh);

            return $result;
        } catch (Exception $e) {
            if(isset($fh) && $fh !== false) {
                fclose($fh);
            }

            throw $e;
        }
    }

    protected function doClear($filePath, $header) {
        $header->clear();
    }

    function clear() {
        $this->safeInvoke(null, 'doClear');
    }

    protected function doDelete($filePath, $header) {
        foreach((array)$filePath as $file) {
            $header->delete($file);
        }
    }

    function delete($filePath) {
        $this->safeInvoke($filePath, 'doDelete');
    }

    protected function doReload($filePath, $header) {
        $return = array();

        foreach((array)$filePath as $file) {
            $fh = $this->openFile($file);

            // update
            $header->delete($file);
            $result = $header->register($file, $fh);

            $this->saveFile($fh, $result['offset']);

            fclose($fh);
            $fh = false;

            $return[$file] = $result;
        }

        if(!is_array($filePath)) {
            $return = $return[$filePath];
        }

        return $return;
    }

    function reload($filePath) {
        if(!is_array($filePath)) {
            return $this->createFileDescriptor($this->safeInvoke($filePath, 'doReload'));
        } else {
            $result = array();

            foreach($this->safeInvoke($filePath, 'doReload') as $file => $item) {
                $result[$file] = $this->createFileDescriptor($item);
            }

            return $result;
        }
    }

    function reloadIfExists($filePath) {
        try {
            return $this->reload($filePath);
        } catch (Exception $e) {
            return false;
        }
    }

    function free() {
        $this->lock();
        if(false === shmop_delete($this->segment)) {
            throw new phpMorphy_Exception("Can`t delete $this->segment segment");
        }

        $this->close();

        $this->unlock();
    }

    function getFilesList() {
        $this->lock();

        $result = $this->readHeader()->getAllFiles();

        $this->unlock();

        return $result;
    }

    protected function createFileDescriptor($result) {
        return new phpMorphy_Shm_FileDescriptor($this->segment, $result['size'], $this->options['header_max_size'] + $result['offset']);
    }

    protected function openFile($filePath) {
        if(false === ($fh = fopen($filePath, 'rb'))) {
            throw new phpMorphy_Exception("Can`t open '$filePath' file");
        }

        return $fh;
    }

    protected function lock() {
        $this->semaphore->lock();
    }

    protected function unlock() {
        $this->semaphore->unlock();
    }

    /**
     * @return int
     */
    protected function getFilesOffset() {
        return $this->options['header_max_size'];
    }

    /**
     * @return int
     */
    protected function getMaxOffset() {
        return $this->options['segment_size'] - 1;
    }

    protected function saveFile($fh, $offset) {
        if(false === ($stat = fstat($fh))) {
            throw new phpMorphy_Exception("Can`t fstat");
        }

        $file_size = $stat['size'];
        $chunk_size = self::READ_BLOCK_SIZE;

        $max_offset = $offset + $file_size;

        if($max_offset >= $this->getMaxOffset()) {
            throw new phpMorphy_Exception("Can`t write to $offset offset, not enough space");
        }

        $i = 0;
        while(!feof($fh)) {
            $data = fread($fh, $chunk_size);
            if(false === (shmop_write($this->segment, $data, $this->getFilesOffset() + $offset + $i))) {
                throw new phpMorphy_Exception("Can`t write chunk of file to shm");
            }

            $i += $chunk_size;
        }
    }

    protected function getSegment($segmentId, $segmentSize) {
        $this->lock();

        try {
            $shm_id = $this->openSegment($segmentId, $segmentSize, $is_new);

            if($is_new) {
                $this->initHeaderObject($shm_id, false);
            }
        } catch (Exception $e) {
            $this->unlock();
            throw $e;
        }

        $this->unlock();

        return $shm_id;
    }

    protected function initHeaderObject($shmId, $lock = true) {
        if($lock) {
            $this->lock();
            $this->writeHeader($shmId, $this->createHeader($shmId));
            $this->unlock();
        } else {
            $this->writeHeader($shmId, $this->createHeader($shmId));
        }
    }

    protected function readHeader() {
        if(false === ($data = shmop_read($this->segment, 0, $this->getFilesOffset()))) {
            throw new phpMorphy_Exception("Can`t read header for " . $this->segment);
        }

        if(false === ($result = unserialize($data))) {
            throw new phpMorphy_Exception("Can`t unserialize header for " . $this->segment);
        }

        return $result;
    }

    protected function writeHeader($shmId, phpMorphy_Shm_Header $header) {
        $data = serialize($header);

        if($GLOBALS['__phpmorphy_strlen']($data) > $this->getFilesOffset()) {
            throw new phpMorphy_Exception("Too long header, try increase SHM_HEADER_MAX_SIZE");
        }

        if(false === shmop_write($shmId, $data, 0)) {
            throw new phpMorphy_Exception("Can`t write shm header");
        }
    }

    protected function createHeader($shmId) {
        return new phpMorphy_Shm_Header($shmId, $this->options['segment_size']);
    }

    protected function openSegment($segmentId, $size, &$new = null) {
        $new = false;

        if(false === ($handle = @shmop_open($segmentId, 'w', 0, 0))) {
            if(false === ($handle = shmop_open($segmentId, 'n', self::DEFAULT_MODE, $size))) {
                throw new phpMorphy_Exception("Can`t create SHM segment with '$segmentId' id and $size size");
            }

            $new = true;
        }

        return $handle;
    }
}
