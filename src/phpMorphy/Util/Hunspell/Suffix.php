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

class phpMorphy_Util_Hunspell_Suffix extends phpMorphy_Util_Hunspell_AffixAbstract {
	protected function getRegExp($find) {
		//return $find;
		return "~{$find}$~iu";
	}

	function generateWord($word) {
		if(!$this->isMatch($word)) {
			return false;
		}

		if($this->remove_len && mb_strlen($word) >= $this->remove_len) {
			$tail = mb_substr($word, -$this->remove_len);

			if($tail != $this->remove) {
				vd("Try to remove $tail from $word");
				vd($this);
				exit;
			}

			$word = mb_substr($word, 0, -$this->remove_len);
		}

		return "$word{$this->append}";
	}

	protected function simpleMatch($word) {
		return mb_substr($word, -$this->find_len) == $this->find;
	}
}