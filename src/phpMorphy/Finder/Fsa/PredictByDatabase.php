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

class phpMorphy_Finder_Fsa_PredictByDatabase extends phpMorphy_Finder_Fsa_Finder {
    protected
        $collector,
        $unicode,
        $graminfo,
        $min_postfix_match;

    function __construct(
        phpMorphy_Fsa_FsaInterface $fsa,
        phpMorphy_AnnotDecoder_AnnotDecoderInterface $annotDecoder,
        $encoding,
        phpMorphy_GramInfo_GramInfoInterface $graminfo,
        $minPostfixMatch = 2,
        $collectLimit = 32
    ) {
        parent::__construct($fsa, $annotDecoder);

        $this->graminfo = $graminfo;
        $this->min_postfix_match = $minPostfixMatch;
        $this->collector = $this->createCollector($collectLimit, $this->getAnnotDecoder());

        $this->unicode = phpMorphy_UnicodeHelper_UnicodeHelperAbstract::getHelperForEncoding($encoding);
    }
    
    protected function doFindWord($word) {
        $rev_word = $this->unicode->strrev($word);

        $result = $this->fsa->walk($this->root, $rev_word);

        if($result['result'] && null !== $result['annot']) {
            $annots = $result['annot'];
        } else {
            $match_len = $this->unicode->strlen($this->unicode->clearIncompleteCharsAtEnd($GLOBALS['__phpmorphy_substr']($rev_word, 0, $result['walked'])));

            if(null === ($annots = $this->determineAnnots($result['last_trans'], $match_len))) {
                return false;
            }
        }

        if(!is_array($annots)) {
            $annots = $this->collector->decodeAnnot($annots);
        }

        return $this->fixAnnots($word, $annots);
    }

    protected function determineAnnots($trans, $matchLen) {
        $annots = $this->fsa->getAnnot($trans);

        if(null == $annots && $matchLen >= $this->min_postfix_match) {
            $this->collector->clear();

            $this->fsa->collect(
                $trans,
                $this->collector->getCallback()
            );

            $annots = $this->collector->getItems();
        }

        return $annots;
    }

    protected function fixAnnots($word, $annots) {
        $result = array();

        // remove all prefixes?
        for($i = 0, $c = count($annots); $i < $c; $i++) {
            $annot = $annots[$i];

            $annot['cplen'] = $annot['plen'] = 0;

            $flexias = $this->graminfo->readFlexiaData($annot, false);

            $prefix = $flexias[$annot['form_no'] * 2];
            $suffix = $flexias[$annot['form_no'] * 2 + 1];

            $plen = $GLOBALS['__phpmorphy_strlen']($prefix);
            $slen = $GLOBALS['__phpmorphy_strlen']($suffix);
            if(
                (!$plen || $GLOBALS['__phpmorphy_substr']($word, 0, $GLOBALS['__phpmorphy_strlen']($prefix)) === $prefix) &&
                (!$slen || $GLOBALS['__phpmorphy_substr']($word, -$GLOBALS['__phpmorphy_strlen']($suffix)) === $suffix)
            ) {
                $result[] = $annot;
            }
        }

        return count($result) ? $result : false;
    }

    protected function createCollector($limit) {
        return new phpMorphy_Fsa_WordsCollector_ForPrediction($limit, $this->getAnnotDecoder());
    }
}