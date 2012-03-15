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


class phpMorphy_UserDict_PatternMatcher_AmbiguityException extends phpMorphy_UserDict_PatternMatcher_BaseException {
    /** @var phpMorphy_UserDict_Pattern  */
    private $pattern;
    /** @var phpMorphy_WordForm_WordFormInterface[] */
    private $suitable_forms = array();

    function __construct(phpMorphy_UserDict_Pattern $pattern, array $suitableForms) {
        $this->pattern = $pattern;
        $this->suitable_forms = $suitableForms;

        foreach($suitableForms as $form) {
            $descs []=
                $this->toInternalEncoding(
                    $form->getParadigm()->getBaseForm() . ' [' .
                    $form->getPartOfSpeech() . ' ' . implode(',', $form->getGrammems()) . ']'
                );
        }

        parent::__construct("An ambiguous word found: '$pattern', variants are: '" . implode("', '", $descs) . "'");
    }

    /**
     * @return phpMorphy_UserDict_Pattern
     */
    public function getPattern() {
        return $this->pattern;
    }

    /**
     * @return phpMorphy_WordForm_WordFormInterface|phpMorphy_WordForm_WordFormInterface[]
     */
    public function getSuitableForms() {
        return $this->suitable_forms;
    }
}