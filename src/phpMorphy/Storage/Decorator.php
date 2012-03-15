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
 * @decorator-decoratee-class phpMorphy_Storage_StorageInterface
 * @decorator-decorator-class phpMorphy_Storage_Decorator
 */

abstract class phpMorphy_Storage_Decorator implements phpMorphy_Storage_StorageInterface, phpMorphy_DecoratorInterface {
    /** @var phpMorphy_Storage_StorageInterface */
    private $object;
    /** @var Closure|null */
    private $on_instantiate;
    
    /**
     * @param $object phpMorphy_Storage_StorageInterface
     */
    function __construct(phpMorphy_Storage_StorageInterface $object) {
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
     * @param $object phpMorphy_Storage_StorageInterface
     * @return phpMorphy_Storage_Decorator
     */
    protected function setDecorateeObject(phpMorphy_Storage_StorageInterface $object) {
        $this->object = $object;
        return $this;
    }
    
    /**
     * @return phpMorphy_Storage_StorageInterface
     */
    public function getDecorateeObject() {
        return $this->object;
    }
    
    /**
     * @param string $class
     * @param array $ctorArgs
     * @return phpMorphy_Storage_StorageInterface
     */
    static protected function instantiateClass($class, $ctorArgs) {
        $ref = new ReflectionClass($class);
        return $ref->newInstanceArgs($ctorArgs);
    }
    
    /**
     * @param string $propName
     * @return phpMorphy_Storage_StorageInterface
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
     * Must return instance of 'phpMorphy_Storage_StorageInterface'
     * @abstract
     * @return object
     */
    protected function proxyInstantiate() {
        if(!isset($this->on_instantiate)) {
            throw new phpMorphy_Exception('You must implement phpMorphy_Storage_Decorator::proxyInstantiate or pass \$onInstantiate to actAsProxy() method');
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
    * Returns name of file
    * @return string
    */
    public function getFileName() {
        $result = $this->object->getFileName();
        return $result === $this->object ? $this : $result;
    }

    /**
    * Returns size of file in bytes
    * @abstract
    * @return int
    */
    public function getFileSize() {
        $result = $this->object->getFileSize();
        return $result === $this->object ? $this : $result;
    }

    /**
    * Returns resource of this storage
    * @return mixed
    */
    public function getResource() {
        $result = $this->object->getResource();
        return $result === $this->object ? $this : $result;
    }

    /**
    * Returns type of this storage
    * @return string
    */
    public function getTypeAsString() {
        $result = $this->object->getTypeAsString();
        return $result === $this->object ? $this : $result;
    }

    /**
    * Reads $len bytes from $offset offset
    *
    * @throws phpMorphy_Exception
    * @param int $offset Read from this offset
    * @param int $length How many bytes to read
    * @param bool $exactLength If this set to true, then exception thrown when we read less than $len bytes
    * @return string
    */
    public function read($offset, $length, $exactLength = true) {
        $result = $this->object->read($offset, $length, $exactLength);
        return $result === $this->object ? $this : $result;
    }

}
