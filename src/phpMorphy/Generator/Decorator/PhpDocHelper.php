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

class phpMorphy_Generator_Decorator_PhpDocHelper {
    /**
     * @static
     * @param string $decorateeClass
     * @param string $decoratorClass
     * @param bool $isAutoRegenerate
     * @return string
     */
    static function generateHeaderPhpDoc(
        $decorateeClass,
        $decoratorClass,
        $isAutoRegenerate = true
    ) {
        $isAutoRegenerate = $isAutoRegenerate ? 'TRUE' : 'FALSE';

        $text = '/**' . PHP_EOL .
                ' * @decorator-auto-regenerate ' . $isAutoRegenerate . PHP_EOL .
                ' * @decorator-generated-at ' . date('r') . PHP_EOL .
                ' * @decorator-decoratee-class ' . $decorateeClass . PHP_EOL .
                ' * @decorator-decorator-class ' . $decoratorClass . PHP_EOL .
                ' */';

        return $text;
    }

    /**
     * @static
     * @param string $phpCodeString
     * @return phpMorphy_Generator_Decorator_PhpDocHelperHeader
     */
    static function parseHeaderPhpDoc($phpCodeString) {
        $phpCodeString = ltrim($phpCodeString);
        if(!preg_match('/^<\?php/', $phpCodeString)) {
            $phpCodeString = '<' . '?php' . PHP_EOL . $phpCodeString;
        }

        $tokens = token_get_all($phpCodeString);
        $first_doc_comment = false;

        foreach($tokens as $token) {
            if(is_array($token) && T_DOC_COMMENT == $token[0]) {
                $first_doc_comment = (string)$token[1];
                break;
            }
        }

        return
            phpMorphy_Generator_Decorator_PhpDocHelperHeader::constructFromString(
                $first_doc_comment
            );
    }
}