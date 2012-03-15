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

abstract class phpMorphy_Dict_Source_Xml_SectionAbstract implements Iterator {
    private
        $reader,
        $section_name,
        $xml_file;

    function __construct($xmlFile) {
        $this->xml_file = $xmlFile;
        $this->section_name = $this->getSectionName();
    }

    private function createReader() {
        $reader = new XMLReader();
        if(false === ($reader->open($this->xml_file))) {
            throw new Exception("Can`t open '$this->xml_file' xml file");
        }

        return $reader;
    }

    private function closeReader() {
        $this->reader->close();
        $this->reader = null;
    }

    private function getReader($section) {
        $reader = $this->createReader();

        while($reader->read()) {
            if($reader->localName === 'options') {
                break;
            }
        }

        if($section !== 'options') {
            if(false === ($reader->next($section))) {
                //throw new Exception("Can`t seek to '$section' element in '{$this->xml_file}' file");
            }
        }

        return $reader;
    }

    function current() {
        return $this->getCurrentValue();
    }

    function next() {
        $this->readNext($this->reader);
        /*
        if($this->valid()) {
            $this->read();
        }
        */
    }

    function key() {
        return $this->getCurrentKey();
    }

    function rewind() {
        if(!is_null($this->reader)) {
            $this->reader->close();
        }

        $this->reader = $this->getReader($this->section_name);

        $this->next();
    }

    function valid() {
        return !is_null($this->reader);
    }

    protected function read() {
        if(
            !$this->reader->read() ||
            ($this->reader->nodeType == XMLReader::END_ELEMENT && $this->reader->localName === $this->section_name)
        ) {
            $this->closeReader();
            return false;
        }

        return true;
    }

    protected function isStartElement($name) {
        return $this->reader->nodeType == XMLReader::ELEMENT && $this->reader->localName === $name;
    }

    protected function isEndElement($name) {
        return
            ($this->reader->nodeType == XMLReader::ELEMENT || $this->reader->nodeType == XMLReader::END_ELEMENT)&&
            $this->reader->localName === $name;
    }

    abstract protected function getSectionName();
    abstract protected function readNext(XMLReader $reader);
    abstract protected function getCurrentKey();
    abstract protected function getCurrentValue();
}