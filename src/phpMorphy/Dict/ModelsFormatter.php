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

class phpMorphy_Dict_ModelsFormatter {
    /**
     * @static
     * @return phpMorphy_Dict_ModelsFormatter
     */
    static function create() {
        static $instance;

        if(is_null($instance)) {
            $instance = new phpMorphy_Dict_ModelsFormatter();
        }

        return $instance; 
    }

    /**
     * @param phpMorphy_Dict_PrefixSet $set
     * @return string
     */
    function formatPrefixSet(phpMorphy_Dict_PrefixSet $set) {
        return $this->formatSimpleModel(
            'PrefixSet',
            array(
                 'id' => $set->getId(),
                 'prefixes' => implode(', ', $set->getPrefixes())
            )
        );
    }

    /**
     * @param phpMorphy_Dict_Lemma $lemma
     * @return string
     */
    function formatLemma(phpMorphy_Dict_Lemma $lemma) {
        return $this->formatSimpleModel(
            'Lemma',
            array(
                 'id' => $lemma->getId(),
                 'base' => $lemma->getBase(),
                 'flexia_id' => $lemma->getFlexiaId(),
                 'common_ancode_id' => $lemma->hasAncodeId() ? $lemma->getAncodeId() : null,
                 'common_prefix_id' => $lemma->hasPrefixId() ? $lemma->getPrefixId() : null,
                 'accent_id' => $lemma->getAccentId()
            )
        );
    }

    /**
     * @param phpMorphy_Dict_PartOfSpeech $pos
     * @return string
     */
    function formatPartOfSpeech(phpMorphy_Dict_PartOfSpeech $pos) {
        return $this->formatSimpleModel(
            'PartOfSpeech',
            array(
                 'id' => $pos->getId(),
                 'name' => $pos->getName(),
                 'is_predict' => $pos->isPredict()
            )
        );
    }

    /**
     * @param phpMorphy_Dict_Grammem $grammem
     * @return string
     */
    function formatGrammem(phpMorphy_Dict_Grammem $grammem) {
        return $this->formatSimpleModel(
            'Grammem',
            array(
                 'id' => $grammem->getId(),
                 'name' => $grammem->getName(),
                 'shift' => $grammem->getShift()
            )
        );
    }

    /**
     * @param phpMorphy_Dict_FlexiaModel $model
     * @return string
     */
    function formatFlexiaModel(phpMorphy_Dict_FlexiaModel $model) {
        $flexias = array();
        $flexia_indent = '  ';
        foreach($model as $flexia) {
            $flexias[] = $flexia_indent . $this->formatFlexia($flexia);
        }

        return
            'FlexiaModel(id = ' . $this->formatValue($model->getId()) . ') {' .
            implode(PHP_EOL, $flexias) . PHP_EOL . '}';
    }

    /**
     * @param phpMorphy_Dict_Flexia $flexia
     * @return string
     */
    function formatFlexia(phpMorphy_Dict_Flexia $flexia) {
        return $this->formatSimpleModel(
            'Flexia',
            array(
                'prefix' => $flexia->getPrefix(),
                'suffix' => $flexia->getSuffix(),
                'ancode_id' => $flexia->getAncodeId()
            )
        );
    }

    /**
     * @param phpMorphy_Dict_Ancode $ancode
     * @return string
     */
    function formatAncode(phpMorphy_Dict_Ancode $ancode) {
        return $this->formatSimpleModel(
            'Ancode',
            array(
                 'id' => $ancode->getId(),
                 'part_of_speech' => $ancode->getPartOfSpeech(),
                 'grammems' => implode(', ', $ancode->getGrammems())
            )
        );
    }

    /**
     * @param phpMorphy_Dict_AccentModel $model
     * @return string
     */
    function formatAccentModel(phpMorphy_Dict_AccentModel $model) {
        $this->formatSimpleModel(
            'AccentModel',
            array(
                 'id' => $model->getId(),
                 'accents' => implode(', ', $model->getAccents())
            )
        );
    }

    /**
     * @param mixed $value
     * @return string
     */
    protected function formatValue($value) {
        if(is_null($value)) {
            return 'null';
        }

        if(is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if(is_string($value)) {
            return "'$value'";
        }

        return (string)$value;
    }

    /**
     * @param string $name
     * @param array $values
     * @param string $separator
     * @return string
     */
    protected function formatSimpleModel($name, $values, $separator = ', ') {
        $result = $name . '(';

        foreach($values as $key => &$value) {
            $value = "$key = " . $this->formatValue($value);
        }

        $result .= implode($separator, $values) . ')';

        return $result;
    }
}