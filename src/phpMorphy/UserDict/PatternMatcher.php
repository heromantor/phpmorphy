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


class phpMorphy_UserDict_PatternMatcher {
    /**
     * @param phpMorphy_Paradigm_ParadigmInterface[] $paradigms
     * @param phpMorphy_UserDict_Pattern $pattern
     * @param bool $isMatchOnlyLemmas
     * @param void &$formIndex
     * @return phpMorphy_WordForm_WordFormInterface
     */
    function findSuitableFormByPattern(
        $paradigms,
        phpMorphy_UserDict_Pattern $pattern,
        $isMatchOnlyLemmas,
        &$formIndex = null
    ) {
        list($suitable_forms, $forms_idx) = $this->findSuitableFormsByPattern(
            $paradigms,
            $pattern,
            $isMatchOnlyLemmas
        );

        $forms_count = count($suitable_forms);

        if($forms_count > 1) {
            throw new phpMorphy_UserDict_PatternMatcher_AmbiguityException($pattern, $suitable_forms);
        }

        if($forms_count == 1) {
            $formIndex = $forms_idx[0];
            return $suitable_forms[0];
        } else {
            return false;
        }
    }

    /**
     * @param phpMorphy_Paradigm_ParadigmInterface[] $paradigmsCollection
     * @param phpMorphy_UserDict_Pattern $pattern
     * @param bool $isMatchOnlyLemmas
     * @return array(0 => phpMorphy_WordForm_WordFormAbstract[], 1 => int[])
     */
    function findSuitableFormsByPattern(
        $paradigmsCollection,
        phpMorphy_UserDict_Pattern $pattern,
        $isMatchOnlyLemmas
    ) {
        $result = array();
        $forms_idx = array();

        foreach($paradigmsCollection as $paradigm) {
            if($isMatchOnlyLemmas) {
                $lemma = $paradigm->getLemma();

                if($pattern->matchWord($lemma)) {
                    $word_form = $paradigm->getWordForm(0);

                    $match_result = $pattern->match(
                        $word_form->getWord(),
                        $word_form->getPartOfSpeech(),
                        $word_form->getGrammems()
                    );

                    if($match_result) {
                        $result[] = $word_form;
                        $forms_idx[] = 0;
                    }
                }
            } else {
                $form_no = 0;
                foreach($paradigm as $word_form) {
                    $match_result = $pattern->match(
                        $word_form->getWord(),
                        $word_form->getPartOfSpeech(),
                        $word_form->getGrammems()
                    );

                    if($match_result) {
                        $result[] = $word_form;
                        $forms_idx[] = $form_no;
                    }

                    $form_no++;
                }
            }
        }

        return array($result, $forms_idx);
    }
}