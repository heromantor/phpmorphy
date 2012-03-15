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
 * @decorator-decoratee-class phpMorphy_GramTab_GramTabInterface
 * @decorator-decorator-class phpMorphy_GramTab_Decorator
 */

abstract class phpMorphy_GramTab_Decorator implements phpMorphy_GramTab_GramTabInterface, phpMorphy_DecoratorInterface {
    /** @var phpMorphy_GramTab_GramTabInterface */
    private $object;
    /** @var Closure|null */
    private $on_instantiate;
    
    /**
     * @param $object phpMorphy_GramTab_GramTabInterface
     */
    function __construct(phpMorphy_GramTab_GramTabInterface $object) {
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
     * @param $object phpMorphy_GramTab_GramTabInterface
     * @return phpMorphy_GramTab_Decorator
     */
    protected function setDecorateeObject(phpMorphy_GramTab_GramTabInterface $object) {
        $this->object = $object;
        return $this;
    }
    
    /**
     * @return phpMorphy_GramTab_GramTabInterface
     */
    public function getDecorateeObject() {
        return $this->object;
    }
    
    /**
     * @param string $class
     * @param array $ctorArgs
     * @return phpMorphy_GramTab_GramTabInterface
     */
    static protected function instantiateClass($class, $ctorArgs) {
        $ref = new ReflectionClass($class);
        return $ref->newInstanceArgs($ctorArgs);
    }
    
    /**
     * @param string $propName
     * @return phpMorphy_GramTab_GramTabInterface
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
     * Must return instance of 'phpMorphy_GramTab_GramTabInterface'
     * @abstract
     * @return object
     */
    protected function proxyInstantiate() {
        if(!isset($this->on_instantiate)) {
            throw new phpMorphy_Exception('You must implement phpMorphy_GramTab_Decorator::proxyInstantiate or pass \$onInstantiate to actAsProxy() method');
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
    * @abstract
    * @param string|int $ancodeId
    * @return string[]|int[]
    */
    public function getGrammems($ancodeId) {
        $result = $this->object->getGrammems($ancodeId);
        return $result === $this->object ? $this : $result;
    }

    /**
    * @abstract
    * @param string|int $ancodeId
    * @return string|int
    */
    public function getPartOfSpeech($ancodeId) {
        $result = $this->object->getPartOfSpeech($ancodeId);
        return $result === $this->object ? $this : $result;
    }

    /**
    * @abstract
    * @param string[]|int[] $ids
    * @return string[]|int[]
    */
    public function resolveGrammemIds($ids) {
        $result = $this->object->resolveGrammemIds($ids);
        return $result === $this->object ? $this : $result;
    }

    /**
    * @abstract
    * @param string|int $id
    * @return string|int
    */
    public function resolvePartOfSpeechId($id) {
        $result = $this->object->resolvePartOfSpeechId($id);
        return $result === $this->object ? $this : $result;
    }

    /**
    * @abstract
    * @return void
    */
    public function includeConsts() {
        $result = $this->object->includeConsts();
        return $result === $this->object ? $this : $result;
    }

    /**
    * @abstract
    * @param string|int $ancodeId
    * @param string|int $commonAncode
    * @return string|int
    */
    public function ancodeToString($ancodeId, $commonAncode = null) {
        $result = $this->object->ancodeToString($ancodeId, $commonAncode);
        return $result === $this->object ? $this : $result;
    }

    /**
    * @abstract
    * @param string $string
    * @return string|int
    */
    public function stringToAncode($string) {
        $result = $this->object->stringToAncode($string);
        return $result === $this->object ? $this : $result;
    }

    /**
    * @abstract
    * @param string|int $partOfSpeechId
    * @param string[]|int[] $grammemIds
    * @return string
    */
    public function toString($partOfSpeechId, $grammemIds) {
        $result = $this->object->toString($partOfSpeechId, $grammemIds);
        return $result === $this->object ? $this : $result;
    }

}
