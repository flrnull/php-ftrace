<?php

/**
 * @author Evgeniy Udodov <flr.null@gmail.com>
 */

namespace FTrace\Utils;

class Trace {

    /**
     * @var string
     */
    private $_file;
    /**
     * @var string
     */
    private $_line;
    /**
     * @var string
     */
    private $_function;
    /**
     * @var string
     */
    private $_class;
    /**
     * @var mixed
     */
    private $_object;
    /**
     * @var string
     */
    private $_type;
    /**
     * @var array
     */
    private $_args;
    /**
     * @var string
     */
    private $_line_view;

    /**
     * @var array
     */
    private $_traceSource;

    /**
     * @param array $traceStack debug_backtrace() process
     */
    public function __construct (array $traceStack) {
        $processedTrace = $this->_preProcessTraceStack($traceStack);
        foreach($processedTrace[0] as $param => $value) {
            $localParam = "_{$param}";
            $this->$localParam = $value;
        }
        $this->_traceSource = $traceStack;
    }

    /**
     * Fills extra trace data
     *
     * @param array $traceStack
     * @return array
     */
    private function _preProcessTraceStack (array $traceStack) {
        foreach($traceStack as $index => $trace) {
            if (!array_key_exists('file', $trace)) {
                continue;
            }
            $traceStack[$index]['line_view'] = $this->_getStringFromFile($trace['file'], $trace['line']);
        }
        return $traceStack;
    }

    /**
     * @param string $fileName
     * @param string $lineNumber
     * @return string
     */
    private function _getStringFromFile ($fileName, $lineNumber) {
        $string = File::getStringFromFile($fileName, $lineNumber);
        return $string;
    }


    /**
     * @return array
     */
    public function getArgs () {
        return $this->_args;
    }

    /**
     * @return string
     */
    public function getClass () {
        return $this->_class;
    }

    /**
     * @return string
     */
    public function getFile () {
        return $this->_file;
    }

    /**
     * @return string
     */
    public function getFunction () {
        return $this->_function;
    }

    /**
     * @return string
     */
    public function getLine () {
        return $this->_line;
    }

    /**
     * @return string
     */
    public function getLineView () {
        return $this->_line_view;
    }

    /**
     * @return mixed
     */
    public function getObject () {
        return $this->_object;
    }

    /**
     * @return string
     */
    public function getType () {
        return $this->_type;
    }

    /**
     * @return array
     */
    public function getTraceSource () {
        return $this->_traceSource;
    }

}