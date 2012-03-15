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
 * @decorator-decoratee-class phpMorphy_Dict_Source_SourceInterface
 * @decorator-decorator-class phpMorphy_Dict_Source_Decorator
 */

abstract class phpMorphy_Dict_Source_Decorator implements phpMorphy_Dict_Source_SourceInterface, phpMorphy_DecoratorInterface {
    /** @var phpMorphy_Dict_Source_SourceInterface */
    private $object;
    /** @var Closure|null */
    private $on_instantiate;
    
    /**
     * @param $object phpMorphy_Dict_Source_SourceInterface
     */
    function __construct(phpMorphy_Dict_Source_SourceInterface $object) {
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
     * @param $object phpMorphy_Dict_Source_SourceInterface
     * @return phpMorphy_Dict_Source_Decorator
     */
    protected function setDecorateeObject(phpMorphy_Dict_Source_SourceInterface $object) {
        $this->object = $object;
        return $this;
    }
    
    /**
     * @return phpMorphy_Dict_Source_SourceInterface
     */
    public function getDecorateeObject() {
        return $this->object;
    }
    
    /**
     * @param string $class
     * @param array $ctorArgs
     * @return phpMorphy_Dict_Source_SourceInterface
     */
    static protected function instantiateClass($class, $ctorArgs) {
        $ref = new ReflectionClass($class);
        return $ref->newInstanceArgs($ctorArgs);
    }
    
    /**
     * @param string $propName
     * @return phpMorphy_Dict_Source_SourceInterface
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
     * Must return instance of 'phpMorphy_Dict_Source_SourceInterface'
     * @abstract
     * @return object
     */
    protected function proxyInstantiate() {
        if(!isset($this->on_instantiate)) {
            throw new phpMorphy_Exception('You must implement phpMorphy_Dict_Source_Decorator::proxyInstantiate or pass \$onInstantiate to actAsProxy() method');
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
    * @return string
    */
    public function getName() {
        $result = $this->object->getName();
        return $result === $this->object ? $this : $result;
    }

    /**
    * ISO3166 country code separated by underscore(_) from ISO639 language code
    * ru_RU, uk_UA for example
    * @return string
    */
    public function getLanguage() {
        $result = $this->object->getLanguage();
        return $result === $this->object ? $this : $result;
    }

    /**
    * Any string
    * @return string
    */
    public function getDescription() {
        $result = $this->object->getDescription();
        return $result === $this->object ? $this : $result;
    }

    /**
    * @return Iterator over objects of phpMorphy_Dict_Ancode
    */
    public function getAncodes() {
        $result = $this->object->getAncodes();
        return $result === $this->object ? $this : $result;
    }

    /**
    * @return Iterator over objects of phpMorphy_Dict_FlexiaModel
    */
    public function getFlexias() {
        $result = $this->object->getFlexias();
        return $result === $this->object ? $this : $result;
    }

    /**
    * @return Iterator over objects of phpMorphy_Dict_PrefixSet
    */
    public function getPrefixes() {
        $result = $this->object->getPrefixes();
        return $result === $this->object ? $this : $result;
    }

    /**
    * @return Iterator over objects of phpMorphy_Dict_AccentModel
    */
    public function getAccents() {
        $result = $this->object->getAccents();
        return $result === $this->object ? $this : $result;
    }

    /**
    * @return Iterator over objects of phpMorphy_Dict_Lemma
    */
    public function getLemmas() {
        $result = $this->object->getLemmas();
        return $result === $this->object ? $this : $result;
    }

}
