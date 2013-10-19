<?php

/**
 * @author Evgeniy Udodov <flr.null@gmail.com>
 */

namespace FTrace\Code;

use FTrace\Utils\Trace;
use FTrace\Utils\Time;
use FTrace\Utils\File;

class Unit {

    private $_lineNumber;
    private $_lineView;
    private $_depth;
    private $_call;
    private $_createMicroTime;
    private $_obTrace;

    /**
     * @param Trace $obTrace
     */
    public function __construct (Trace $obTrace) {
        $this->_fillVarsFromTrace($obTrace);
    }

    /**
     * @param Trace $obTrace
     */
    private function _fillVarsFromTrace (Trace $obTrace) {
        $this->_lineNumber = (int)$obTrace->getLine();
        $this->_lineView = trim($obTrace->getLineView());
        $this->_depth = count($obTrace->getTraceSource());
        $this->_createMicroTime = Time::getFloatTime();
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

    /**
     * @return string
     */
    public function getLineView () {
        return $this->_lineView;
    }

    /**
     * @return mixed
     */
    public function getTrace () {
        return $this->_obTrace;
    }

    /**
     * @param int $depth
     * @return Unit
     */
    public static function getMockForCall ($depth) {
        $traceMock = array();
        for($i = 1; $i <= $depth; $i++) {
            $traceMock[] = array('object' => 'Mock');
        }
        return new self(new Trace($traceMock));
    }

    /**
     * @return Call
     */
    public function getCall () {
        return $this->_call;
    }

    /**
     * @return bool
     */
    public function isCall () {
        return ($this->_call !== null);
    }

    /**
     * @return bool
     */
    public function isMock () {
        return (is_null($this->_obTrace->getLine()) && is_null($this->_obTrace->getLineView()));
    }

    /**
     * @param Unit $unit
     */
    public function initCall (Unit $unit) {
        $this->_call = new Call(false, array($unit));
    }

    /**
     * @param Unit $unit
     */
    public function mockReplaceWithUnit (Unit $unit) {
        $this->_fillVarsFromTrace($unit->getTrace());
    }

}