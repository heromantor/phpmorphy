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



class phpMorphy_UserDict_Log_CLI implements phpMorphy_UserDict_LogInterface {
    protected
        /* @var bool */
        $is_verbose,
        /* @var phpMorphy_UserDict_EncodingConverter */
        $encoding_converter,
        /* @var string */
        $current_lexem,
        /* @var phpMorphy_UserDict_Log_ErrorsHandlerInterface */
        $errors_handler,
        /** @var resource */
        $output_file
        ;

    function __construct(
        $isVerbose,
        phpMorphy_UserDict_Log_ErrorsHandlerInterface $errorsHandler,
        phpMorphy_UserDict_EncodingConverter $encodingConverter
    ) {
        $this->is_verbose = (bool)$isVerbose;
        $this->errors_handler = $errorsHandler;
        $this->encoding_converter = $encodingConverter;

        $this->output_file = STDERR;
    }

    function addLexem($lexem) {
        $lexem = $this->setCurrentLexem($lexem);
        $this->infoMessage("Add '$lexem' lexem");
    }

    function deleteLexem($lexem) {
        $lexem = $this->setCurrentLexem($lexem);
        $this->infoMessage("Delete '$lexem' lexem");
    }

    function editLexem($lexem) {
        $lexem = $this->setCurrentLexem($lexem);
        $this->infoMessage("Edit '$lexem' lexem");
    }

    protected function setCurrentLexem($lexem) {
        $this->current_lexem = $this->toInternalEncoding($lexem);
        return $this->current_lexem;
    }

    function errorAmbiguity(phpMorphy_UserDict_Pattern $pattern, $variants, $isError = true) {
        $descs = array();

        foreach($variants as $form) {
            $descs []=
                $this->toInternalEncoding(
                    $form->getParadigm()->getBaseForm() . ' [' .
                    $form->getPartOfSpeech() . ' ' . implode(',', $form->getGrammems()) . ']'
                );
        }

        $this->dispatchMessage(
            "An ambiguous word found: '$pattern', variants are: '" . implode("', '", $descs) . "'",
            $isError
        );
    }

    function errorCantDeduceForm($patternWord, $isError = true) {
        $this->dispatchMessage("Can`t deduce from '" . $this->toInternalEncoding($patternWord) . "'", $isError);
    }

    function errorPatternNotFound(phpMorphy_UserDict_Pattern $pattern, $isError = true) {
        $this->dispatchMessage("Pattern '" . $this->toInternalEncoding($pattern) . "' didn`t match anything", $isError);
    }

    protected function dispatchMessage($message, $isError) {
        $method = $isError ? 'errorMessage' : 'infoMessage';
        return $this->$method($message);
    }

    function errorMessage($message) {
        $fmt_message = "[EE] Error occurred while processing '$this->current_lexem' lexem: $message" . PHP_EOL;
        fprintf($this->output_file, $fmt_message);
        $this->errors_handler->handle($message);
    }

    function infoMessage($message) {
        if($this->is_verbose) {
            fprintf($this->output_file, "[II] $message" . PHP_EOL);
        }
    }

    protected function toInternalEncoding($string) {
        return $this->encoding_converter->toInternal($string);
    }

}