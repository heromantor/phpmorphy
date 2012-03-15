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
 * @decorator-decoratee-class phpMorphy_AncodesResolver_AncodesResolverInterface
 * @decorator-decorator-class phpMorphy_AncodesResolver_Decorator
 */

abstract class phpMorphy_AncodesResolver_Decorator implements phpMorphy_AncodesResolver_AncodesResolverInterface, phpMorphy_DecoratorInterface {
    /** @var phpMorphy_AncodesResolver_AncodesResolverInterface */
    private $object;
    /** @var Closure|null */
    private $on_instantiate;
    
    /**
     * @param $object phpMorphy_AncodesResolver_AncodesResolverInterface
     */
    function __construct(phpMorphy_AncodesResolver_AncodesResolverInterface $object) {
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
     * @param $object phpMorphy_AncodesResolver_AncodesResolverInterface
     * @return phpMorphy_AncodesResolver_Decorator
     */
    protected function setDecorateeObject(phpMorphy_AncodesResolver_AncodesResolverInterface $object) {
        $this->object = $object;
        return $this;
    }
    
    /**
     * @return phpMorphy_AncodesResolver_AncodesResolverInterface
     */
    public function getDecorateeObject() {
        return $this->object;
    }
    
    /**
     * @param string $class
     * @param array $ctorArgs
     * @return phpMorphy_AncodesResolver_AncodesResolverInterface
     */
    static protected function instantiateClass($class, $ctorArgs) {
        $ref = new ReflectionClass($class);
        return $ref->newInstanceArgs($ctorArgs);
    }
    
    /**
     * @param string $propName
     * @return phpMorphy_AncodesResolver_AncodesResolverInterface
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
     * Must return instance of 'phpMorphy_AncodesResolver_AncodesResolverInterface'
     * @abstract
     * @return object
     */
    protected function proxyInstantiate() {
        if(!isset($this->on_instantiate)) {
            throw new phpMorphy_Exception('You must implement phpMorphy_AncodesResolver_Decorator::proxyInstantiate or pass \$onInstantiate to actAsProxy() method');
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
    * @param int|string $ancodeId
    * @return string
    */
    public function resolve($ancodeId) {
        $result = $this->object->resolve($ancodeId);
        return $result === $this->object ? $this : $result;
    }

    /**
    * @abstract
    * @param string $ancode
    * @return string
    */
    public function unresolve($ancode) {
        $result = $this->object->unresolve($ancode);
        return $result === $this->object ? $this : $result;
    }

}
