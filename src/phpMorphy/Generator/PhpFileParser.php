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


class phpMorphy_Generator_PhpFileParser {
    /** @var string */
    private $namespace;
    /** @var array */
    private $tokens;
    /** @var int */
    private $token_pos;
    /** @var int */
    private $offset;
    /** @var int */
    private $line;

    private function __construct() {
    }

    /**
     * @static
     * @param string $fileName
     * @return phpMorphy_Generator_PhpFileParser_FileDescriptor
     */
    static function parseFile($fileName) {
        return self::parseString(file_get_contents($fileName));
    }

    /**
     * @static
     * @param string $string
     * @return phpMorphy_Generator_PhpFileParser_FileDescriptor
     */
    static function parseString($string) {
        return self::parseArray(token_get_all($string));
    }

    /**
     * @static
     * @param array $tokens
     * @return phpMorphy_Generator_PhpFileParser_FileDescriptor
     */
    static function parseArray(array $tokens) {
        $that = new phpMorphy_Generator_PhpFileParser;

        $that->tokens = $tokens;
        $file_descriptor = new phpMorphy_Generator_PhpFileParser_FileDescriptor();

        $that->doParse($file_descriptor);

        $file_descriptor->finalize();

        return $file_descriptor;
    }

    private function tokenName($token) {
        return is_string($token) ? $token : token_name($token[0]);
    }

    /**
     * Parses phpMorphy_Generator_PhpFileParser_phpDoc for namespace or class|interface
     * @param phpMorphy_Generator_PhpFileParser_phpDoc $doc
     * @return array|phpMorphy_Generator_PhpFileParser_ClassDescriptor|string
     */
    private function parsePhpDoc(phpMorphy_Generator_PhpFileParser_phpDoc $doc) {
        $start_token = $this->tokens[$this->token_pos];
        $tokens = $this->tokens;

        for($this->token_pos++, $c = count($tokens); $this->token_pos < $c; $this->token_pos++) {
            $token = $tokens[$this->token_pos];

            switch($token[0]) {
                case T_NAMESPACE:
                    $this->parseNamespace($doc);
                    return array();
                case T_ABSTRACT:
                case T_FINAL:
                case T_CLASS:
                case T_INTERFACE:
                    return array($this->parseClass($doc));
                case T_COMMENT:
                case T_WHITESPACE:
                    break;
                default:
                    return array();
            }
        }
    }

    /**
     * Parses namespace definition
     * @throws phpMorphy_Generator_PhpFileParser_Exception
     * @param null|phpMorphy_Generator_PhpFileParser_phpDoc $doc
     * @return string
     */
    private function parseNamespace(phpMorphy_Generator_PhpFileParser_phpDoc $doc = null) {
        $start_token = $this->tokens[$this->token_pos];
        $tokens = $this->tokens;

        for($this->token_pos++, $c = count($tokens); $this->token_pos < $c; $this->token_pos++) {
            $token = $tokens[$this->token_pos];
            $lexem = is_string($token) ? $token : $token[0];

            switch($lexem) {
                case T_STRING:
                    $this->namespace = $token[1];
                    return $token[1];
                case T_WHITESPACE:
                case T_COMMENT:
                case T_DOC_COMMENT:
                    break;
                default:
                    throw new phpMorphy_Generator_PhpFileParser_Exception("Unexpected token '" . $this->tokenName($token) . "' in namespace definition", $start_token);
            }
        }

        throw new phpMorphy_Generator_PhpFileParser_Exception("Unexpected end of file in namespace definition", $start_token);
    }

