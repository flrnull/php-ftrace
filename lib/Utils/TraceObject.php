<?php

/**
 * @author Evgeniy Udodov <flr.null@gmail.com>
 */

namespace ForwardTrace\Utils;

class TraceObject {

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
     * @param array $trace Processed Trace object data
     */
    public function __construct (array $trace) {
        foreach($trace[0] as $param => $value) {
            $localParam = "_{$param}";
            $this->$localParam = $value;
        }
        $this->_traceSource = $trace;
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