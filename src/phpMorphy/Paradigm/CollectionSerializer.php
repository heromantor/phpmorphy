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

class phpMorphy_Paradigm_CollectionSerializer {
    /**
     *
     * @param phpMorphy_Paradigm_ParadigmInterface[] $collection
     * @param bool $asText
     * @return string
     */
    function serialize($collection, $asText) {
        $result = array();

        foreach($collection as $paradigm) {
            $result[] = $this->processParadigm($paradigm, $asText);
        }

        return $result;
    }

    /**
     * @param phpMorphy_Paradigm_ParadigmInterface $paradigm
     * @param bool $asText
     * @return array
     */
    protected function processParadigm(phpMorphy_Paradigm_ParadigmInterface $paradigm, $asText) {
        $forms = array();
        $all = array();

        foreach($paradigm as $word_form) {
            $forms[] = $word_form->getWord();
            $all[] = $this->serializeGramInfo($word_form, $asText);
        }

        return array(
            'forms' => $forms,
            'all' => $all,
            'common' => '',
        );
    }

    /**
     * @param phpMorphy_WordForm_WithFormNo $wordForm
     * @param bool $asText
     * @return array|string
     */
    protected function serializeGramInfo(phpMorphy_WordForm_WithFormNo $wordForm, $asText) {
        if($asText) {
            return $wordForm->getPartOfSpeech() . ' ' . implode(',', $wordForm->getGrammems());
        } else {
            return array(
                'pos' => $wordForm->getPartOfSpeech(),
                'grammems' => $wordForm->getGrammems()
            );
        }
    }
}