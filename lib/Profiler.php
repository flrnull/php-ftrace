<?php

/**
 * @author Evgeniy Udodov <flr.null@gmail.com>
 */

namespace FTrace;

include_once __DIR__ . '/Utils/Time.php';
include_once __DIR__ . '/Utils/Trace.php';
include_once __DIR__ . '/Utils/TraceObject.php';
include_once __DIR__ . '/Utils/File.php';
include_once __DIR__ . '/Utils/CodeStack.php';

use FTrace\Utils\CodeStack;
use FTrace\Utils\Time;
use FTrace\Utils\Trace;
use FTrace\Utils\TraceObject;

class Profiler {

    /**
     * @var Profiler
     */
    private static $_instance;

    /**
     * @var array
     */
    private $_result;

    /**
     * @var CodeStack
     */
    private $_codeStack;

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
     */
    public static function start () {
        self::instance()->startProfiling();
        register_tick_function(array(&self::$_instance, 'tickHandler'));
    }

    /**
     * Stops profiler
     *
     * @return array
     */
    public static function stop () {
        unregister_tick_function(array(&self::$_instance, 'tickHandler'));
        self::$_instance->stopProfiling();
        return self::$_instance->_result;
    }

    public function startProfiling () {
        $this->_initResultStructure();
        Time::start('__profiler_global');
    }

    public function stopProfiling () {
        $this->_result['time_passed'] = Time::stop('__profiler_global');
        $this->_result['code_time'] = (float)$this->_result['time_passed'] - (float)$this->_result['profiler_time'];
    }

    public function tickHandler () {
        $this->_tickStartTime();
        $this->_result['counter']++;
        $trace = new Trace();

        if ($this->_tickIsInternal($trace)) {
            $this->_tickEndTime();
            return;
        }

        $obTrace = new TraceObject($trace->getData());
        $code = $this->_codeStack;

        $code->pushBlock($obTrace);
        if ($code->blockCompleted()) {
            $this->_addToResult($code);
        }

        $this->_tickEndTime();
    }

    private function _tickStartTime () {
        Time::start('__profiler_tick');
    }

    private function _tickEndTime () {
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
        return $trace->getLastFileName() === __FILE__;
    }

    /**
     * @param TraceObject $obTrace
     */
    private function _addToResult (CodeStack $code) {
        $obTrace = $code->getTrace();
        $string = "\n";
        $string .=$obTrace->getFile() . ":" . $obTrace->getLine();
        $string .="\n";
        $string .=$obTrace->getLineView();
        $string .="\n";
        $this->_result['result'] .= $string;
    }

    /**
     * Inits result structure
     */
    private function _initResultStructure () {
        $this->_codeStack = new CodeStack();
        $this->_result = array(
            'counter'       => 0,
            'time_passed'   => 0,
            'profiler_time' => 0,
            'code_time'     => 0,
            'result'        => "",
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