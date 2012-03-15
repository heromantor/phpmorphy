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

class phpMorphy_Dict_Source_ValidatingSource
    extends phpMorphy_Dict_Source_Decorator
    implements phpMorphy_Dict_Source_NormalizedAncodesInterface
{
    /** @var phpMorphy_Dict_Source_ValidatingSource_ValidatorInterface */
    private $validator;

    function __construct(phpMorphy_Dict_Source_SourceInterface $object) {
        parent::__construct($object);
        $this->validator = new phpMorphy_Dict_Source_ValidatingSource_Validator();
    }

    protected function setDecorateeObject(phpMorphy_Dict_Source_SourceInterface $object) {
        return parent::setDecorateeObject(
            phpMorphy_Dict_Source_NormalizedAncodes::wrap($object)
        );
    }

    static function wrap(phpMorphy_Dict_Source_SourceInterface $source) {
        if($source instanceof phpMorphy_Dict_Source_ValidatingSource) {
            return $source;
        }

        return new phpMorphy_Dict_Source_ValidatingSource($source);
    }

    /**
     * @return Iterator over objects of phpMorphy_Dict_Lemma
     */
    function getLemmas() {
        $validator = $this->validator;

        if(count($validator->getAncodesValidator()) < 1) {
            iterator_count($this->getAncodes());
        }

        if(count($validator->getPrefixesValidator()) < 1) {
            iterator_count($this->getPrefixes());
        }

        if(count($validator->getFlexiasValidator()) < 1) {
            iterator_count($this->getFlexias());
        }

        return new phpMorphy_Dict_Source_ValidatingSource_Iterator(
            parent::getLemmas(),
            function(phpMorphy_Dict_Lemma $lemma) use ($validator) {
                $flexia_id = $lemma->getFlexiaId();

                if(!$validator->validateFlexiaId($flexia_id)) {
                    throw new phpMorphy_Exception("Unknown flexia_id '$flexia_id' found");
                }

                if($lemma->hasPrefixId()) {
                    $prefix_id = $lemma->getPrefixId();

                    if(!$validator->validatePrefixId($prefix_id)) {
                        throw new phpMorphy_Exception("Unknown prefix_id '$prefix_id' found");
                    }
                }

                if($lemma->hasAncodeId()) {
                    $ancode_id = $lemma->getAncodeId();

                    if(!$validator->validateAncodeId($ancode_id)) {
                        throw new phpMorphy_Exception("Unknown common_ancode_id '$ancode_id' found");
                    }
                }
            }
        );
    }

    /**
     * @return Iterator over objects of phpMorphy_Dict_AccentModel
     */
    function getAccents() {
        return parent::getAccents();
    }

    /**
     * @return Iterator over objects of phpMorphy_Dict_PrefixSet
     */
    function getPrefixes() {
        $section = $this->validator->getPrefixesValidator();

        return new phpMorphy_Dict_Source_ValidatingSource_Iterator(
            parent::getPrefixes(),
            function(phpMorphy_Dict_PrefixSet $prefixSet) use ($section) {
                $section->insertId($prefixSet->getId());
            }
        );
    }

    /**
     * @return Iterator over objects of phpMorphy_Dict_FlexiaModel
     */
    function getFlexias() {
        $validator = $this->validator;
        $section = $validator->getFlexiasValidator();

        if(count($validator->getAncodesValidator()) < 1) {
            iterator_count($this->getAncodes());
        }
        
        return new phpMorphy_Dict_Source_ValidatingSource_Iterator(
            parent::getFlexias(),
            function(phpMorphy_Dict_FlexiaModel $flexiaModel) use ($validator, $section) {
                $section->insertId($flexiaModel->getId());

                foreach($flexiaModel as $flexia) {
                    $ancode_id = $flexia->getAncodeId();

                    if(!$validator->validateAncodeId($ancode_id)) {
                        throw new Exception("Unknown ancode_id '$ancode_id' found");
                    }
                }
            }
        );
    }

    /**
     * @return Iterator over objects of phpMorphy_Dict_Ancode
     */
    function getAncodes() {
        $section = $this->validator->getAncodesValidator();

        return new phpMorphy_Dict_Source_ValidatingSource_Iterator(
            parent::getAncodes(),
            function(phpMorphy_Dict_Source_NormalizedAncodes_Ancode $ancode) use ($section) {
                $section->insertId($ancode->getId());
            }
        );
    }

    function getGrammems() {
        return $this->getDecorateeObject()->getGrammems();
    }

    function getPoses() {
        return $this->getDecorateeObject()->getPoses();
    }
}