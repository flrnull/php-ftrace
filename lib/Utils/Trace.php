<?php

/**
 * @author Evgeniy Udodov <flr.null@gmail.com>
 */

namespace FTrace\Utils;

class Trace {

    private $_trace;

    /**
     * @var array
     */
    private static $_stringsCache;

    /**
     * @param int $skipLevelCount 1 means skip only Trace construct
     */
    public function __construct ($skipLevelCount = 1) {
        $this->_trace = array_slice(debug_backtrace(), $skipLevelCount);
        $this->_processTrace();
    }

    /**
     * @return int
     */
    public function getCurrentDeep () {
        return count($this->_trace);
    }

    /**
     * Get latest parent's class name
     *
     * @return string|null
     */
    public function getLastFileName () {
        return isset($this->_trace[0]['file'])
               ? $this->_trace[0]['file']
               : null;
    }

    /**
     * @return array
     */
    public function getData () {
        return $this->_trace;
    }

    /**
     * Fills extra trace data
     */
    private function _processTrace () {
        foreach($this->_trace as $index => $trace) {
            $this->_trace[$index]['line_view'] = trim($this->_getStringFromFile($trace['file'], $trace['line']));
        }

    }

    /**
     * @param string $fileName
     * @param string $lineNumber
     * @return string
     */
    private function _getStringFromFile ($fileName, $lineNumber) {
        if (is_null(self::$_stringsCache)) {
            self::$_stringsCache = array();
        }
        $key = "{$fileName}:{$lineNumber}";
        if (isset(self::$_stringsCache[$key])) {
            return self::$_stringsCache[$key];
        }
        $files = $this->_getTraceFiles();
        File::openFiles($files);
        $string = File::getStringFromFile($fileName, $lineNumber);
        File::closeFiles();
        self::$_stringsCache[$key] = $string;
        return $string;
    }

    private function _getTraceFiles () {
        return array_unique(array_map(function($trace) { return $trace['file']; }, $this->_trace));
    }

}