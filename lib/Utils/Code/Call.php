<?php

/**
 * @author Evgeniy Udodov <flr.null@gmail.com>
 */

namespace FTrace\Utils\Code;

class Call {

    private $_stack;

    public function __construct () {
        $this->_stack = array();
    }

    public function getStack () {
        return $this->_stack;
    }

}