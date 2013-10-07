<?php

/**
 * @author Evgeniy Udodov <flr.null@gmail.com>
 */

namespace FTrace\Utils;

class CodeStack {

    private $_stack;

    public function __construct () {
        $this->_stack = array();
    }

    public function pushBlock (TraceObject $obTrace) {
        $this->_stack[] = $obTrace;
    }

    public function getTrace () {
        return array_pop($this->_stack);
    }

    public function blockCompleted () {
        return true;
    }

}