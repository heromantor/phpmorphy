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

class phpMorphy_Generator_ReflectionHelper {
    protected $files_cache = array();

    /**
     * @param ReflectionMethod $method
     * @return string
     */
    function generateArgsPass(ReflectionMethod $method) {
        return implode(
            ', ',
            array_map(
                function (ReflectionParameter $arg) {
                    return '$' . $arg->getName();
                },
                $method->getParameters()
            )
        );
    }

    /**
     * @param ReflectionMethod $method
     * @param int $modifiersFilter
     * @return string
     */
    function generateMethodModifiers(ReflectionMethod $method, $modifiersFilter) {
        return implode(
            ' ',
            $this->modifiersToArray($method->getModifiers() & ~$modifiersFilter)
        );
    }

    /**
     * @param ReflectionMethod $method
     * @return string
     */
    function generateMethodArguments(ReflectionMethod $method) {
        if($method->isInternal()) {
            // This code can`t handle constants, it replaces its with value
            return implode(
                ', ',
                array_map(
                    array($this, 'generateMethodArgument'),
                    $method->getParameters()
                )
            );
        } else {
            $lines = $this->getFileLines($method->getFileName());

            $start_line = $method->getStartLine() - 1;
            $function_lines = array_slice(
                $lines,
                $start_line,
                $method->getEndLine() - $start_line
            );

            // todo: use code beautifier?
            return $this->parseFunctionArgs(implode(PHP_EOL, $function_lines));
        }
    }

    /**
     * @param ReflectionMethod $method
     * @param int $modifiersFilter
     * @return string
     */
    function generateMethodSignature(ReflectionMethod $method, $modifiersFilter) {
        $modifiers = $this->generateMethodModifiers($method, $modifiersFilter);

        $ref = $method->returnsReference() ? '&' : '';
        $args =  '(' . $this->generateMethodArguments($method) . ')';
        $modifiers = $modifiers !== '' ? "$modifiers " : '';

        return
            $modifiers . 'function ' . $ref . $method->getName() . $args;
    }

    /**
     * @throws phpMorphy_Exception
     * @param string $fileName
     * @return string[]
     */
    protected function getFileLines($fileName) {
        if(!isset($this->files_cache[$fileName])) {
            $lines = file($fileName);

            if(!is_array($lines)) {
                throw new phpMorphy_Exception("Can`t read '$fileName' file");
            }

            $this->files_cache[$fileName] = $lines;
        }

        return $this->files_cache[$fileName];
    }

    /**
     * @param string $functionDeclaration
     * @return string
     */
    protected function parseFunctionArgs($functionDeclaration) {
        $str = '<' . '?php' . $functionDeclaration;
        $tokens = token_get_all($str);

        $brackets = 0;
        $wait_function_bracket = false;
        $in_args_list = false;
        $buffer = '';

        foreach($tokens as $token) {
            if($in_args_list && $brackets < 1) {
                break;
            }

            if(is_array($token)) {
                if($token[0] == T_FUNCTION) {
                    $wait_function_bracket = true;
                }

                if($in_args_list) {
                    $buffer .= $token[1];
                }
            } else {
                if($token === '(') {
                    if($wait_function_bracket) {
                        $brackets = 0;
                        $wait_function_bracket = false;
                        $in_args_list = true;
                    }

                    $brackets++;
                } else if($token === ')') {
                    $brackets--;
                }

                if($in_args_list) {
                    $buffer .= $token;
                }
            }
        }

        return substr($buffer, 1, -1);
    }


    /**
     * @param ReflectionParameter $arg
     * @return string
     */
    protected function generateMethodArgument(ReflectionParameter $arg) {
        if($arg->isArray()) {
            $type_hint = 'array ';
        } else {
            $type_hint = $arg->getClass() ? $arg->getClass()->getName() . ' ' : '';
        }

        $ref = $arg->isPassedByReference() ? '&' : '';
        $default = $arg->isDefaultValueAvailable() ?
            ' = ' . $this->generateDefaultValue($arg->getDefaultValue()) :
            '';

        return $type_hint . $ref . '$' . $arg->getName() . $default;
    }

