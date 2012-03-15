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


class phpMorphy_Dict_Writer_Csv extends phpMorphy_Dict_Writer_WriterAbstract {
    const DUMP_EVERY_FLEXIA_MODEL = 64;
    const DUMP_EVERY_LEMMA = 1024;

    private $poses_file;
    private $grammems_file;
    private $ancodes_file;
    private $flexia_models_file;
    private $flexias_file;
    private $prefixes_file;
    private $lemmas_file;
    private $is_write_header;

    function __construct(
        $posesFile,
        $grammemsFile,
        $ancodesFile,
        $flexiasFile,
        $prefixesFile,
        $lemmasFile,
        $isWriteHeader = true
    ) {
        parent::__construct();

        $this->poses_file = (string)$posesFile;
        $this->grammems_file = (string)$grammemsFile;
        $this->ancodes_file = (string)$ancodesFile;
        $this->flexias_file = (string)$flexiasFile;
        $this->prefixes_file = (string)$prefixesFile;
        $this->lemmas_file = (string)$lemmasFile;

        $this->is_write_header = (bool)$isWriteHeader;
    }

    function write(phpMorphy_Dict_Source_SourceInterface $source) {
        $this->getObserver()->onStart();

        try {
            $source = phpMorphy_Dict_Source_ValidatingSource::wrap($source);

            $this->writePoses($this->openFile($this->poses_file), $source->getPoses());
            $this->writeGrammems($this->openFile($this->grammems_file), $source->getGrammems());
            $this->writeAncodes($this->openFile($this->ancodes_file), $source->getAncodes());
            $this->writeFlexias($this->openFile($this->flexias_file), $source->getFlexias());
            $this->writePrefixes($this->openFile($this->prefixes_file), $source->getPrefixes());
            $this->writeLemmas($this->openFile($this->lemmas_file), $source->getLemmas());
        } catch (Exception $e) {
            $this->getObserver()->onEnd();
            throw $e;
        }

        $this->getObserver()->onEnd();
    }

    private function openFile($name) {
        return fopen($name, 'wb');
    }

    private function writeLine($fh, $tokens) {
        $tokens = array_map(
            function($token) {
                if(is_int($token)) {
                    return (string)$token;
                } else if(null === $token) {
                    return 'NULL';
                } else {
                    return '"' . str_replace('"', '""', (string)$token) . '"';
                }
            },
            $tokens
        );

        fwrite($fh, implode(',', $tokens) . PHP_EOL);
    }

    private function writeFlexias($fh, $it) {
        $this->log(__METHOD__);
        if(!$this->checkForIteratorNotEmpty($it)) return;

        $this->writeLine($fh, array('flexia_model_id', 'prefix', 'suffix', 'ancode_id'));
        $count = 0;

        foreach($it as $flexia_model) {
            $flexia_model_id = $flexia_model->getId();

            foreach($flexia_model as $flexia) {
                $this->writeLine(
                    $fh,
                    array(
                         $flexia_model_id,
                         $flexia->getPrefix(),
                         $flexia->getSuffix(),
                         $flexia->getAncodeId()
                    )
                );
            }

            $count++;

            if(0 == ($count % self::DUMP_EVERY_FLEXIA_MODEL)) {
                $this->log("$count flexia models done");
            }
        }
    }

    private function writePrefixes($fh, $it) {
        $this->log(__METHOD__);
        if(!$this->checkForIteratorNotEmpty($it)) return;

        $this->writeLine($fh, array('prefix_set_id', 'prefix'));

        foreach($it as $prefix_set) {
            $prefix_set_id = $prefix_set->getId();

            foreach($prefix_set as $prefix) {
                $this->writeLine($fh, array($prefix_set_id, $prefix));
            }
        }
    }

    private function writeAccents($fh, $it) {
        $this->log(__METHOD__);
        if(!$this->checkForIteratorNotEmpty($it)) return;

        $this->writeLine($fh, array('accent_model_id', 'accent_vowel_pos'));

        foreach($it as $accent_model) {
            $accent_model_id = $accent_model->getId();

            foreach($accent_model as $accent) {
                $accent = $accent_model->isEmptyAccent($accent) ? null : $accent;
                $this->writeLine($fh, array($accent_model_id, $accent));
            }
        }
    }

    private function writeLemmas($fh, $it) {
        $this->log(__METHOD__);
        if(!$this->checkForIteratorNotEmpty($it)) return;

        $this->writeLine($fh, array('base', 'flexia_id', 'prefix_id', 'ancode_id'));
        $count = 0;

        foreach($it as $lemma) {
            $this->writeLine(
                $fh,
                array(
                     $lemma->getBase(),
                     $lemma->getFlexiaId(),
                     $lemma->hasPrefixId() ? $lemma->getPrefixId() : null,
                     $lemma->hasAncodeId() ? $lemma->getAncodeId() : null
                )
            );

            $count++;

            if(0 == ($count % self::DUMP_EVERY_LEMMA)) {
                $this->log("$count lemmas done");
            }
        }
    }

    private function writePoses($fh, $it) {
        $this->log(__METHOD__);
        if(!$this->checkForIteratorNotEmpty($it)) return;

        $this->writeLine($fh, array('pos_id', 'name', 'is_predict'));
        
        foreach($it as $pos) {
            $this->writeLine($fh, array($pos->getId(), $pos->getName(), $pos->isPredict()));
        }
    }

    private function writeGrammems($fh, $it) {
        $this->log(__METHOD__);
        if(!$this->checkForIteratorNotEmpty($it)) return;

        $this->writeLine($fh, array('grammem_id', 'name', 'shift'));

        foreach($it as $grammem) {
            $this->writeLine($fh, array($grammem->getId(), $grammem->getName(), $grammem->getShift()));
        }
    }

    private function writeAncodes($fh, $it) {
        $this->log(__METHOD__);
        if(!$this->checkForIteratorNotEmpty($it)) return;

        $this->writeLine($fh, array('id', 'name', 'pos_id', 'grammem_id'));

        foreach($it as $ancode) {
            $ancode_id = $ancode->getId();
            $ancode_name = $ancode->getName();
            $ancode_pos = $ancode->getPartOfSpeechId();

            foreach($ancode->getGrammemsIds() as $grammem_id) {
                $this->writeLine($fh, array($ancode_id, $ancode_name, $ancode_pos, $grammem_id));
            }
        }
    }

    private function checkForIteratorNotEmpty($it) {
        if(is_array($it)) {
            return count($it) > 0;
        } else if($it instanceof Traversable) {
            $it->rewind();
            return $it->valid();
        }

        throw new phpMorphy_Exception("Not traversable object given");
    }
}