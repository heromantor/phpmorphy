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
 * @decorator-decoratee-class phpMorphy_Dict_Flexia
 * @decorator-decorator-class phpMorphy_Dict_FlexiaDecorator
 */

abstract class phpMorphy_Dict_FlexiaDecorator extends phpMorphy_Dict_Flexia implements phpMorphy_DecoratorInterface {
    /** @var phpMorphy_Dict_Flexia */
    private $object;
    /** @var Closure|null */
    private $on_instantiate;
    
    /**
     * @param $object phpMorphy_Dict_Flexia
     */
    function __construct(phpMorphy_Dict_Flexia $object) {
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
     * @param $object phpMorphy_Dict_Flexia
     * @return phpMorphy_Dict_FlexiaDecorator
     */
    protected function setDecorateeObject(phpMorphy_Dict_Flexia $object) {
        $this->object = $object;
        return $this;
    }
    
    /**
     * @return phpMorphy_Dict_Flexia
     */
    public function getDecorateeObject() {
        return $this->object;
    }
    
    /**
     * @param string $class
     * @param array $ctorArgs
     * @return phpMorphy_Dict_Flexia
     */
    static protected function instantiateClass($class, $ctorArgs) {
        $ref = new ReflectionClass($class);
        return $ref->newInstanceArgs($ctorArgs);
    }
    
    /**
     * @param string $propName
     * @return phpMorphy_Dict_Flexia
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
     * Must return instance of 'phpMorphy_Dict_Flexia'
     * @abstract
     * @return object
     */
    protected function proxyInstantiate() {
        if(!isset($this->on_instantiate)) {
            throw new phpMorphy_Exception('You must implement phpMorphy_Dict_FlexiaDecorator::proxyInstantiate or pass \$onInstantiate to actAsProxy() method');
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

    public function setPrefix($prefix) {
        $result = $this->object->setPrefix($prefix);
        return $result === $this->object ? $this : $result;
    }

    public function setSuffix($suffix) {
        $result = $this->object->setSuffix($suffix);
        return $result === $this->object ? $this : $result;
    }

    public function setAncodeId($id) {
        $result = $this->object->setAncodeId($id);
        return $result === $this->object ? $this : $result;
    }

    public function getPrefix() {
        $result = $this->object->getPrefix();
        return $result === $this->object ? $this : $result;
    }

    public function getSuffix() {
        $result = $this->object->getSuffix();
        return $result === $this->object ? $this : $result;
    }

    public function getAncodeId() {
        $result = $this->object->getAncodeId();
        return $result === $this->object ? $this : $result;
    }

    public function __toString() {
        $result = $this->object->__toString();
        return $result === $this->object ? $this : $result;
    }

}
