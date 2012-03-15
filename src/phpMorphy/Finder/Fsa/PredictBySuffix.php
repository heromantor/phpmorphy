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

class phpMorphy_Finder_Fsa_PredictBySuffix extends phpMorphy_Finder_Fsa_Finder {
    protected
        $min_suf_len,
        $unicode;

    function __construct(phpMorphy_Fsa_FsaInterface $fsa, phpMorphy_AnnotDecoder_AnnotDecoderInterface $annotDecoder, $encoding, $minimalSuffixLength = 4) {
        parent::__construct($fsa, $annotDecoder);

        $this->min_suf_len = (int)$minimalSuffixLength;
        $this->unicode = phpMorphy_UnicodeHelper_UnicodeHelperAbstract::getHelperForEncoding($encoding);
    }

    protected function doFindWord($word) {
        $word_len = $this->unicode->strlen($word);

        if(!$word_len) {
            return false;
        }

        $skip_len = 0;

        for($i = 1, $c = $word_len - $this->min_suf_len; $i < $c; $i++) {
            $first_char_size = $this->unicode->getFirstCharSize($word);
            $skip_len += $first_char_size;

            $word = $GLOBALS['__phpmorphy_substr']($word, $first_char_size);

            if(false !== ($result = parent::doFindWord($word))) {
                break;
            }
        }

        if($i < $c) {
            return $this->fixAnnots(
                $this->decodeAnnot($result, true),
                $skip_len
            );
        } else {
            return false;
        }
    }

    protected function fixAnnots($annots, $len) {
        for($i = 0, $c = count($annots); $i < $c; $i++) {
            $annots[$i]['cplen'] += $len;
        }

        return $annots;
    }
}