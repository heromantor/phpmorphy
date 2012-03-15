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

class phpMorphy_Aot_Mrd_Section_Lemmas extends phpMorphy_Aot_Mrd_SectionAbstract {
	protected function processLine($line) {
		//if(6 != count($tokens = array_map('trim', explode(' ', $line)))) {
		$line = $this->iconv($line);

		if(6 != count($tokens = explode(' ', $line))) {
			throw new phpMorphy_Aot_Mrd_Exception("Invalid lemma str('$line'), too few tokens");
		}

		$base = trim($tokens[0]);

		if($base === '#') {
			$base = '';
		}

		$lemma = new phpMorphy_Dict_Lemma(
			$base, //$this->iconv(trim($tokens[0])), // base
			(int)$tokens[1], // flexia_id
			(int)$tokens[2] // accent_id
		);

		if('-' !== $tokens[4]) {
			$lemma->setAncodeId($tokens[4]);
		}

		if('-' !== $tokens[5]) {
			$lemma->setPrefixId((int)$tokens[5]);
		}

		return $lemma;
	}
}