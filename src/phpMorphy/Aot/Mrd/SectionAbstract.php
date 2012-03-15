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

abstract class phpMorphy_Aot_Mrd_SectionAbstract implements Iterator, Countable {
	const INTERNAL_ENCODING = 'utf-8';

	protected
		$file_it,
		$encoding,
		$start_line,
		$current_line,
		$section_size;

	function __construct(SeekableIterator $file, $encoding, $startLine) {
		$this->file_it = $file;

		$this->encoding = $this->prepareEncoding($encoding);
		$this->start_line = $startLine;
		$this->section_size = $this->readSectionSize($file);
	}

	protected function prepareEncoding($encoding) {
		$encoding = strtolower($encoding);

		if($encoding == 'utf8') {
			$encoding = 'utf-8';
		}

		return $encoding;
	}

	protected function openFile($fileName) {
		return new SplFileObject($fileName);
	}

	function getSectionLinesCount() {
		return $this->count() + 1;
	}

	function count() {
		return $this->section_size;
	}

	function key() {
		return $this->current_line;
	}

	function getPosition() {
		return $this->current_line;
	}

	function rewind() {
		$this->current_line = 0;
		$this->file_it->seek($this->start_line + 1);
	}

	function valid() {
		if($this->current_line >= $this->section_size) {
			return false;
		}

		if(!$this->file_it->valid()) {
			throw new phpMorphy_Aot_Mrd_Exception(
				"Too small section {$this->current_line} lines gathered, $this->section_size expected"
			);
		}

		return true;
	}

	function current() {
		return $this->processLine(rtrim($this->file_it->current()));
	}

	function next() {
		$this->file_it->next();
		$this->current_line++;
	}

	protected function iconv($string) {
		if($this->encoding == self::INTERNAL_ENCODING) {
			return $string;
		}

		return iconv($this->encoding, self::INTERNAL_ENCODING, $string);
	}

	protected function readSectionSize(SeekableIterator $it) {
		$it->seek($this->start_line);

		if(!$it->valid()) {
			throw new phpMorphy_Aot_Mrd_Exception("Can`t read section size, iterator not valid");
		}

		$size = trim($it->current());

		if(!preg_match('~^[0-9]+$~', $size)) {
			throw new phpMorphy_Aot_Mrd_Exception("Invalid section size: $size");
		}

		return (int)$size;
	}

	protected function processLine($line) {
		return $line;
	}
}