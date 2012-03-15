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

class phpMorphy_Aot_Rml_IniFile {
	const RML_PLACEHOLDER = '$RML';
	const RML_ENV_VAR = 'RML';

	protected
		$ini,
		$rml;

	function __construct() {
		$this->ini = $this->parseFile($this->getIniPath());
	}

	function getGramTabPath($language) {
		return $this->getValue($this->getGramTabPathKey($language));
	}

	function export() {
		return $this->ini;
	}

	function keyExists($key) {
		return array_key_exists($key, $this->ini);
	}

	function getValue($key) {
		if(!$this->keyExists($key)) {
			throw new phpMorphy_Aot_Rml_Exception("Key $key not exists in rml.ini");
		}

		return $this->ini[$key];
	}

	protected function getGramTabPathKey($language) {
		if(!strlen($language)) {
			throw new phpMorphy_Aot_Rml_Exception("You must specify language for gram tab file");
		}

		$uc_lang = ucfirst(strtolower($language));
		$first_char = $uc_lang[0];

		return 'Software\\Dialing\\Lemmatizer\\' . $uc_lang . '\\' . $first_char . 'gramtab';
	}

	protected function parseFile($file) {
		$result = array();

		try {
			$lines = iterator_to_array($this->createIterators($file));
		} catch (Exception $e) {
			throw new phpMorphy_Aot_Rml_Exception("Can`t open $file file: " . $e->getMessage());
		}

		foreach($lines as $line) {
			if(false !== ($pos = strpos($line, ' ')) || false !== ($pos = strpos($line, "\t"))) {
				$key = trim(substr($line, 0, $pos));
				$value = $this->replaceRmlVar(trim(substr($line, $pos + 1)));

				if(strlen($key)) {
					$result[$key] = $value;
				}
			}
		}

		return $result;
	}

	protected function createIterators($file) {
		return new phpMorphy_Util_Iterator_Filter(
            new SplFileObject($file),
            function($item) {
                return strlen($item) > 0;
            }
        );
	}

	protected function replaceRmlVar($line) {
		return str_replace(self::RML_PLACEHOLDER, $this->getRmlVar(), $line);
	}

	protected function getRmlVar() {
		if(!isset($this->rml)) {
			if(false === ($this->rml = getenv(self::RML_ENV_VAR))) {
				throw new phpMorphy_Aot_Rml_Exception("Can`t find RML environment variable");
			}
		}

		return $this->rml;
	}

	protected function getIniPath() {
		return $this->getRmlVar() . '/Bin/rml.ini';
	}
}