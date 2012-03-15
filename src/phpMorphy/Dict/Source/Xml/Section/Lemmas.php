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

class phpMorphy_Dict_Source_Xml_Section_Lemmas extends phpMorphy_Dict_Source_Xml_SectionAbstract {
    protected
        $count,
        $current;

    protected function getSectionName() {
        return 'lemmas';
    }

    function rewind() {
        $this->count = 0;
        $this->current = null;

        parent::rewind();
    }

    protected function readNext(XMLReader $reader) {
        do {
            if($this->isStartElement('lemma')) {
                unset($this->current);

                $this->current = new phpMorphy_Dict_Lemma(
                    $reader->getAttribute('base'),
                    $reader->getAttribute('flexia_id'),
                    0
                );

                $prefix_id = $reader->getAttribute('prefix_id');
                $ancode_id = $reader->getAttribute('ancode_id');

                if(!is_null($prefix_id)) {
                    $this->current->setPrefixId($prefix_id);
                }

                if(!is_null($ancode_id)) {
                    $this->current->setAncodeId($ancode_id);
                }

                $this->count++;

                $this->read();

                break;
            }
        } while($this->read());
    }

    protected function getCurrentKey() {
        return $this->count - 1;
    }

    protected function getCurrentValue() {
        return $this->current;
    }
}