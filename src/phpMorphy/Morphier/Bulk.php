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

// TODO: Fix this LSP violation!!! this class can`t implement phpMorphy_Morphier_MorphierInterface
class phpMorphy_Morphier_Bulk implements phpMorphy_Morphier_MorphierInterface {
    protected
        /** @var phpMorphy_Fsa_FsaInterface */
        $fsa,
        /** @var int */
        $root_trans,
        /** @var phpMorphy_Helper */
        $helper,
        /** @var array */
        $notfound = array(),
        /** @var phpMorphy_GramInfo_GramInfoInterface */
        $graminfo;

    /**
     * @param phpMorphy_Fsa_FsaInterface $fsa
     * @param phpMorphy_Helper $helper
     */
    function __construct(phpMorphy_Fsa_FsaInterface $fsa, phpMorphy_Helper $helper) {
        $this->fsa = $fsa;
        $this->root_trans = $fsa->getRootTrans();

        $this->helper = clone $helper;
        $this->helper->setAnnotDecoder($this->createAnnotDecoder($helper));

        $this->graminfo = $helper->getGramInfo();
    }

    /**
     * @return phpMorphy_Fsa_FsaInterface
     */
    function getFsa() {
        return $this->fsa;
    }

    /**
     * @return phpMorphy_Helper
     */
    function getHelper() {
        return $this->helper;
    }

    /**
     * @return phpMorphy_GramInfo_GramInfoInterface
     */
    function getGraminfo() {
        return $this->graminfo;
    }

    /**
     * @return array
     */
    function getNotFoundWords() {
        return $this->notfound;
    }

    /**
     * @param phpMorphy_Helper $helper
     * @return phpMorphy_AnnotDecoder_Common
     */
    protected function createAnnotDecoder(phpMorphy_Helper $helper) {
        return phpMorphy_AnnotDecoder_Factory::instance($helper->getEndOfString())->getCommonDecoder();
    }

    /**
     * @param string[] $words
     * @return array
     */
    function getAnnot($words) {
        $result = array();

        foreach($this->findWord($words) as $annot => $found_words) {
            $annot = $this->helper->decodeAnnot($annot, true);

            foreach($found_words as $word) {
                $result[$word][] = $annot;
            }
        }

        return $result;
    }

    /**
     * @param string[] $words
     * @return array
     */
    function getBaseForm($words) {
        $annots = $this->findWord($words);

        return $this->composeForms($annots, true, false, false);
    }

    /**
     * @param string[] $words
     * @return array
     */
    function getAllForms($words) {
        $annots = $this->findWord($words);

        return $this->composeForms($annots, false, false, false);
    }

    /**
     * @param string[] $words
     * @return array
     */
    function getPseudoRoot($words) {
        $annots = $this->findWord($words);

        return $this->composeForms($annots, false, true, false);
    }

    /**
     * @param string[] $words
     * @return array
     */
    function getPartOfSpeech($words) {
        $annots = $this->findWord($words);

        return $this->composeForms($annots, false, false, true);
    }

    /**
     * @param string[] $words
     * @param string $method
     * @param bool $passWordAsFirstArg
     * @return array
     */
    protected function processAnnotsWithHelper($words, $method, $passWordAsFirstArg = false) {
        $result = array();

        foreach($this->findWord($words) as $annot_raw => $words) {
            if($GLOBALS['__phpmorphy_strlen']($annot_raw) == 0) continue;

            if($passWordAsFirstArg) {
                foreach($words as $word) {
                    $result[$word] = $this->helper->$method($word, $annot_raw);
                }
            } else {
                $result_for_annot = $this->helper->$method($annot_raw);

                foreach($words as $word) {
                    $result[$word] = $result_for_annot;
                }
            }
        }

        return $result;
    }

    /**
     * @param string[] $words
     * @return array
     */
    function getAncode($words) {
        return $this->processAnnotsWithHelper($words, 'getAncode');
    }

    /**
     * @param string[] $words
     * @return array
     */
    function getGrammarInfoMergeForms($words) {
        return $this->processAnnotsWithHelper($words, 'getGrammarInfoMergeForms');
    }

