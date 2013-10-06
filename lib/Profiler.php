<?php

/**
 * @author Evgeniy Udodov <flr.null@gmail.com>
 */

namespace Profiler;

use Profiler\Utils\Time;
use Profiler\Utils\Trace;
use Profiler\Utils\TraceObject;

class Profiler {

    /**
     * @var Profiler
     */
    private static $_instance;

    /**
     * @var array
     */
    private static $_previousTrace;

    /**
     * @var array
     */
    private $_result;

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

        if ($this->_tickIsInternal($trace) || $this->_tickIsSameAsPrevious($trace)) {
            $this->_tickEndTime();
            return;
        }

        $obTrace = new TraceObject($trace->getData());
        echo "\n";
        echo $obTrace->getFile() . ":" . $obTrace->getLine();
        echo "\n";
        echo $obTrace->getLineView();
        echo "\n";

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
     * @param Trace $trace
     * @return bool
     */
    private function _tickIsSameAsPrevious (Trace $trace) {
        if (is_null(self::$_previousTrace)) {
            self::$_previousTrace = array(
                'deep' => null,
                'file' => null,
                'line' => null,
            );
            return false;
        }
        $traceData = $trace->getData();
        $deep = count($traceData);

        if ($deep === self::$_previousTrace['deep']
         && $traceData[0]['file'] === self::$_previousTrace['file']
         && $traceData[0]['line'] === self::$_previousTrace['line']
        ) {
            $result = true;
        } else {
            $result = false;
        }

        self::$_previousTrace = array(
            'deep' => $deep,
            'file' => $traceData[0]['file'],
            'line' => $traceData[0]['line'],
        );
        return $result;
    }

    /**
     * Inits result structure
     */
    private function _initResultStructure () {
        $this->_result = array(
            'counter'       => 0,
            'time_passed'   => 0,
            'profiler_time' => 0,
            'code_time'     => 0,
        );
    }

}