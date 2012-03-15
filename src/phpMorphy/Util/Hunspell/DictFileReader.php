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

class phpMorphy_Util_Hunspell_DictFileReader extends IteratorIterator {
	function __construct($fileName, $encoding) {
		parent::__construct($this->createIterators($fileName, $encoding));
	}

	protected function createIterators($fileName, $encoding) {
		return new phpMorphy_Util_Iterator_Iconv(
			new phpMorphy_Util_Iterator_Filter(
                new SplFileObject($fileName),
                function($item) {
                    return strlen($item) > 0;
                }
            ),
			$encoding
		);
	}

	function current() {
		$line = trim(parent::current());

		$word = '';
		$flags = '';
		$morph = '';

		if(false !== ($pos = mb_strpos($line, "\t"))) {
			$morph = trim(mb_substr($line, $pos + 1));
			$line = rtrim(mb_substr($line, 0, $pos));
		}

		if(false !== ($pos = mb_strpos($line, '/'))) {
			$word = rtrim(mb_substr($line, 0, $pos));
			$flags = ltrim(mb_substr($line, $pos + 1));
		} else {
			$word = $line;
		}

		return array(
			'word' => $word,
			'flags' => $this->parseFlags($flags),
			'morph' => $morph
		);
	}

	protected function parseFlags($flags) {
		// TODO: May be long(two chars?) or numeric format(aka compressed)
		// But i support only basic syntax now
		return strlen($flags) ? str_split($flags) : array();
	}
}