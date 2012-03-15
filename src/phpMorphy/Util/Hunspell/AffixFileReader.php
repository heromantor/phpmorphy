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

class phpMorphy_Util_Hunspell_AffixFileReader extends IteratorIterator {
	function __construct($fileName, $defaultEncoding) {
		parent::__construct($this->createIterators($this->createIterators($fileName)));

		$this->setEncoding($defaultEncoding);
	}

	function setEncoding($enc) {
		$this->getInnerIterator()->setEncoding($enc);
	}

	protected function createIterators($fileName) {
		return new phpMorphy_Util_Iterator_Iconv(
			new phpMorphy_Util_Iterator_Filter(
				new SplFileObject($fileName),
                function($item) {
                    return strlen($item) > 0;
                }
			)
		);
	}
    
	function current() {
		return explode(
			' ',
			preg_replace('~\s{2,}~', ' ', trim(parent::current()))
		);
	}
}