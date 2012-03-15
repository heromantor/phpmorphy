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

class phpMorphy_Dict_Source_Xml_Section_Flexias extends phpMorphy_Dict_Source_Xml_SectionAbstract {
    protected
        $current;

    protected function getSectionName() {
        return 'flexias';
    }

    function rewind() {
        $this->current = null;

        parent::rewind();
    }

    protected function readNext(XMLReader $reader) {
        do {
            if($this->isStartElement('flexia_model')) {
                $flexia_model = new phpMorphy_Dict_FlexiaModel($reader->getAttribute('id'));

                while($this->read()) {
                    if($this->isStartElement('flexia')) {
                            $flexia_model->append(
                                new phpMorphy_Dict_Flexia(
                                    $reader->getAttribute('prefix'),
                                    $reader->getAttribute('suffix'),
                                    $reader->getAttribute('ancode_id')
                                )
                            );
                    } elseif($this->isEndElement('flexia_model')) {
                        break;
                    }
                }

                unset($this->current);
                $this->current = $flexia_model;

                break;
            }
        } while($this->read());
    }

    protected function getCurrentKey() {
        return $this->current->getId();
    }

    protected function getCurrentValue() {
        return $this->current;
    }
}