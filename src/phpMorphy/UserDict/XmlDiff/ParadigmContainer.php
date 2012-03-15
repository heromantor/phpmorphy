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


class phpMorphy_UserDict_XmlDiff_ParadigmContainer {
    /** @var phpMorphy_Paradigm_ParadigmInterface */
    private $collection = array();

    function append(phpMorphy_Paradigm_ParadigmInterface $paradigm) {
        $this->collection[] = $paradigm;
    }

    function delete($index) {
        array_splice($this->collection, $index, 1);
    }

    function saveToMutableSource(
        phpMorphy_Dict_Source_Mutable $source,
        phpMorphy_UserDict_EncodingConverter $converter
    ) {
        $saver = new phpMorphy_UserDict_XmlDiff_ParadigmSaver($source, $converter);

        foreach($this->collection as $paradigm) {
            $saver->save($paradigm);
        }
    }

    function findWord($word, $onlyLemma = false, &$indices = null) {
        $result = array();
        $indices = array();

        $i = 0;
        /** @var phpMorphy_Paradigm_ParadigmInterface $paradigm */
        foreach($this->collection as $paradigm) {
            if(in_array($word, $paradigm->getAllForms())) {
                $result[] = $paradigm;
                $indices[] = $i;
            }
        }

        return count($result) ? $result : false;
    }
}