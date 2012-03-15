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

class phpMorphy_Aot_Mrd_File {
	protected
		$flexias,
		$accents,
		$sessions,
		$prefixes,
		$lemmas
		;

	function __construct($fileName, $encoding) {
		$line = 0;
		$this->initSections($line, $fileName, $encoding);
	}

	protected function initSections(&$startLine, $fileName, $encoding) {
		foreach($this->getSectionsNames() as $sectionName) {
			try {
				$section = $this->createNewSection(
					$sectionName,
					$fileName,
					$encoding,
					$startLine
				);

				$this->$sectionName = $section;
			} catch(Exception $e) {
				throw new phpMorphy_Aot_Mrd_Exception("Can`t init '$sectionName' section: " . $e->getMessage());
			}
		}
	}

	protected function createNewSection($sectionName, $fileName, $encoding, &$lineNo) {
		$sect_clazz = $this->getSectionClassName($sectionName);

		$section = new $sect_clazz($this->openFile($fileName), $encoding, $lineNo);
		$lineNo += $section->getSectionLinesCount();

		return $section;
	}

	protected function getSectionsNames() {
		return array(
			'flexias',
			'accents',
			'sessions',
			'prefixes',
			'lemmas'
		);
	}

	protected function openFile($fileName) {
		return new SplFileObject($fileName);
	}

	protected function getSectionClassName($sectionName) {
		return 'phpMorphy_Aot_Mrd_Section_' . ucfirst(strtolower($sectionName));
	}

	function __get($propName) {
		if(!preg_match('/^\w+_section$/', $propName)) {
			throw new phpMorphy_Aot_Mrd_Exception("Unsupported prop name given $propName");
		}

		list($sect_name) = explode('_', $propName);

		if(!isset($this->$sect_name)) {
			throw new phpMorphy_Aot_Mrd_Exception("Invalid section name given $propName");
		}

		return $this->$sect_name;
	}
}