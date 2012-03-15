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

class phpMorphy_GramTab_GramTab implements phpMorphy_GramTab_GramTabInterface {
    protected
        /** @var array */
        $data,
        /** @var array */
        $ancodes,
        /** @var array */
        $grammems,
        // $__ancodes_map,
        /** @var array */
        $poses;

    /**
     * @throws phpMorphy_Exception
     * @param phpMorphy_Storage_StorageInterface $storage
     *
     */
    protected function __construct(phpMorphy_Storage_StorageInterface $storage) {
        $this->data = unserialize($storage->read(0, $storage->getFileSize()));

        if(false === $this->data) {
            throw new phpMorphy_Exception("Broken gramtab data");
        }

        $this->grammems = $this->data['grammems'];
        $this->poses = $this->data['poses'];
        $this->ancodes = $this->data['ancodes'];
    }

    /**
     * TODO: remove this
     * @static
     * @param phpMorphy_Storage_StorageInterface $storage
     * @return phpMorphy_GramTab_GramTab
     */
    static function create(phpMorphy_Storage_StorageInterface $storage) {
        return new phpMorphy_GramTab_GramTab($storage);
    }

    /**
     * @throws phpMorphy_Exception
     * @param int|string $ancodeId
     * @return int[]|string[]
     */
    function getGrammems($ancodeId) {
        if(!isset($this->ancodes[$ancodeId])) {
            throw new phpMorphy_Exception("Invalid ancode id '$ancodeId'");
        }

        return $this->ancodes[$ancodeId]['grammem_ids'];
    }

    /**
     * @throws phpMorphy_Exception
     * @param int|string $ancodeId
     * @return int|string
     */
    function getPartOfSpeech($ancodeId) {
        if(!isset($this->ancodes[$ancodeId])) {
            throw new phpMorphy_Exception("Invalid ancode id '$ancodeId'");
        }

        return $this->ancodes[$ancodeId]['pos_id'];
    }

    /**
     * @throws phpMorphy_Exception
     * @param int[]|string[] $ids
     * @return string|string[]
     */
    function resolveGrammemIds($ids) {
        if(is_array($ids)) {
            $result = array();

            foreach($ids as $id) {
                if(!isset($this->grammems[$id])) {
                    throw new phpMorphy_Exception("Invalid grammem id '$id'");
                }

                $result[] = $this->grammems[$id]['name'];
            }

            return $result;
        } else {
            if(!isset($this->grammems[$ids])) {
                throw new phpMorphy_Exception("Invalid grammem id '$ids'");
            }

            return $this->grammems[$ids]['name'];
        }
    }

    /**
     * @throws phpMorphy_Exception
     * @param string|int $id
     * @return string|int
     */
    function resolvePartOfSpeechId($id) {
        if(!isset($this->poses[$id])) {
            throw new phpMorphy_Exception("Invalid part of speech id '$id'");
        }

        return $this->poses[$id]['name'];
    }

    /**
     * @return void
     */
    function includeConsts() {
        static $is_included = false;

        if(!$is_included) {
            require_once(__DIR__ . '/gramtab_consts.php');
            $is_included = true;
        }
    }

    /**
     * @param string $ancodeId
     * @param string $commonAncode
     * @return string
     */
    function ancodeToString($ancodeId, $commonAncode = null) {
        if(isset($commonAncode)) {
            $commonAncode = implode(',', $this->getGrammems($commonAncode)) . ',';
        }

        return
            $this->getPartOfSpeech($ancodeId) . ' ' .
            $commonAncode .
            implode(',', $this->getGrammems($ancodeId));
    }

    /**
     * TODO: implement this
     * @param string|int $partOfSpeech
     * @param string[]|int[] $grammems
     * @return int|string
     */
    protected function findAncode($partOfSpeech, $grammems) {
    }

    /**
     * @throws phpMorphy_Exception
     * @param string $string
     * @return string|int
     */
    function stringToAncode($string) {
        if(!isset($string)) {
            return null;
        }

        if(!isset($this->__ancodes_map[$string])) {
            throw new phpMorphy_Exception("Ancode with '$string' graminfo not found");
        }

        return $this->__ancodes_map[$string];
    }

    function toString($partOfSpeechId, $grammemIds) {
        return $partOfSpeechId . ' ' . implode(',', $grammemIds);
    }

    /**
     * @return array
     */
    protected function buildAncodesMap() {
        $result = array();

        foreach($this->ancodes as $ancode_id => $data) {
            $key = $this->toString($data['pos_id'], $data['grammem_ids']);

            $result[$key] = $ancode_id;
        }

        return $result;
    }

    /**
     * @throws phpMorphy_Exception
     * @param string $propName
     * @return mixed
     */
    function __get($propName) {
        switch($propName) {
            case '__ancodes_map':
                $this->__ancodes_map = $this->buildAncodesMap();
                return $this->__ancodes_map;
        }

        throw new phpMorphy_Exception("Unknown '$propName' property");
    }
}