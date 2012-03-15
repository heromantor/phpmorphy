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

abstract class phpMorphy_Fsa_FsaAbstract implements phpMorphy_Fsa_FsaInterface {
    const HEADER_SIZE = 128;

    protected
        /** @var mixed */
        $resource,
        /** @var array */
        $header,
        /** @var int */
        $fsa_start,
        /** @var int */
        $root_trans,
        /** @var string[] */
        $alphabet;

    /**
     * @param mixed $resource
     * @param array $header
     */
    protected function __construct($resource, $header) {
        $this->resource = $resource;
        $this->header = $header;
        $this->fsa_start = $header['fsa_offset'];
        $this->root_trans = $this->readRootTrans();
    }

    /**
     * @static
     * @throws phpMorphy_Exception
     * @param phpMorphy_Storage_StorageInterface $storage
     * @param bool $isLazy
     * @return phpMorphy_Fsa_FsaInterface
     */
    static function create(phpMorphy_Storage_StorageInterface $storage, $isLazy) {
        if($isLazy) {
            return new phpMorphy_Fsa_Proxy($storage);
        }

        $header = phpMorphy_Fsa_FsaAbstract::readHeader(
            $storage->read(0, self::HEADER_SIZE, true)
        );

        if(!phpMorphy_Fsa_FsaAbstract::validateHeader($header)) {
            throw new phpMorphy_Exception('Invalid fsa format');
        }

        if($header['flags']['is_sparse']) {
            $type = 'sparse';
        } else if($header['flags']['is_tree']) {
            $type = 'tree';
        } else {
            throw new phpMorphy_Exception('Only sparse or tree fsa`s supported');
        }

        $storage_type = $storage->getTypeAsString();
        $file_path = __DIR__ . "/access/fsa_{$type}_{$storage_type}.php";
        $clazz = 'phpMorphy_Fsa_' . ucfirst($type) . '_' . ucfirst($storage_type);

        return new $clazz(
            $storage->getResource(),
            $header
        );
    }

    /**
     * @return int
     */
    function getRootTrans() {
        return $this->root_trans;
    }

    /**
     * @return phpMorphy_Fsa_State
     */
    function getRootState() {
        return $this->createState($this->getRootStateIndex());
    }

    /**
     * @return string[]
     */
    function getAlphabet() {
        if (!isset($this->alphabet)) {
            $this->alphabet = str_split($this->readAlphabet());
        }

        return $this->alphabet;
    }

    /**
     * @param int $index
     * @return phpMorphy_Fsa_State
     */
    protected function createState($index) {
        return new phpMorphy_Fsa_State($this, $index);
    }

    /**
     * @static
     * @throws phpMorphy_Exception
     * @param string $headerRaw
     * @return array
     */
    static protected function readHeader($headerRaw) {
        if ($GLOBALS['__phpmorphy_strlen']($headerRaw) != self::HEADER_SIZE) {
            throw new phpMorphy_Exception('Invalid header string given');
        }

        $header = unpack(
            'a4fourcc/Vver/Vflags/Valphabet_offset/Vfsa_offset/Vannot_offset/Valphabet_size/Vtranses_count/Vannot_length_size/'
            .
            'Vannot_chunk_size/Vannot_chunks_count/Vchar_size/Vpadding_size/Vdest_size/Vhash_size',
            $headerRaw
        );

        if (false === $header) {
            throw new phpMorphy_Exception('Can`t unpack header');
        }

        $flags = array();
        $raw_flags = $header['flags'];
        $flags['is_tree'] = $raw_flags & 0x01 ? true : false;
        $flags['is_hash'] = $raw_flags & 0x02 ? true : false;
        $flags['is_sparse'] = $raw_flags & 0x04 ? true : false;
        $flags['is_be'] = $raw_flags & 0x08 ? true : false;

        $header['flags'] = $flags;

        $header['trans_size'] =
                $header['char_size'] + $header['padding_size'] + $header['dest_size'] +
                $header['hash_size'];

        return $header;
    }

    /**
     * @static
     * @param array $header
     * @return bool
     */
    static protected function validateHeader($header) {
        if (
            'meal' != $header['fourcc'] ||
            3 != $header['ver'] ||
            $header['char_size'] != 1 ||
            $header['padding_size'] > 0 ||
            $header['dest_size'] != 3 ||
            $header['hash_size'] != 0 ||
            $header['annot_length_size'] != 1 ||
            $header['annot_chunk_size'] != 1 ||
            $header['flags']['is_be'] ||
            $header['flags']['is_hash'] ||
            1 == 0
        ) {
            return false;
        }

        return true;
    }

    /**
     * @return int
     */
    protected function getRootStateIndex() {
        return 0;
    }

    /**
     * @abstract
     * @return int
     */
    abstract protected function readRootTrans();

    /**
     * @abstract
     * @return string
     */
    abstract protected function readAlphabet();
};