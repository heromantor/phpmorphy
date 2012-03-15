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

class phpMorphy_Generator_Decorator_HandlerDefault implements
    phpMorphy_Generator_Decorator_HandlerInterface
{
    const INDENT_SIZE = 4;
    const INDENT_CHAR = ' ';

    /**
     * @return string
     */
    function generateHeaderDocComment($decorateeClass, $decoratorClass) {
        return $this->indentText(
            phpMorphy_Generator_Decorator_PhpDocHelper::generateHeaderPhpDoc(
                $decorateeClass,
                $decoratorClass,
                true
            ),
            0
        );
    }

    /**
     * @param string $docComment
     * @param string $class
     * @param string[]|null $extends
     * @param string[]|null $implements
     * @return string
     */
    function generateClassDeclaration($docComment, $class, $extends, $implements) {
        $docComment = $this->unindentText($docComment);

        $text = $docComment . PHP_EOL . 'abstract class ' . $class;

        $implements = false === $implements ? array() : $implements;
        $implements[] = 'phpMorphy_DecoratorInterface';

        if(null !== $extends) {
            $text .= ' extends ' . implode(', ', $extends);
        }

        if(null !== $implements) {
            $text .= ' implements ' . implode(', ', $implements);
        }

        return $this->indentText($text, 0);
    }

    /**
     * @param string $decoratorClass
     * @param string $decorateeClass
     * @return string
     */
    function generateCommonMethods($decoratorClass, $decorateeClass) {
        $ctor = <<<EOF
/** @var $decorateeClass */
private \$object;
/** @var Closure|null */
private \$on_instantiate;

/**
 * @param \$object $decorateeClass
 */
function __construct($decorateeClass \$object) {
    \$this->setDecorateeObject(\$object);
}

/**
 * Set current decorator behaviour to proxy model
 * @param Closure|null \$onInstantiate
 */
protected function actAsProxy(/*TODO: uncomment for php >= 5.3 Closure */\$onInstantiate = null) {
    unset(\$this->object);
    \$this->on_instantiate = \$onInstantiate;
}

/**
 * @param \$object $decorateeClass
 * @return $decoratorClass
 */
protected function setDecorateeObject($decorateeClass \$object) {
    \$this->object = \$object;
    return \$this;
}

/**
 * @return $decorateeClass
 */
public function getDecorateeObject() {
    return \$this->object;
}

/**
 * @param string \$class
 * @param array \$ctorArgs
 * @return $decorateeClass
 */
static protected function instantiateClass(\$class, \$ctorArgs) {
    \$ref = new ReflectionClass(\$class);
    return \$ref->newInstanceArgs(\$ctorArgs);
}

/**
 * @param string \$propName
 * @return $decorateeClass
 */
public function __get(\$propName) {
    if('object' === \$propName) {
        \$obj = \$this->proxyInstantiate();
        \$this->setDecorateeObject(\$obj);
        return \$obj;
    }

    throw new phpMorphy_Exception("Unknown property '\$propName'");
}

/**
 * This method invoked by __get() at first time access to proxy object
 * Must return instance of '$decorateeClass'
 * @abstract
 * @return object
 */
protected function proxyInstantiate() {
    if(!isset(\$this->on_instantiate)) {
        throw new phpMorphy_Exception('You must implement $decoratorClass::proxyInstantiate or pass \\\$onInstantiate to actAsProxy() method');
    }

    \$fn = \$this->on_instantiate;
    unset(\$this->on_instantiate);

    return \$fn();
}

/**
 * Implement deep copy paradigm
 */
function __clone() {
    if(isset(\$this->object)) {
        \$this->object = clone \$this->object;
    }
}
EOF;

        return $this->indentText($ctor, 1);
    }

    /**
     * @param string $docComment
     * @param string $modifiers
     * @param bool $isReturnRef
     * @param string $name
     * @param string $args
     * @param string $passArgs
     * @return string
     */
    function generateMethod($docComment, $modifiers, $isReturnRef, $name, $args, $passArgs) {
        $ref = $isReturnRef ? '&' : '';
        $docComment = $this->unindentText($docComment);
        $args = $this->unindentText($args);

        $text = <<<EOF
$docComment
$modifiers function {$ref}$name($args) {
    \$result = \$this->object->$name($passArgs);
    return \$result === \$this->object ? \$this : \$result;
}
EOF;

        return $this->indentText($text, 1);
    }

    /**
     * @param string $text
     * @param int $level
     * @return string
     */
    protected function indentText($text, $level) {
        $indent = str_repeat(self::INDENT_CHAR, self::INDENT_SIZE * $level);

        return implode(
            PHP_EOL,
            array_map(
                function ($line) use ($indent) {
                    return $indent . rtrim($line);
                },
                explode(PHP_EOL, ltrim($text))
            )
        );
    }


    /**
     * @param string $text
     * @return string
     */
    protected function unindentText($text) {
        $lines = array_values(
            array_filter(
                array_map(
                    'rtrim',
                    explode("\n", $text)
                ),
                'strlen'
            )
        );

        $min_indent_length = $this->getMinIndentLength($lines);

        if(count($lines) > 1) {
            // try min indent without first line
            $old_first_line = array_shift($lines);
            $min_indent_length_new = $this->getMinIndentLength($lines);

            if($min_indent_length_new > $min_indent_length) {
                array_unshift(
                    $lines,
                    str_repeat(' ', $min_indent_length_new) . ltrim($old_first_line)
                );
                $min_indent_length = $min_indent_length_new;
            }
        }

        foreach($lines as &$line) {
            $line = substr($line, $min_indent_length);
        }

        return implode(PHP_EOL, $lines);
    }

    protected function getMinIndentLength(array $lines) {
        $min_indent_length = PHP_INT_MAX;
        foreach($lines as $line) {
            $diff = strlen($line) - strlen(ltrim($line));
            $min_indent_length = min($diff, $min_indent_length);
        }

        return $min_indent_length;
    }
}
