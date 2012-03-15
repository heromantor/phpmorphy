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

class phpMorphy_Fsa_WordsCollector_ForPrediction extends phpMorphy_Fsa_WordsCollector {
    protected
        $used_poses = array(),
        $annot_decoder,
        $collected = 0;

    function __construct($limit, phpMorphy_AnnotDecoder_AnnotDecoderInterface $annotDecoder) {
        parent::__construct($limit);

        $this->annot_decoder = $annotDecoder;
    }

    function collect($path, $annotRaw) {
        if($this->collected > $this->limit) {
            return false;
        }

        $used_poses =& $this->used_poses;
        $annots = $this->decodeAnnot($annotRaw);

        for($i = 0, $c = count($annots); $i < $c; $i++) {
            $annot = $annots[$i];
            $annot['cplen'] = $annot['plen'] = 0;

            $pos_id = $annot['pos_id'];

            if(isset($used_poses[$pos_id]) && false) {
                $result_idx = $used_poses[$pos_id];

                if($annot['freq'] > $this->items[$result_idx]['freq']) {
                    $this->items[$result_idx] = $annot;
                }
            } else {
                $used_poses[$pos_id] = count($this->items);
                $this->items[] = $annot;
            }
        }

        $this->collected++;
        return true;
    }

    function clear() {
        parent::clear();
        $this->collected = 0;
        $this->used_poses = array();
    }

    function decodeAnnot($annotRaw) {
        return $this->annot_decoder->decode($annotRaw, true);
    }
}