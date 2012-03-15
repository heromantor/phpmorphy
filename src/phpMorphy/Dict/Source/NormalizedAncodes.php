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



class phpMorphy_Dict_Source_NormalizedAncodes
    extends phpMorphy_Dict_Source_Decorator
    implements phpMorphy_Dict_Source_NormalizedAncodesInterface
{
    protected
        $manager;

    static function wrap(phpMorphy_Dict_Source_SourceInterface $source) {
        if($source instanceof phpMorphy_Dict_Source_NormalizedAncodes) {
            return $source;
        }

        return new phpMorphy_Dict_Source_NormalizedAncodes($source);
    }

    function __construct(phpMorphy_Dict_Source_SourceInterface $inner) {
        parent::__construct($inner);

        $this->manager = $this->createManager($inner);
    }

    protected function createManager($inner) {
        return new phpMorphy_Dict_Source_NormalizedAncodes_AncodesManager($inner);
    }

    function getPoses() {
        return array_values($this->manager->getPosesMap());
    }

    function getGrammems() {
        return array_values($this->manager->getGrammemsMap());
    }

    function getAncodes() {
        return $this->manager->getAncodes();
    }

    function getFlexias() {
        return $this->createDecoratingIterator(parent::getFlexias(), 'phpMorphy_Dict_Source_NormalizedAncodes_FlexiaModel');
    }

    function getLemmas() {
        return $this->createDecoratingIterator(parent::getLemmas(), 'phpMorphy_Dict_Source_NormalizedAncodes_Lemma');
    }

    protected function createDecoratingIterator(Traversable $it, $newClass) {
        return new phpMorphy_Dict_Source_NormalizedAncodes_DecoratingIterator($it, $this->manager, $newClass);
    }
}