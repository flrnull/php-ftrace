<?php

/**
 * @author Evgeniy Udodov <flr.null@gmail.com>
 */

namespace FTrace\Utils\Code;

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
        $this->_call = null;
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
     * @param int $lineNumber
     */
    public function processNewLine ($lineNumber) {
        $file = $this->_obTrace->getFile();
        $this->_lineNumber = $lineNumber;
        $this->_lineView = File::getStringFromFile($file, $lineNumber);
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
     * @param Unit $unit
     * @param int $prevDepth
     */
    public function initCall (Unit $unit, $prevDepth = null) {
        $this->_call = Call::create($unit, $prevDepth);
    }

    /**
     * @return bool
     */
    public function isClosingBrace () {
        $braceInLine = $this->_stackReturnExtraBrace($this->_lineView, '{', '}');
        return $braceInLine === '}';
    }

    /**
     * @return bool
     */
    public function isOpeningBrace () {
        $braceInLine = $this->_stackReturnExtraBrace($this->_lineView, '{', '}');
        return $braceInLine === '{';
    }

    /**
     * @param int $prevDepth
     * @return bool
     */
    public function isCallFirstUnit ($prevDepth) {
        return ($this->getDepth() > $prevDepth);
    }

    /**
     * @param int $prevDepth
     * @return bool
     */
    public function isCallClosingUnit ($prevDepth) {
        return ($this->getDepth() < $prevDepth);
    }

    public function mockReplaceWithUnit (Unit $unit) {
        $this->_fillVarsFromTrace($unit->getTrace());
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
    public function isMock () {
        return (is_null($this->_obTrace->getLine()) && is_null($this->_obTrace->getLineView()));
    }

    /**
     * @param string $string
     * @param string $braceOpen
     * @param string $braceClose
     * @return string
     */
    private function _stackReturnExtraBrace ($string, $braceOpen, $braceClose) {
        $stack = array();
        $stringChars = str_split($string);
        foreach($stringChars as $char) {
            if ($char === $braceOpen) {
                array_push($stack, $char);
            } elseif ($char === $braceClose) {
                if (count($stack) === 0) {
                    return $braceClose;
                } else {
                    array_pop($stack);
                }
            }
        }
        if (count($stack) === 0) {
            return null;
        } else {
            return array_pop($stack);
        }
    }

}