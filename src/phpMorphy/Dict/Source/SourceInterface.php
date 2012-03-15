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

interface phpMorphy_Dict_Source_SourceInterface {
    /**
     * @return string
     */
    function getName();
    /**
     * ISO3166 country code separated by underscore(_) from ISO639 language code
     * ru_RU, uk_UA for example
     * @return string
     */
    function getLanguage();
    /**
     * Any string
     * @return string
     */
    function getDescription();

    /**
     * @return Iterator over objects of phpMorphy_Dict_Ancode
     */
    function getAncodes();
    /**
     * @return Iterator over objects of phpMorphy_Dict_FlexiaModel
     */
    function getFlexias();
    /**
     * @return Iterator over objects of phpMorphy_Dict_PrefixSet
     */
    function getPrefixes();
    /**
     * @return Iterator over objects of phpMorphy_Dict_AccentModel
     */
    function getAccents();
    /**
     * @return Iterator over objects of phpMorphy_Dict_Lemma
     */
    function getLemmas();
}