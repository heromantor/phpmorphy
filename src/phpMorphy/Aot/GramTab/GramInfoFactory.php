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

class phpMorphy_Aot_GramTab_GramInfoFactory {
    const GRAMMEMS_SEPARATOR = ',';
    const UNKNOWN_PART_OF_SPEECH_TAG = '*';
    
    /** @var phpMorphy_Aot_GramTab_GramInfoHelperInterface */
    protected $helper;

    /**
     * @param phpMorphy_Aot_GramTab_GramInfoHelperInterface $helper
     */
    function __construct(phpMorphy_Aot_GramTab_GramInfoHelperInterface $helper) {
        $this->helper = $helper;
    }

    /**
     * @param string $partOfSpeech
     * @param string $grammems
     * @param string $ancode
     * @return phpMorphy_Aot_GramTab_GramInfo
     */
    function create($partOfSpeech, $grammems, $ancode) {
        $grammems = $this->parseGrammems($grammems);
        $partOfSpeech = $this->helper->convertPartOfSpeech($partOfSpeech, $grammems);

        return new phpMorphy_Aot_GramTab_GramInfo(
            $this->parsePartOfSpeech($partOfSpeech),
            $grammems,
            $ancode,
            $this->helper->isPartOfSpeechProductive($partOfSpeech)
        );
    }

    /**
     * @param string $partOfSpeech
     * @return string|null
     */
    protected function parsePartOfSpeech($partOfSpeech) {
        return $partOfSpeech === self::UNKNOWN_PART_OF_SPEECH_TAG ? null : $partOfSpeech;
    }
    
    /**
     * @param string $grammems
     * @return string[]
     */
    protected function parseGrammems($grammems) {
        $default = mb_internal_encoding();
        mb_internal_encoding('utf-8');

        $grammems = array_map(
            'mb_strtolower',
            array_unique(
                array_values(
                    array_filter(
                        array_map(
                            'trim',
                            explode(self::GRAMMEMS_SEPARATOR, $grammems)
                        ),
                        'strlen'
                    )
                )
            )
        );

        mb_internal_encoding($default);

        return $grammems;
    }
}