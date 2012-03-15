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

class phpMorphy_GrammemsProvider_ru_RU extends phpMorphy_GrammemsProvider_ForFactoryAbstract {
    const INTERNAL_ENCODING = 'utf-8';
    static protected $instances = array();

    static protected $grammems_map = array(
        'род' => array('МР', 'ЖР', 'СР'),
        'одушевленность' => array('ОД', 'НО'),
        'число' => array('ЕД', 'МН'),
        'падеж' => array('ИМ', 'РД', 'ДТ', 'ВН', 'ТВ', 'ПР', 'ЗВ', '2'),
        'залог' => array('ДСТ', 'СТР'),
        'время' => array('НСТ', 'ПРШ', 'БУД'),
        'повелительная форма' => array('ПВЛ'),
        'лицо' => array('1Л', '2Л', '3Л'),
        'краткость' => array('КР'),
        'сравнительная форма' => array('СРАВН'),
        'превосходная степень' => array('ПРЕВ'),
        'вид' => array('СВ', 'НС'),
        'переходность' => array('ПЕ', 'НП'),
        'безличный глагол' => array('БЕЗЛ'),
    );

    function getSelfEncoding() {
        return self::INTERNAL_ENCODING;
    }

    function getGrammemsMap() {
        return self::$grammems_map;
    }

    static function instance(phpMorphy_MorphyInterface $morphy) {
        $key = $morphy->getEncoding();

        if(!isset(self::$instances[$key])) {
            $class = __CLASS__;
            self::$instances[$key] = new $class($key);
        }

        return self::$instances[$key];
    }
}