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
 * @decorator-decoratee-class phpMorphy_Fsa_FsaInterface
 * @decorator-decorator-class phpMorphy_Fsa_Decorator
 */

abstract class phpMorphy_Fsa_Decorator implements phpMorphy_Fsa_FsaInterface, phpMorphy_DecoratorInterface {
    /** @var phpMorphy_Fsa_FsaInterface */
    private $object;
    /** @var Closure|null */
    private $on_instantiate;
    
    /**
     * @param $object phpMorphy_Fsa_FsaInterface
     */
    function __construct(phpMorphy_Fsa_FsaInterface $object) {
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
     * @param $object phpMorphy_Fsa_FsaInterface
     * @return phpMorphy_Fsa_Decorator
     */
    protected function setDecorateeObject(phpMorphy_Fsa_FsaInterface $object) {
        $this->object = $object;
        return $this;
    }
    
    /**
     * @return phpMorphy_Fsa_FsaInterface
     */
    public function getDecorateeObject() {
        return $this->object;
    }
    
    /**
     * @param string $class
     * @param array $ctorArgs
     * @return phpMorphy_Fsa_FsaInterface
     */
    static protected function instantiateClass($class, $ctorArgs) {
        $ref = new ReflectionClass($class);
        return $ref->newInstanceArgs($ctorArgs);
    }
    
    /**
     * @param string $propName
     * @return phpMorphy_Fsa_FsaInterface
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
     * Must return instance of 'phpMorphy_Fsa_FsaInterface'
     * @abstract
     * @return object
     */
    protected function proxyInstantiate() {
        if(!isset($this->on_instantiate)) {
            throw new phpMorphy_Exception('You must implement phpMorphy_Fsa_Decorator::proxyInstantiate or pass \$onInstantiate to actAsProxy() method');
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
    * Return root transition of fsa
    * @return int
    */
    public function getRootTrans() {
        $result = $this->object->getRootTrans();
        return $result === $this->object ? $this : $result;
    }

    /**
    * Returns root state object
    * @return phpMorphy_Fsa_State
    */
    public function getRootState() {
        $result = $this->object->getRootState();
        return $result === $this->object ? $this : $result;
    }

    /**
    * Returns alphabet i.e. all chars used in automat
    * @return string[]
    */
    public function getAlphabet() {
        $result = $this->object->getAlphabet();
        return $result === $this->object ? $this : $result;
    }

    /**
    * Return annotation for given transition(if annotation flag is set for given trans)
    *
    * @param array $trans
    * @return string|null
    */
    public function getAnnot($trans) {
        $result = $this->object->getAnnot($trans);
        return $result === $this->object ? $this : $result;
    }

    /**
    * Find word in automat
    *
    * @param int $trans starting transition
    * @param string $word
    * @param bool $readAnnot read annot or simple check if word exists in automat
    * @return bool TRUE if word is found, FALSE otherwise
    */
    public function walk($trans, $word, $readAnnot = true) {
        $result = $this->object->walk($trans, $word, $readAnnot);
        return $result === $this->object ? $this : $result;
    }

    /**
    * Traverse automat and collect words
    * For each found words $callback function invoked with follow arguments:
    * call_user_func($callback, $word, $annot)
    * when $readAnnot is FALSE then $annot arg is always NULL
    *
    * @param int $startNode
    * @param mixed $callback callback function(in php format callback i.e. string or array(obj, method) or array(class, method)
    * @param bool $readAnnot read annot
    * @param string $path string to be append to all words
    */
    public function collect($startNode, $callback, $readAnnot = true, $path = '') {
        $result = $this->object->collect($startNode, $callback, $readAnnot, $path);
        return $result === $this->object ? $this : $result;
    }

    /**
    * Read state at given index
    *
    * @param int $index
    * @return array
    */
    public function readState($index) {
        $result = $this->object->readState($index);
        return $result === $this->object ? $this : $result;
    }

    /**
    * Unpack transition from binary form to array
    *
    * @param string|string[] $rawTranses may be array for convert more than one transitions
    * @return array
    */
    public function unpackTranses($rawTranses) {
        $result = $this->object->unpackTranses($rawTranses);
        return $result === $this->object ? $this : $result;
    }

}