    /**
     * @param string[] $words
     * @return array
     */
    function getGrammarInfo($words) {
        return $this->processAnnotsWithHelper($words, 'getGrammarInfo');
    }

    /**
     * @param string[] $words
     * @return array
     */
    function getAllFormsWithAncodes($words) {
        return $this->processAnnotsWithHelper($words, 'getAllFormsWithResolvedAncodes', true);
    }

    /**
     * @param string[] $words
     * @return array
     */
    function getParadigmCollection($words) {
        return $this->processAnnotsWithHelper($words, 'getParadigmCollection', true);
    }

    /**
     * @param string[] $words
     * @return array
     */
    protected function findWord($words) {
        $unknown_words_annot = '';

        $this->notfound = array();

        list($labels, $finals, $dests) = $this->buildPatriciaTrie($words);

        $annots = array();
        $unknown_words_annot = '';
        $stack = array(0, '', $this->root_trans);
        $stack_idx = 0;

        $fsa = $this->fsa;

        // TODO: Improve this
        while($stack_idx >= 0) {
            $n = $stack[$stack_idx];
            $path = $stack[$stack_idx + 1] . $labels[$n];
            $trans = $stack[$stack_idx + 2];
            $stack_idx -= 3; // TODO: Remove items from stack? (performance!!!)

            $is_final = $finals[$n] > 0;

            $result = false;
            if(false !== $trans && $n > 0) {
                $label = $labels[$n];

                $result = $fsa->walk($trans, $label, $is_final);

                if($GLOBALS['__phpmorphy_strlen']($label) == $result['walked']) {
                    $trans = $result['word_trans'];
                } else {
                    $trans = false;
                }
            }

            if($is_final) {
                if(false !== $trans && isset($result['annot'])) {
                    $annots[$result['annot']][] = $path;
                } else {
                    //$annots[$unknown_words_annot][] = $path;
                    $this->notfound[] = $path;
                }
            }

            if(false !== $dests[$n]) {
                foreach($dests[$n] as $dest) {
                    $stack_idx += 3;
                    $stack[$stack_idx] = $dest;
                    $stack[$stack_idx + 1] = $path;
                    $stack[$stack_idx + 2] = $trans;
                }
            }
        }

        return $annots;
    }

    /**
     * @param string[] $annotsRaw
     * @param bool $composeBase
     * @param bool $composePseudoRoot
     * @param bool $composePartOfSpeech
     * @return array
     */
    protected function composeForms(
        $annotsRaw, $composeBase, $composePseudoRoot, $composePartOfSpeech
    ) {
        $result = array();

        // process found annotations
        foreach($annotsRaw as $annot_raw => $words) {
            if($GLOBALS['__phpmorphy_strlen']($annot_raw) == 0) continue;

            foreach($this->helper->decodeAnnot($annot_raw, $composeBase) as $annot) {
                if(!($composeBase || $composePseudoRoot)) {
                    $flexias = $this->graminfo->readFlexiaData($annot);
                }

                $cplen = $annot['cplen'];
                $plen = $annot['plen'];
                $flen = $annot['flen'];

                if($composePartOfSpeech) {
                    $pos_id = $this->helper->extractPartOfSpeech($annot);
                }

                foreach($words as $word) {
                    if($flen) {
                        $base = $GLOBALS['__phpmorphy_substr']($word, $cplen + $plen, -$flen);
                    } else {
                        if($cplen || $plen) {
                            $base = $GLOBALS['__phpmorphy_substr']($word, $cplen + $plen);
                        } else {
                            $base = $word;
                        }
                    }

                    $prefix = $cplen ? $GLOBALS['__phpmorphy_substr']($word, 0, $cplen) : '';

                    if($composePseudoRoot) {
                        $result[$word][$base] = 1;
                    } else if($composeBase) {
                        $form = $prefix . $annot['base_prefix'] . $base . $annot['base_suffix'];

                        $result[$word][$form] = 1;
                    } else if($composePartOfSpeech) {
                        $result[$word][$pos_id] = 1;
                    } else {
                        for($i = 0, $c = count($flexias); $i < $c; $i += 2) {
                            $form = $prefix . $flexias[$i] . $base . $flexias[$i + 1];
                            $result[$word][$form] = 1;
                        }
                    }
                }
            }
        }

        for($keys = array_keys($result), $i = 0, $c = count($result); $i < $c; $i++) {
            $key = $keys[$i];

            $result[$key] = array_keys($result[$key]);
        }

        return $result;
    }

