<?php

/**
 * @author Evgeniy Udodov <flr.null@gmail.com>
 */

namespace FTrace;

include_once __DIR__ . '/Utils/Time.php';
include_once __DIR__ . '/Utils/Trace.php';
include_once __DIR__ . '/Utils/File.php';
include_once __DIR__ . '/Code/Code.php';
include_once __DIR__ . '/Code/Unit.php';
include_once __DIR__ . '/Code/Call.php';
include_once __DIR__ . '/Render/Simple.php';
include_once __DIR__ . '/Render/Cli.php';

use FTrace\Utils\Time;
use FTrace\Utils\Trace;
use FTrace\Code\Code;

class Profiler {

    /**
     * Skip ticks in this file
     */
    const TRACE_FUNC_FILE = 'lib/ftrace.php';

    /**
     * @var Profiler
     */
    private static $_instance;

    /**
     * @var int
     */
    private $_depthLimit;

    /**
     * @var array
     */
    private $_result;

    /**
     * @var Code
     */
    private $_code;

    /**
     * @return Profiler
     */
    public static function instance () {
        if (is_null(self::$_instance))
            self::$_instance = new self();
        return self::$_instance;
    }

    /**
     * Starts profiler
     *
     * @param int $depthLimit
     */
    public static function start ($depthLimit = null) {
        self::instance()->startProfiling($depthLimit);
    }

    /**
     * Code trace
     */
    public function tickHandler () {
        $this->_tickSetStartTime();
        $obTrace = new Trace(debug_backtrace());

        if ($this->_tickIsInternal($obTrace) || $this->_invalidDepth($obTrace)) {
            $this->_tickSetFinishTime();
            return;
        }
        $this->_result['counter']++;

        $this->_code->pushCode($obTrace);

        $str = "\n";$str .= basename($obTrace->getFile());$str .= " : ";$str .= $obTrace->getLine();$str .= " (depth = " . count($obTrace->getTraceSource()) . ") : "; $str .= $obTrace->getLineView(); $str .= " "; $str .= (is_array($obTrace->getArgs())) ? json_encode($obTrace->getArgs()) : "";
        $this->_result['debug'][] = $str;

        $this->_tickSetFinishTime();
    }

    /**
     * Stops profiler
     *
     * @return array
     */
    public static function stop () {
        self::$_instance->stopProfiling();
        return self::$_instance->_result;
    }

    /**
     * @param int $depthLimit
     */
    public function startProfiling ($depthLimit = null) {
        $this->_depthLimit = $depthLimit;
        $this->_initResultStructure();
        Time::start('__profiler_global');
        register_tick_function(array($this, 'tickHandler'));
    }

    public function stopProfiling () {
        unregister_tick_function(array($this, 'tickHandler'));
        $this->_result['time_passed'] = Time::stop('__profiler_global');
        $this->_result['code_time'] = (float)$this->_result['time_passed'] - (float)$this->_result['profiler_time'];
        $this->_result['code'] = $this->_code->getData();
    }

    private function _tickSetStartTime () {
        Time::start('__profiler_tick');
    }

    private function _tickSetFinishTime () {
        $tickTime = Time::stop('__profiler_tick');
        $this->_result['profiler_time'] = (float)$tickTime + (float)$this->_result['profiler_time'];
    }

    /**
     * Is tick is inside Profiler
     *
     * @param Trace $trace
     * @return bool
     */
    private function _tickIsInternal (Trace $trace) {
        return (is_null($trace->getFile()) || $trace->getFile() === __FILE__ || strpos($trace->getFile(), self::TRACE_FUNC_FILE) !== false);
    }

    /**
     * @param Trace $trace
     * @return bool
     */
    private function _invalidDepth (Trace $trace) {
        if (is_null($this->_depthLimit)) {
            return false;
        }

        $traceDepth = count($trace->getTraceSource());
        if ($this->_depthLimit >= $traceDepth) {
            return false;
        }

        return true;
    }

    /**
     * Inits result structure
     */
    private function _initResultStructure () {
        $this->_code = new Code();
        $this->_result = array(
            'counter'       => 0,
            'time_passed'   => 0,
            'profiler_time' => 0,
            'code_time'     => 0,
            'code'          => array(),
            'debug'         => "",
        );
    }

    /**
     * @param $obTrace
     * @throws ProfilerException
     */
    private function _throwException ($obTrace) {
        throw new ProfilerException(
            "Code uncompleted blocks " . $obTrace->getFile() . ":" . $obTrace->getLine()
            . "\n" . $obTrace->getLineView() . "\n"
        );
    }

}

class ProfilerException extends \Exception {}