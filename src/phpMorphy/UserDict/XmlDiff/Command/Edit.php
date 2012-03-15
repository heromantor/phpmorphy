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




class phpMorphy_UserDict_XmlDiff_Command_Edit {
    /** @var phpMorphy_UserDict_Pattern */
    protected $pattern;
    /** @var phpMorphy_UserDict_EditCommand_CommandAbstract[] */
    protected $commands = array();

    /**
     * @param phpMorphy_UserDict_Pattern $pattern
     * @return phpMorphy_UserDict_XmlDiff_Command_Edit
     */
    public function setPattern(phpMorphy_UserDict_Pattern $pattern) {
        $this->pattern = $pattern;
        return $this;
    }

    /**
     * @return phpMorphy_UserDict_Pattern
     */
    public function getPattern() {
        return $this->pattern;
    }

    /**
     * @param phpMorphy_UserDict_EditCommand_CommandAbstract $command
     * @return phpMorphy_UserDict_XmlDiff_Command_Edit
     */
    public function appendCommand(phpMorphy_UserDict_EditCommand_CommandAbstract $command) {
        $this->commands[] = $command;
        return $this;
    }

    /**
     * @param phpMorphy_Paradigm_MutableDecorator $paradigm
     * @return phpMorphy_Paradigm_MutableDecorator
     */
    public function apply(phpMorphy_Paradigm_MutableDecorator $paradigm) {
        foreach($this->commands as $command) {
            $command->apply($paradigm);
        }

        return $paradigm;
    }
}