    /**
     * @param mixed $value
     * @return string
     */
    protected function generateDefaultValue($value) {
        if(null === $value) {
            return 'null';
        }

        return str_replace(
            array("\r", "\n"),
            '',
            var_export($value, true)
        );
    }

    /**
     * @param int $modifiers
     * @return string[]
     */
    protected function modifiersToArray($modifiers) {
        $result = array();

        if($modifiers & ReflectionMethod::IS_FINAL) $result[] = 'final';
        if($modifiers & ReflectionMethod::IS_STATIC) $result[] = 'static';
        if($modifiers & ReflectionMethod::IS_ABSTRACT) $result[] = 'abstract';
        if($modifiers & ReflectionMethod::IS_PRIVATE) $result[] = 'private';
        if($modifiers & ReflectionMethod::IS_PROTECTED) $result[] = 'protected';
        if($modifiers & ReflectionMethod::IS_PUBLIC) $result[] = 'public';

        return $result;
    }

    /**
     * Return parent for $refClass class, if $refClass points to interface then return false
     *
     * @param ReflectionClass $refClass
     * @return null|string[]
     */
    function getParent(ReflectionClass $refClass) {
        if($refClass->isInterface()) {
            return false;
        } else {
            $parent = $refClass->getParentClass();
            return $parent ? array($parent->getName()) : null;
        }
    }

    /**
     * Return direct implemented interfaces for given class or interface.
     * i.e. for
     *  interface A {}
     *  interface B {}
     *  interface C {}
     *  interface AB extends A, B {}
     *  class Foo implements AB, C {}
     *
     *  this function returns array('AB', 'C'), unlike
     *  ReflectionClass::getInterfaces() method which returns A, B, C, AB interfaces
     *
     * @param ReflectionClass $refClass
     * @return null|string[]
     */
    function getInterfaces(ReflectionClass $refClass) {
        $interfaces = $this->getDirectInterfaces($refClass);

        if(false === $interfaces) {
            return false;
        }

        return array_map(
            function (ReflectionClass $interface) {
                return $interface->getName();
            },
            $interfaces
        );
    }

    /**
     * @param ReflectionClass $refClass
     * @return null|ReflectionClass[]
     */
    protected function getDirectInterfaces(ReflectionClass $refClass) {
        // interface_name => child_count map
        $interfaces_map = array();

        /** @var $interface_to_check ReflectionClass */
        foreach($refClass->getInterfaces() as $parent_name => $parent) {
            if(!isset($interfaces_map[$parent_name])) {
                $interfaces_map[$parent_name] = 0;
            }

            /** @var $interface ReflectionClass */
            foreach($refClass->getInterfaces() as $child_name => $child) {
                if($parent_name !== $child_name) {
                    if($child->isSubclassOf($parent_name)) {
                        $interfaces_map[$parent_name]++;
                    }
                }
            }
        }

        $result = array();
        $all_interfaces = $refClass->getInterfaces();
        foreach($interfaces_map as $name => $child_count) {
            if($child_count == 0) {
                $result[] = $all_interfaces[$name];
            }
        }

        return count($result) ? $result : null;
    }

    /**
     * @return ReflectionMethod[]
     */
    function getOverridableMethods(ReflectionClass $refClass) {
        if($refClass->isFinal()) {
            return array();
        }

        $filter =
                ReflectionMethod::IS_PUBLIC |
                ReflectionMethod::IS_ABSTRACT;

        $result = array();

        /** @var $method ReflectionMethod */
        foreach($refClass->getMethods($filter) as $method) {
            if($this->isOverridableMethod($method)) {
               $result[] = $method;
            }
        }

        return $result;
    }

    /**
     * @param ReflectionMethod $method
     * @return bool
     */
    function isOverridableMethod(ReflectionMethod $method) {
        return !(
            $method->isStatic() ||
            $method->isFinal() ||
            !$method->isPublic() ||
            $method->isConstructor() ||
            $method->isDestructor() ||
            in_array(
                $method->getName(),
                array(
                     '__clone'
                )
            )
        );
    }
}