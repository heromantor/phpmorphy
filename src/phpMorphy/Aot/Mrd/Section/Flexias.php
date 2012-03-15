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

class phpMorphy_Aot_Mrd_Section_Flexias extends phpMorphy_Aot_Mrd_SectionAbstract {
	const COMMENT_STRING = 'q//q';

	protected function processLine($line) {
		$line = $this->iconv($this->removeComment($line));

		$model = new phpMorphy_Dict_FlexiaModel($this->getPosition());

		foreach(explode('%', substr($line, 1)) as $token) {
			//$parts = array_map('trim', explode('*', $token));
			$parts = explode('*', $token);

			switch(count($parts)) {
				case 2:
					$ancode = $parts[1];
					$prefix = '';
					break;
				case 3:
					$ancode = $parts[1];
					$prefix = $parts[2];
					break;
				default:
					throw new phpMorphy_Aot_Mrd_Exception("Invalid flexia string($token) in str($line)");
			}

			$flexia = $parts[0];

			$model->append(
				new phpMorphy_Dict_Flexia(
					$prefix, //$this->iconv($prefix),
					$flexia, //$this->iconv($flexia),
					$ancode
				)
			);
		}

		return $model;
	}

	protected function removeComment($line) {
		if(false !== ($pos = strrpos($line, self::COMMENT_STRING))) {
			return rtrim(substr($line, 0, $pos));
		} else {
			return $line;
		}
	}
}