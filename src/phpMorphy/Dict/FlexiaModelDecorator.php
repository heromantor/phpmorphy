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
 * @decorator-decoratee-class phpMorphy_Dict_FlexiaModel
 * @decorator-decorator-class phpMorphy_Dict_FlexiaModelDecorator
 */

abstract class phpMorphy_Dict_FlexiaModelDecorator extends phpMorphy_Dict_FlexiaModel implements phpMorphy_DecoratorInterface {
    /** @var phpMorphy_Dict_FlexiaModel */
    private $object;
    /** @var Closure|null */
    private $on_instantiate;
    
    /**
     * @param $object phpMorphy_Dict_FlexiaModel
     */
    function __construct(phpMorphy_Dict_FlexiaModel $object) {
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
     * @param $object phpMorphy_Dict_FlexiaModel
     * @return phpMorphy_Dict_FlexiaModelDecorator
     */
    protected function setDecorateeObject(phpMorphy_Dict_FlexiaModel $object) {
        $this->object = $object;
        return $this;
    }
    
    /**
     * @return phpMorphy_Dict_FlexiaModel
     */
    public function getDecorateeObject() {
        return $this->object;
    }
    
    /**
     * @param string $class
     * @param array $ctorArgs
     * @return phpMorphy_Dict_FlexiaModel
     */
    static protected function instantiateClass($class, $ctorArgs) {
        $ref = new ReflectionClass($class);
        return $ref->newInstanceArgs($ctorArgs);
    }
    
    /**
     * @param string $propName
     * @return phpMorphy_Dict_FlexiaModel
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
     * Must return instance of 'phpMorphy_Dict_FlexiaModel'
     * @abstract
     * @return object
     */
    protected function proxyInstantiate() {
        if(!isset($this->on_instantiate)) {
            throw new phpMorphy_Exception('You must implement phpMorphy_Dict_FlexiaModelDecorator::proxyInstantiate or pass \$onInstantiate to actAsProxy() method');
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

    public function setId($id) {
        $result = $this->object->setId($id);
        return $result === $this->object ? $this : $result;
    }

    public function getId() {
        $result = $this->object->getId();
        return $result === $this->object ? $this : $result;
    }

    public function getFlexias() {
        $result = $this->object->getFlexias();
        return $result === $this->object ? $this : $result;
    }

    public function __toString() {
        $result = $this->object->__toString();
        return $result === $this->object ? $this : $result;
    }

    /**
    * @return array
    */
    public function getData() {
        $result = $this->object->getData();
        return $result === $this->object ? $this : $result;
    }

    /**
    * @return Iterator
    */
    public function getIterator() {
        $result = $this->object->getIterator();
        return $result === $this->object ? $this : $result;
    }

    /**
    * @throws phpMorphy_Exception
    * @param Traversable $values
    * @return void
    */
    public function import($values) {
        $result = $this->object->import($values);
        return $result === $this->object ? $this : $result;
    }

    /**
    * @param mixed $value
    * @return void
    */
    public function append($value) {
        $result = $this->object->append($value);
        return $result === $this->object ? $this : $result;
    }

    /**
    * @return void
    */
    public function clear() {
        $result = $this->object->clear();
        return $result === $this->object ? $this : $result;
    }

    /**
    * @param int $offset
    * @return bool
    */
    public function offsetExists($offset) {
        $result = $this->object->offsetExists($offset);
        return $result === $this->object ? $this : $result;
    }

    /**
    * @throws phpMorphy_Exception
    * @param int $offset
    * @return mixed
    */
    public function offsetGet($offset) {
        $result = $this->object->offsetGet($offset);
        return $result === $this->object ? $this : $result;
    }

    /**
    * @param int $offset
    * @param mixed $value
    * @return void
    */
    public function offsetSet($offset, $value) {
        $result = $this->object->offsetSet($offset, $value);
        return $result === $this->object ? $this : $result;
    }

    /**
    * @param int $offset
    * @return void
    */
    public function offsetUnset($offset) {
        $result = $this->object->offsetUnset($offset);
        return $result === $this->object ? $this : $result;
    }

    /**
    * @return int
    */
    public function count() {
        $result = $this->object->count();
        return $result === $this->object ? $this : $result;
    }

}
