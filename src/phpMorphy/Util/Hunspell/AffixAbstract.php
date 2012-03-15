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

abstract class phpMorphy_Util_Hunspell_AffixAbstract {
	protected
		$remove_len,
		$remove,
		$append,
		$find,
		$find_len,
		$morph,
		$reg,
		$is_simple,
		$is_empty
		;

	function __construct($find, $remove, $append, $morph = null) {
		$this->remove_len = mb_strlen((string)$remove);
		$this->remove = $remove;
		$this->append = $append;
		$this->morph = $morph;
		$this->find = $find;
		$this->find_len = mb_strlen($find);
		$this->is_simple = $this->isSimple($find);
		$this->is_empty = $this->isEmpty($find);

		$this->reg = $this->getRegExp($find);
	}

	function getRemoveLength() { return $this->remove_len; }
	function isMorphDescription() { return isset($this->morph); }
	function getMorphDescription() { return $this->morph; }

	function isMatch($word) {
		if($this->is_empty) {
			return true;
		}

		if($this->is_simple) {
			return $this->simpleMatch($word);
		} else {
			//return false;
			return preg_match($this->reg, $word) > 0;
			//return mb_ereg_match($this->reg, $word);
		}
	}

	protected function isSimple($find) {
		return strpos($find, '[') === false && strpos($find, '.') === false;
	}

	protected function isEmpty($find) {
		return $find === '.';
	}

	abstract function generateWord($word);

	abstract protected function simpleMatch($word);
	abstract protected function getRegExp($find);
}