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

class phpMorphy_Dict_Writer_Observer_Standart implements phpMorphy_Dict_Writer_Observer_ObserverInterface {
    protected
        $start_time;

    function __construct($callback) {
        if(!is_callable($callback)) {
            throw new Exception("Invalid callback");
        }

        $this->callback = $callback;
    }

    function onStart() {
        $this->start_time = microtime(true);
    }

    function onEnd() {
        $this->writeMessage(sprintf("Total time = %f", microtime(true) - $this->start_time));
    }

    function onLog($message) {
        $this->writeMessage(sprintf("+%0.2f %s", microtime(true) - $this->start_time, $message));
    }

    protected function writeMessage($msg) {
        call_user_func($this->callback, $msg);
    }
}