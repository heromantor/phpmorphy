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

class phpMorphy_Util_Iterator_Iconv extends IteratorIterator {
	private
		$encoding,
		$int_encoding;

	function __construct(Iterator $it, $encoding = null, $internalEncoding = 'UTF-8') {
		parent::__construct($it);

		$this->setEncoding($encoding);
		$this->setInternalEncoding($internalEncoding);
	}

	function ignoreUnknownChars() {
		$this->insertEncModifier('IGNORE');
	}

	function translitUnknownChars() {
		$this->insertEncModifier('IGNORE');
	}

	protected function insertEncModifier($modifier) {
		$enc = $this->getEncodingWithoutModifiers();

		$this->setEncoding("{$enc}//$modifier");
	}

	protected function getEncodingWithoutModifiers() {
		$enc = $this->encoding;

		if(false !== ($pos = strrpos($enc, '//'))) {
			return substr($enc, 0, $pos);
		} else {
			return $enc;
		}
	}

	function setEncoding($encoding) {
		$this->encoding = $encoding;
	}

	function getEncoding() {
		return $this->getEncodingWithoutModifiers();
	}

	function setInternalEncoding($encoding) {
		$this->int_encoding = $encoding;
	}

	function getInternalEncoding() {
		return $this->int_encoding;
	}

	function current() {
        $string = parent::current();
        
		if(isset($this->encoding) && $this->encoding !== $this->int_encoding) {
			$result = iconv($this->encoding, $this->int_encoding, $string);
			//$result = mb_convert_encoding($string, $this->int_encoding, $this->encoding);

			if(!is_string($result)) {
				throw new phpMorphy_Exception(
					"Can`t convert '$string' " . $this->getEncoding() . ' -> ' . $this->getInternalEncoding()
				);
			}

			return $result;
		} else {
			return $string;
		}
	}
}