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

class phpMorphy_Dict_Source_Xml implements phpMorphy_Dict_Source_SourceInterface {
    protected
        $xml_file,
        $locale;

    function __construct($xmlFile) {
        $this->xml_file = $xmlFile;

        foreach(new phpMorphy_Dict_Source_Xml_Section_Options($xmlFile) as $key => $value) {
            if('locale' === $key) {
                $this->locale = $value;
                break;
            }
        }

        if(!strlen($this->locale)) {
            throw new Exception("Can`t find locale in '{$xmlFile}' file");
        }
    }

    function getName() {
        return 'morphyXml';
    }

    function getLanguage() {
        return $this->locale;
    }

    function getDescription() {
        return "Morphy xml file '{$this->xml_file}'";
    }

    function getAncodes() {
        return new phpMorphy_Dict_Source_Xml_Section_Ancodes($this->xml_file);
    }

    function getFlexias() {
        return new phpMorphy_Dict_Source_Xml_Section_Flexias($this->xml_file);
    }

    function getPrefixes() {
        return new phpMorphy_Dict_Source_Xml_Section_Prefixes($this->xml_file);
    }

    function getLemmas() {
        return new phpMorphy_Dict_Source_Xml_Section_Lemmas($this->xml_file);
    }

    function getAccents() {
        // HACK: all lemmas points to accent model with 0 index and length = 4096
        $accent_model = new phpMorphy_Dict_AccentModel(0);
        $accent_model->import(new ArrayIterator(array_fill(0, 4096, null)));

        return new ArrayIterator(array( 0 => $accent_model));
    }
}