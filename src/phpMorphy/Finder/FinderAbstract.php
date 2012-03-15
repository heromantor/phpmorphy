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

abstract class phpMorphy_Finder_FinderAbstract implements phpMorphy_Finder_FinderInterface {
    protected
        /** @var phpMorphy_AnnotDecoder_AnnotDecoderInterface */
        $annot_decoder,
        /** @var string */
        $prev_word,
        /** @var array */
        $prev_result = false;

    /**
     * @param phpMorphy_AnnotDecoder_AnnotDecoderInterface $annotDecoder
     */
    function __construct(phpMorphy_AnnotDecoder_AnnotDecoderInterface $annotDecoder) {
        $this->annot_decoder = $annotDecoder;
    }

    /**
     * @param string $word
     * @return array
     */
    function findWord($word) {
        if($this->prev_word === $word) {
            return $this->prev_result;
        }

        $result = $this->doFindWord($word);

        $this->prev_word = $word;
        $this->prev_result = $result;

        return $result;
    }

    /**
     * @return phpMorphy_AnnotDecoder_AnnotDecoderInterface
     */
    function getAnnotDecoder() {
        return $this->annot_decoder;
    }

    /**
     * @param string $raw
     * @param bool $withBase
     * @return array
     */
    function decodeAnnot($raw, $withBase) {
        return $this->annot_decoder->decode($raw, $withBase);
    }

    /**
     * @abstract
     * @param string $word
     * @return array
     */
    abstract protected function doFindWord($word);
}