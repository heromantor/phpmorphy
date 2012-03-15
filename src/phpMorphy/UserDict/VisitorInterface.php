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


interface phpMorphy_UserDict_VisitorInterface {
    /**
     * @param string $lexem
     * @param phpMorphy_UserDict_Pattern $pattern
     */
    function addLexem($lexem, phpMorphy_UserDict_Pattern $pattern);

    /**
     * @param phpMorphy_UserDict_Pattern $pattern
     * @param bool $deleteFromInternal
     * @param bool $deleteFromExternal
     */
    function deleteLexem(phpMorphy_UserDict_Pattern $pattern, $deleteFromInternal, $deleteFromExternal);

    /**
     *
     */
    function editLexem(phpMorphy_UserDict_XmlDiff_Command_Edit $command);
}