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



class phpMorphy_Paradigm_Formatter {
    const COMMON_PREFIX_SEPARATOR = '|';
    const PREFIX_SEPARATOR = '<';
    const SUFFIX_SEPARATOR = '>';
    const COMMON_GRAMMEMS_SEPARATOR = '|';

    static function create() {
        return new phpMorphy_Paradigm_Formatter();
    }

    function format(phpMorphy_Paradigm_ParadigmInterface $paradigm, $indent = '') {
        ob_start();

        try {
            $form_no = 1;
            foreach($paradigm as $word_form) {
                echo $indent, sprintf("%3d. ", $form_no++);
                $this->printWordForm($word_form);
                echo PHP_EOL;
            }
        } catch (Exception $e) {
            ob_end_clean();
            throw $e;
        }

        return ob_get_clean();
    }

    function printWordForm(phpMorphy_WordForm_WordFormInterface $form) {
        echo
            $form->getWord() , ' [',
            $form->getCommonPrefix() , self::COMMON_PREFIX_SEPARATOR,
            $form->getPrefix() , self::PREFIX_SEPARATOR,
            $form->getBase() , self::SUFFIX_SEPARATOR,
            $form->getSuffix(),
            '] (',
            $form->getPartOfSpeech() , ' ',
            implode(',', $form->getCommonGrammems()) , self::COMMON_GRAMMEMS_SEPARATOR,
            implode(',', $form->getFormGrammems()),
            ')';
    }
}