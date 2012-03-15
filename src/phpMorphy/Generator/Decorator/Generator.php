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

class phpMorphy_Generator_Decorator_Generator {
    /**
     * @static
     * @param string $decorateeClass
     * @return string
     */
    static function getDecoratorClassName($decorateeClass) {
        $decorateeClass = preg_replace('~([^_]+)_(\1)(Interface|Abstract)?$~', '\1_', $decorateeClass);

        return $decorateeClass . 'Decorator';
    }

    /**
     * @static
     * @throws phpMorphy_Exception
     * @param string $decorateeClass
     * @param string|null $decoratorClass
     * @param null|phpMorphy_Generator_Decorator_HandlerInterface $handler
     * @return string
     */
    static function generate(
        $decorateeClass,
        $decoratorClass = null,
        phpMorphy_Generator_Decorator_HandlerInterface $handler = null
    ) {
        if(!class_exists($decorateeClass) && !interface_exists($decorateeClass)) {
            throw new phpMorphy_Exception("Class '$decorateeClass' not found");
        }

        if(null === $decoratorClass) {
            $decoratorClass = self::getDecoratorClassName($decorateeClass);
        }

        $helper = new phpMorphy_Generator_ReflectionHelper();
        if(null === $handler) {
            $handler = new phpMorphy_Generator_Decorator_HandlerDefault();
        }

        $classref = new ReflectionClass($decorateeClass);

        // generateHeaderPhpDoc header
        $buffer = $handler->generateHeaderDocComment($decorateeClass, $decoratorClass) .
                  PHP_EOL . PHP_EOL;

        $parents = null;
        $interfaces = null;
        if($classref->isInterface()) {
            $interfaces = array($decorateeClass);
        } else {
            $parents = array($decorateeClass);
        }

        // generateHeaderPhpDoc class declaration
        $buffer .= $handler->generateClassDeclaration(
            $classref->getDocComment(),
            $decoratorClass,
            $parents,
            $interfaces
        );

        // generateHeaderPhpDoc common methods
        $buffer .=
            ' {' . PHP_EOL .
            $handler->generateCommonMethods($decoratorClass, $decorateeClass) .
            PHP_EOL . PHP_EOL;

        // generateHeaderPhpDoc wrapped methods
        foreach($helper->getOverridableMethods($classref) as $method) {
            $buffer .= $handler->generateMethod(
                $method->getDocComment(),
                $helper->generateMethodModifiers($method, ReflectionMethod::IS_ABSTRACT),
                $method->returnsReference(),
                $method->getName(),
                $helper->generateMethodArguments($method),
                $helper->generateArgsPass($method)
            ) . PHP_EOL . PHP_EOL;
        }

        $buffer .= '}' . PHP_EOL;

        return $buffer;
    }
}
