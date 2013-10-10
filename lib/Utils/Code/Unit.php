<?php

/**
 * @author Evgeniy Udodov <flr.null@gmail.com>
 */

namespace FTrace\Utils\Code;

use FTrace\Utils\Trace;

class Unit {

    private $_lineNumber;
    private $_lineView;
    private $_depth;
    private $_call;
    private $_obTrace;

    /**
     * @param Trace $obTrace
     */
    public function __construct (Trace $obTrace) {
        $this->_lineNumber = (int)$obTrace->getLine();
        $this->_lineView = $obTrace->getLineView();
        $this->_depth = count($obTrace->getTraceSource());
        $this->_call = null;
        $this->_obTrace = $obTrace;
    }

    /**
     * @return int
     */
    public function getDepth () {
        return $this->_depth;
    }

    /**
     * @return int
     */
    public function getLineNumber () {
        return $this->_lineNumber;
    }

    public function initCall (Unit $unit) {
        throw new \Exception("Not imp");
    }

    public function isClosingBrace () {
        throw new \Exception("Not imp");
    }

}