    /**
     * Parses class definition
     * @throws phpMorphy_Generator_PhpFileParser_Exception
     * @param null|phpMorphy_Generator_PhpFileParser_phpDoc $doc
     * @param null|string $namespace
     * @return phpMorphy_Generator_PhpFileParser_ClassDescriptor
     */
    private function parseClass(phpMorphy_Generator_PhpFileParser_phpDoc $doc = null) {
        $start_token = $this->tokens[$this->token_pos];
        $tokens = $this->tokens;

        $result = new phpMorphy_Generator_PhpFileParser_ClassDescriptor();
        $result->startLine = $start_token[2];
        $result->phpDoc = $doc;
        $result->namespace = $this->namespace;

        for($c = count($tokens); $this->token_pos < $c; $this->token_pos++) {
            $token = $tokens[$this->token_pos];
            $lexem = is_string($token) ? $token : $token[0];

            switch($lexem) {
                case T_CLASS:
                    $result->type = phpMorphy_Generator_PhpFileParser_ClassDescriptor::IS_CLASS;
                    break;
                case T_INTERFACE:
                    $result->type = phpMorphy_Generator_PhpFileParser_ClassDescriptor::IS_INTERFACE;
                    break;
                case T_STRING:
                    if(null === $result->type) {
                        throw new phpMorphy_Generator_PhpFileParser_Exception("Unexpected token '" . $this->tokenName($token) . "' in class definition", $start_token);
                    }

                    if(null === $result->name) {
                        $result->name = $token[1];
                    }
                    break;
                case T_CURLY_OPEN: // ???
                case '{':
                    $this->parseOpenCurlyBracket();
                    $this->token_pos++;

                    if(false === ($end_line = $this->findFirstTokenLine(1))) {
                        $this->token_pos--;
                        if(false === ($end_line = $this->findFirstTokenLine(-1))) {
                            throw new phpMorphy_Generator_PhpFileParser_Exception("Can`t find end line for class", $start_token);
                        }
                    }

                    $result->endLine = $end_line;

                    return $result;
                case T_DOC_COMMENT:
                case T_COMMENT:
                case T_WHITESPACE:
                case T_ABSTRACT:
                case T_FINAL:
                case T_EXTENDS:
                case T_IMPLEMENTS:
                case ',':
                    break;
                default:
                    throw new phpMorphy_Generator_PhpFileParser_Exception("Unexpected token '" . $this->tokenName($token) . "' in class definition", $start_token);
            }
        }

        throw new phpMorphy_Generator_PhpFileParser_Exception("Unexpected end of file in class definition", $start_token);
    }

    /**
     * @param int $direction
     * @return int|false
     */
    private function findFirstTokenLine($direction) {
        $pos = $this->token_pos;

        for($c = count($this->tokens); $pos < $c && $pos >= 0; $pos += $direction) {
            if(is_array($this->tokens[$pos])) {
                $token =  $this->tokens[$pos];

                return $direction > 0 ? $token[2] : $token[2] + substr_count($token[1], "\n");
            }
        }

        return false;
    }

    /**
     * Seeks to pair close curly bracket
     * @throws phpMorphy_Generator_PhpFileParser_Exception
     * @return void
     */
    private function parseOpenCurlyBracket() {
        $opens = 1;
        $start_token = $this->tokens[$this->token_pos];
        $tokens = $this->tokens;

        for($this->token_pos++, $c = count($tokens); $this->token_pos < $c; $this->token_pos++) {
            $token = $tokens[$this->token_pos];
            $lexem = is_string($token) ? $token : $token[0];

            switch($lexem) {
                case T_CURLY_OPEN:
                case '{':
                    $opens++;
                    break;
                case '}':
                    $opens--;

                    if(0 === $opens) {
                        return;
                    }

                    break;
            }
        }

        throw new phpMorphy_Generator_PhpFileParser_Exception("Unexpected end of file, expect close curly bracket", $start_token);
    }

    private function doParse(phpMorphy_Generator_PhpFileParser_FileDescriptor $file_descriptor) {
        $processed = 0;
        $tokens = $this->tokens;

        $this->token_pos = 0;
        $this->offset = 0;
        $this->line = 1;

        for($c = count($tokens); $this->token_pos < $c; $this->token_pos++) {
            $token = $tokens[$this->token_pos];
            $lexem = is_string($token) ? $token : $token[0];

            switch($lexem) {
                case T_DOC_COMMENT:
                    if($processed === 0) {
                        $pos = $this->token_pos + 1;
                        if($pos < $c) {
                            $next_token = $tokens[$pos];

                            if(is_array($next_token) &&
                               T_WHITESPACE === $next_token[0] &&
                               substr_count($next_token[1], "\n") > 1
                            ) {
                                $file_descriptor->phpDoc = new phpMorphy_Generator_PhpFileParser_phpDoc($token);
                                break;
                            }
                        }
                    }

                    $file_descriptor->classes = array_merge(
                        $file_descriptor->classes,
                        $this->parsePhpDoc(new phpMorphy_Generator_PhpFileParser_phpDoc($token))
                    );
                    break;
                case T_NAMESPACE:
                    $this->parseNamespace();
                    break;
                case T_CLASS:
                case T_INTERFACE:
                case T_ABSTRACT:
                case T_FINAL:
                    $file_descriptor->classes[] = $this->parseClass();
                    break;
                case T_WHITESPACE:
                case T_COMMENT:
                case T_OPEN_TAG:
                    $processed--;
                    break;
            }

            $processed++;
        }
    }
}