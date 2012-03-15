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

class phpMorphy_Aot_Mrd_Section_Accents extends phpMorphy_Aot_Mrd_SectionAbstract {
	const UNKNOWN_ACCENT_VALUE = 255;

	protected function processLine($line) {
		if(substr($line, -1, 1) == ';') {
			$line = substr($line, 0, -1);
		}

		$result = new phpMorphy_Dict_AccentModel($this->getPosition());
		$result->import(
			new ArrayIterator(
				array_map(
					array($this, 'processAccentValue'),
					explode(';', $line)
				)
			)
		);

		return $result;
	}

	protected function processAccentValue($item) {
		$item = (int)$item;

		if($item == self::UNKNOWN_ACCENT_VALUE) {
			$item = null;
		}

		return $item;
	}
}