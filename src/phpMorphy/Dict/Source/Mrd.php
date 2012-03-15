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

class phpMorphy_Dict_Source_Mrd implements phpMorphy_Dict_Source_SourceInterface {
    protected
        $manager;

    function __construct($mwzFilePath) {
        $this->manager = $this->createMrdManager($mwzFilePath);
    }

    protected function createMrdManager($mwzPath) {
        $manager = new phpMorphy_Aot_MrdManager();
        $manager->open($mwzPath);

        return $manager;
    }

    function getName() {
        return 'mrd';
    }

    // phpMorphy_Dict_Source_SourceInterface
    function getLanguage() {
        $lang = strtolower($this->manager->getLanguage());

        switch($lang) {
            case 'russian':
                return 'ru_RU';
            case 'english':
                return 'en_EN';
            case 'german':
                return 'de_DE';
            default:
                return $this->manager->getLanguage();
        }
    }

    function getDescription() {
        return 'Dialing dictionary file for ' . $this->manager->getLanguage() . ' language';
    }

    function getAncodes() {
        return $this->manager->getGramInfo();
    }

    function getFlexias() {
        return $this->manager->getMrd()->flexias_section;
    }

    function getPrefixes() {
        return $this->manager->getMrd()->prefixes_section;
    }

    function getAccents() {
        return $this->manager->getMrd()->accents_section;
    }

    function getLemmas() {
        return $this->manager->getMrd()->lemmas_section;
    }
}