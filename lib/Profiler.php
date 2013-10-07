<?php

/**
 * @author Evgeniy Udodov <flr.null@gmail.com>
 */

namespace FTrace;

include_once __DIR__ . '/Utils/Time.php';
include_once __DIR__ . '/Utils/Trace.php';
include_once __DIR__ . '/Utils/TraceObject.php';
include_once __DIR__ . '/Utils/File.php';
include_once __DIR__ . '/Utils/Code.php';
include_once __DIR__ . '/Utils/Code/Block.php';
include_once __DIR__ . '/Utils/Code/Unit.php';
include_once __DIR__ . '/Utils/Code/Call.php';

use FTrace\Utils\Time;
use FTrace\Utils\Trace;
use FTrace\Utils\TraceObject;
use FTrace\Utils\Code;

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
        $this->_result['calls'] = $this->_code->getCalls();
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
        $this->_code->pushCode($obTrace);

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
     * Inits result structure
     */
    private function _initResultStructure () {
        $this->_code = new Code();
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