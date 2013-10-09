<?php

/**
 * @author Evgeniy Udodov <flr.null@gmail.com>
 */

namespace FTrace\Utils\Code;

use FTrace\Utils\Trace;

class Unit {

    private $_lineNumber;
    private $_lineView;
    private $_deep;
    private $_call;
    private $_obTrace;

    /**
     * @param Trace $obTrace
     */
    public function __construct (Trace $obTrace) {
        $this->_lineNumber = (int)$obTrace->getLine();
        $this->_lineView = $obTrace->getLineView();
        $this->_deep = count($obTrace->getTraceSource());
        $this->_call = null;
        $this->_obTrace = $obTrace;
    }

    /**
     * @return int
     */
    public function getDeep () {
        return $this->_deep;
    }

    /**
     * @return int
     */
    public function getLineNumber () {
        return $this->_lineNumber;
    }

    public function initCall (Unit $unit) {

    }

}