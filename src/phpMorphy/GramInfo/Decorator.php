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

/**
 * @decorator-auto-regenerate TRUE
 * @decorator-generated-at Thu, 23 Jun 2011 05:28:55 +0400
 * @decorator-decoratee-class phpMorphy_GramInfo_GramInfoInterface
 * @decorator-decorator-class phpMorphy_GramInfo_Decorator
 */

abstract class phpMorphy_GramInfo_Decorator implements phpMorphy_GramInfo_GramInfoInterface, phpMorphy_DecoratorInterface {
    /** @var phpMorphy_GramInfo_GramInfoInterface */
    private $object;
    /** @var Closure|null */
    private $on_instantiate;
    
    /**
     * @param $object phpMorphy_GramInfo_GramInfoInterface
     */
    function __construct(phpMorphy_GramInfo_GramInfoInterface $object) {
        $this->setDecorateeObject($object);
    }
    
    /**
     * Set current decorator behaviour to proxy model
     * @param Closure|null $onInstantiate
     */
    protected function actAsProxy(/*TODO: uncomment for php >= 5.3 Closure */$onInstantiate = null) {
        unset($this->object);
        $this->on_instantiate = $onInstantiate;
    }
    
    /**
     * @param $object phpMorphy_GramInfo_GramInfoInterface
     * @return phpMorphy_GramInfo_Decorator
     */
    protected function setDecorateeObject(phpMorphy_GramInfo_GramInfoInterface $object) {
        $this->object = $object;
        return $this;
    }
    
    /**
     * @return phpMorphy_GramInfo_GramInfoInterface
     */
    public function getDecorateeObject() {
        return $this->object;
    }
    
    /**
     * @param string $class
     * @param array $ctorArgs
     * @return phpMorphy_GramInfo_GramInfoInterface
     */
    static protected function instantiateClass($class, $ctorArgs) {
        $ref = new ReflectionClass($class);
        return $ref->newInstanceArgs($ctorArgs);
    }
    
    /**
     * @param string $propName
     * @return phpMorphy_GramInfo_GramInfoInterface
     */
    public function __get($propName) {
        if('object' === $propName) {
            $obj = $this->proxyInstantiate();
            $this->setDecorateeObject($obj);
            return $obj;
        }
    
        throw new phpMorphy_Exception("Unknown property '$propName'");
    }
    
    /**
     * This method invoked by __get() at first time access to proxy object
     * Must return instance of 'phpMorphy_GramInfo_GramInfoInterface'
     * @abstract
     * @return object
     */
    protected function proxyInstantiate() {
        if(!isset($this->on_instantiate)) {
            throw new phpMorphy_Exception('You must implement phpMorphy_GramInfo_Decorator::proxyInstantiate or pass \$onInstantiate to actAsProxy() method');
        }
    
        $fn = $this->on_instantiate;
        unset($this->on_instantiate);
    
        return $fn();
    }
    
    /**
     * Implement deep copy paradigm
     */
    function __clone() {
        if(isset($this->object)) {
            $this->object = clone $this->object;
        }
    }

    /**
    * Returns langugage for graminfo file
    * @return string
    */
    public function getLocale() {
        $result = $this->object->getLocale();
        return $result === $this->object ? $this : $result;
    }

    /**
    * Return encoding for graminfo file
    * @return string
    */
    public function getEncoding() {
        $result = $this->object->getEncoding();
        return $result === $this->object ? $this : $result;
    }

    /**
    * @return bool
    * TODO: implement this latter in dict
    */
    public function isInUpperCase() {
        $result = $this->object->isInUpperCase();
        return $result === $this->object ? $this : $result;
    }

    /**
    * Return size of character
    *   (cp1251(or any single byte encoding) - 1
    *   utf8 - 1
    *   utf16 - 2
    *   utf32 - 4
    *   etc..
    * @return int
    */
    public function getCharSize() {
        $result = $this->object->getCharSize();
        return $result === $this->object ? $this : $result;
    }

    /**
    * Return end of string value (usually string with \0 value of char_size + 1 length)
    * @return string
    */
    public function getEnds() {
        $result = $this->object->getEnds();
        return $result === $this->object ? $this : $result;
    }

    /**
    * Reads graminfo header
    *
    * @param int $offset
    * @return array
    */
    public function readGramInfoHeader($offset) {
        $result = $this->object->readGramInfoHeader($offset);
        return $result === $this->object ? $this : $result;
    }

    /**
    * Returns size of header struct
    * @return int
    */
    public function getGramInfoHeaderSize() {
        $result = $this->object->getGramInfoHeaderSize();
        return $result === $this->object ? $this : $result;
    }

    /**
    * Read ancodes section for header retrieved with readGramInfoHeader
    *
    * @param array $info
    * @return array
    */
    public function readAncodes($info) {
        $result = $this->object->readAncodes($info);
        return $result === $this->object ? $this : $result;
    }

    /**
    * Read flexias section for header retrieved with readGramInfoHeader
    *
    * @param array $info
    * @return array
    */
    public function readFlexiaData($info) {
        $result = $this->object->readFlexiaData($info);
        return $result === $this->object ? $this : $result;
    }

    /**
    * Read all graminfo headers offsets, which can be used latter for readGramInfoHeader method
    * @return int[]
    */
    public function readAllGramInfoOffsets() {
        $result = $this->object->readAllGramInfoOffsets();
        return $result === $this->object ? $this : $result;
    }

    /**
    * @abstract
    * @return array
    */
    public function getHeader() {
        $result = $this->object->getHeader();
        return $result === $this->object ? $this : $result;
    }

    /**
    * @abstract
    * @return array
    */
    public function readAllPartOfSpeech() {
        $result = $this->object->readAllPartOfSpeech();
        return $result === $this->object ? $this : $result;
    }

    /**
    * @abstract
    * @return array
    */
    public function readAllGrammems() {
        $result = $this->object->readAllGrammems();
        return $result === $this->object ? $this : $result;
    }

    /**
    * @abstract
    * @return array
    */
    public function readAllAncodes() {
        $result = $this->object->readAllAncodes();
        return $result === $this->object ? $this : $result;
    }

}
