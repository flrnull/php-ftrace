<?php

/**
 * @author Evgeniy Udodov <flr.null@gmail.com>
 */

namespace Profiler;

use Profiler\Utils\Time;
use Profiler\Utils\Trace;

class Profiler {

    /**
     * @var Profiler
     */
    private static $_instance;

    /**
     * Trace levels to skip
     */
    const TRACE_SKIP_LEVEL_COUNT = 2;

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
        self::instance()->startHandler();
        register_tick_function(array(&self::$_instance, 'tickHandler'));
    }

    /**
     * Stops profiler
     *
     * @return array
     */
    public static function stop () {
        unregister_tick_function(array(&self::$_instance, 'tickHandler'));
        self::$_instance->stopHandler();
        return self::$_instance->_result;
    }

    public function startHandler () {
        $this->_initResultStructure();
        Time::start('__profiler');
    }

    public function stopHandler () {
        $GLOBALS['__profiler']['time_passed'] = Time::stop('__profiler');
    }

    public function tickHandler () {
        $trace = new Trace(self::TRACE_SKIP_LEVEL_COUNT);
        if ($this->_tickIsInternal($trace)) {
            return;
        }

        //echo '<-------->', "\n";
        //var_dump(debug_backtrace());
        var_dump($trace->getData());
        //echo '</-------->', "\n";
        $this->_result['counter']++;
    }

    /**
     * Is tick is inside Profiler
     *
     * @param Trace $trace
     * @return bool
     */
    private function _tickIsInternal (Trace $trace) {
        return $trace->getLastClassName() === __CLASS__;
    }

    /**
     * Inits result structure
     */
    private function _initResultStructure () {
        $this->_result = array(
            'counter'   => 0,
            'deeps'     => array(),
        );
    }

}