    /**
     * @throws phpMorphy_Exception
     * @param string[] $words
     * @return array
     */
    protected function buildPatriciaTrie($words) {
        if(!is_array($words)) {
            throw new phpMorphy_Exception("Words must be array");
        }

        sort($words);

        $stack = array();
        $prev_word = '';
        $prev_word_len = 0;
        $prev_lcp = 0;

        $state_labels = array();
        $state_finals = array();
        $state_dests = array();

        $state_labels[] = '';
        $state_finals = '0';
        $state_dests[] = array();

        $node = 0;

        foreach($words as $word) {
            if($word == $prev_word) {
                continue;
            }

            $word_len = $GLOBALS['__phpmorphy_strlen']($word);
            // find longest common prefix
            for($lcp = 0, $c = min($prev_word_len, $word_len); $lcp < $c && $word[$lcp] == $prev_word[$lcp]; $lcp++);

            if($lcp == 0) {
                $stack = array();

                $new_state_id = count($state_labels);

                $state_labels[] = $word;
                $state_finals .= '1';
                $state_dests[] = false;

                $state_dests[0][] = $new_state_id;

                $node = $new_state_id;
            } else {
                $need_split = true;
                $trim_size = 0; // for split

                if($lcp == $prev_lcp) {
                    $need_split = false;
                    $node = $stack[count($stack) - 1];
                } elseif($lcp > $prev_lcp) {
                    if($lcp == $prev_word_len) {
                        $need_split = false;
                    } else {
                        $need_split = true;
                        $trim_size = $lcp - $prev_lcp;
                    }

                    $stack[] = $node;
                } else {
                    $trim_size = $GLOBALS['__phpmorphy_strlen']($prev_word) - $lcp;

                    for($stack_size = count($stack) - 1; ;--$stack_size) {
                        $trim_size -= $GLOBALS['__phpmorphy_strlen']($state_labels[$node]);

                        if($trim_size <= 0) {
                            break;
                        }

                        if(count($stack) < 1) {
                            throw new phpMorphy_Exception('Infinite loop posible');
                        }

                        $node = array_pop($stack);
                    }

                    $need_split = $trim_size < 0;
                    $trim_size = abs($trim_size);

                    if($need_split) {
                        $stack[] = $node;
                    } else {
                        $node = $stack[$stack_size];
                    }
                }

                if($need_split) {
                    $node_key = $state_labels[$node];

                    // split
                    $new_node_id_1 = count($state_labels);
                    $new_node_id_2 = $new_node_id_1 + 1;

                    // new_node_1
                    $state_labels[] = $GLOBALS['__phpmorphy_substr']($node_key, $trim_size);
                    $state_finals .= $state_finals[$node];
                    $state_dests[] = $state_dests[$node];

                    // adjust old node
                    $state_labels[$node] = $GLOBALS['__phpmorphy_substr']($node_key, 0, $trim_size);
                    $state_finals[$node] = '0';
                    $state_dests[$node] = array($new_node_id_1);

                    // append new node, new_node_2
                    $state_labels[] = $GLOBALS['__phpmorphy_substr']($word, $lcp);
                    $state_finals .= '1';
                    $state_dests[] = false;

                    $state_dests[$node][] = $new_node_id_2;

                    $node = $new_node_id_2;
                } else {
                    $new_node_id = count($state_labels);

                    $state_labels[] = $GLOBALS['__phpmorphy_substr']($word, $lcp);
                    $state_finals .= '1';
                    $state_dests[] = false;

                    if(false !== $state_dests[$node]) {
                        $state_dests[$node][] = $new_node_id;
                    } else {
                        $state_dests[$node] = array($new_node_id);
                    }

                    $node = $new_node_id;
                }
            }

            $prev_word = $word;
            $prev_word_len = $word_len;
            $prev_lcp = $lcp;
        }

        return array($state_labels, $state_finals, $state_dests);
    }
}