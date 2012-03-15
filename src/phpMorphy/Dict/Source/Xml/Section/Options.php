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

class phpMorphy_Dict_Source_Xml_Section_Options extends phpMorphy_Dict_Source_Xml_SectionAbstract {
    protected
        $current;

    protected function getSectionName() {
        return 'options';
    }

    protected function readNext(XMLReader $reader) {
        do {
            if($this->isStartElement('locale')) {
                if(!$this->current = $reader->getAttribute('name')) {
                    throw new Exception('Empty locale name found');
                }

                $this->read();

                break;
            }
        } while($this->read());
    }

    function rewind() {
        $this->current = null;

        parent::rewind();
    }

    protected function getCurrentKey() {
        return 'locale';
    }

    protected function getCurrentValue() {
        return $this->current;
    }
}