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

class phpMorphy_Util_Hunspell_DictFile {
	protected
		$file_name,
		$affix,
		$encoding
		;

	function __construct($fileName, phpMorphy_Util_Hunspell_AffixFile $affixFile, $encoding = null) {
		$this->file_name = $fileName;
		$this->affix = $affixFile;

		if($encoding === null) {
			try {
				$encoding = $affixFile->getEncoding();
			} catch(Exception $e) {
				throw new phpMorphy_Util_Hunspell_Exception("You must explicit specifiy encoding, because affix file dosn`t contain encoding");
			}
		}

		$this->encoding = $encoding;
	}

	protected function createDictReader() {
		return new phpMorphy_Util_Hunspell_DictFileReader($this->file_name, $this->encoding);
	}

	function export($callback) {
		$reader = $this->createDictReader();
		$reader->rewind();

		if($reader->valid()) {
			$tokens = $reader->current();

			if(preg_match('~^[0-9]+$~', $tokens['word'])) {
				$reader->next();
			}
		}

		while($reader->valid()) {
			$result = $reader->current();
			$reader->next();

			$all_words = $this->generateWordForms($result['word'], $result['morph'], $result['flags']);

			if(false === call_user_func($callback, $result['word'], $all_words['lemma'], $all_words['words'], $all_words['morphs'])) {
				break;
			}
		}
	}

	protected function generateWordForms($base, $baseMorph, $flagsList) {
		$prefix_flags = array();
		$suffix_flags = array();

		foreach($flagsList as $flag) {
			if($this->affix->isFlagExists($flag)) {
				$flag_obj = $this->affix->getFlag($flag);

				if($flag_obj->isSuffix()) {
					$suffix_flags[$flag] = $flag_obj;
				} else {
					$prefix_flags[$flag] = $flag_obj;
				}
			}
		}

		$words = array($base);
		$morphs = array($baseMorph);
		$lemma = '';

		// process prefixes
		$max_prefix_removed = $this->generateWordsForAffixes($base, $prefix_flags, $words, $baseMorph, $morphs);
		// process suffixes
		$max_suffix_removed = $this->generateWordsForAffixes($base, $suffix_flags, $words, $baseMorph, $morphs);

		if($max_suffix_removed) {
			$lemma = mb_substr($base, $max_prefix_removed, -$max_suffix_removed);
		} else {
			$lemma = mb_substr($base, $max_prefix_removed);
		}

		// process cross product
		if(count($prefix_flags) && count($suffix_flags)) {
			foreach($prefix_flags as $prefix) {
				if($prefix->isCrossProduct()) {
					$prefixed_bases = array();
					$prefixed_morphs = array();
					$prefix->generateWords($base, $prefixed_bases, $baseMorph, $prefixed_morphs);

					if(count($prefixed_bases)) {
						foreach($suffix_flags as $suffix) {
							if($suffix->isCrossProduct()) {
								$i = 0;
								foreach($prefixed_bases as $prefixed_base) {
									$suffix->generateWords($prefixed_base, $words, $prefixed_morphs[$i], $morphs);
									$i++;
								}
							}
						}
					}
				}
			}
		}

		return array(
			'words' => $words,
			'morphs' => $morphs,
			'lemma' => $lemma
		);
	}

	protected function generateWordsForAffixes($base, $affixes, &$words, $wordMorph, &$morphs) {
		$max_removed = 0;

		foreach($affixes as $affix) {
			$removed_length = $affix->generateWords($base, $words, $wordMorph, $morphs);

			$max_removed = max($removed_length, $max_removed);
		}

		return $max_removed;
	}
}