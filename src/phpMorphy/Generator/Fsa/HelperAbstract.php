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

abstract class phpMorphy_Generator_Fsa_HelperAbstract extends
    phpMorphy_Generator_HelperAbstract
{

    /**
     * @return string
     */
    abstract function getType();

    /**
     * @return string
     */
    function getParentClassName() {
        return 'phpMorphy_Fsa_FsaAbstract';
    }

    /**
     * @return string
     */
    function getClassName() {
        $type = ucfirst($this->getType());
        $storage_type = ucfirst($this->storage->getType());

        return "phpMorphy_Fsa_{$type}_$storage_type";
    }

    /**
     * @return string
     */
    function getFsaStartOffset() {
        return '$fsa_start';
    }

    /**
     * @return string
     */
    function checkTerm($var) {
        return "($var & 0x0100)";
    }

    /**
     * @return string
     */
    function getChar($var) {
        return "($var & 0xFF)";
    }

    /**
     * @return string
     */
    function prolog() {
        if (strlen($prolog = $this->storage->prolog())) {
            $prolog .= '; ';
        }

        $prolog .= '$fsa_start = $this->fsa_start';

        return $prolog;
    }

    /**
     * @return string
     */
    function unpackTrans($expression) {
        return "unpack('V', $expression)";
    }

    /**
     * @return string
     */
    function getTransSize() {
        return 4;
    }

    /**
     * @return string
     */
    function idx2offset($idxVar) {
        $trans_size = $this->getTransSize();

        if (($trans_size & ($trans_size - 1)) == 0) {
            // if trans size is power of two
            $multiple = '<< ' . (int) log($trans_size, 2);
        } else {
            $multiple = "* $trans_size";
        }

        return "(($idxVar) $multiple)";
    }

    /**
     * @return string
     */
    function readTrans($transVar, $charVar) {
        $read = $this->storage->read($this->getOffsetByTrans($transVar, $charVar),
                                     $this->getTransSize());
        return $this->unpackTrans($read);
    }

    /**
     * @return string
     */
    function seekTrans($transVar, $charVar) {
        return $this->storage->seek($this->getOffsetByTrans($transVar, $charVar));
    }

    /**
     * @return string
     */
    function readAnnotTrans($transVar) {
        $read = $this->storage->read($this->getAnnotOffsetByTrans($transVar),
                                     $this->getTransSize());
        return $this->unpackTrans($read);
    }

    /**
     * @return string
     */
    function seekAnnotTrans($transVar) {
        return $this->storage->seek($this->getAnnotOffsetByTrans($transVar));
    }

    /**
     * @return string
     */
    function getOffsetByTrans($transVar, $charVar) {
        return $this->getOffsetInFsa(
            $this->idx2offset($this->getIndexByTrans($transVar, $charVar))
        );
    }

    /**
     * @return string
     */
    function getAnnotOffsetByTrans($transVar) {
        return $this->getOffsetInFsa(
            $this->idx2offset($this->getAnnotIndexByTrans($transVar))
        );
    }

    /**
     * @return string
     */
    function getOffsetInFsa($offset) {
        return sprintf('%s + %s', $this->getFsaStartOffset(), $offset);
    }

    /**
     * @return string
     */
    protected function processTpl($name, $opts = array()) {
        $opts['helper'] = $this;

        return $this->tpl->get($this->getType() . '/' . $name, $opts);
    }

    /**
     * @return string
     */
    function tplFindCharInState() {
        return $this->processTpl('find_char_in_state');
    }

    /**
     * @return string
     */
    function tplUnpackTrans() {
        return $this->processTpl('unpack_trans');
    }

    /**
     * @return string
     */
    function tplReadState() {
        return $this->processTpl('read_state');
    }

    /**
     * @return string
     */
    function tplExtraFuncs() {
        return $this->processTpl('extra_funcs');
    }

    /**
     * @return string
     */
    function tplExtraProps() {
        return $this->processTpl('extra_props');
    }

    /**
     * @return string
     */
    abstract function getRootTransOffset();

    /**
     * @return string
     */
    abstract function getDest($var);

    /**
     * @return string
     */
    abstract function getAnnotIdx($var);

    /**
     * @return string
     */
    abstract function getIndexByTrans($transVar, $charVar);

    /**
     * @return string
     */
    abstract function getAnnotIndexByTrans($transVar);
}