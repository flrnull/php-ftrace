<?php

/**
 * @author Evgeniy Udodov <flr.null@gmail.com>
 */

namespace FTrace\Utils;

class Time {

    private static $_timers = array();

    /**
     * @param string $name
     */
    public static function start ($name = 'timer') {
        self::$_timers[$name] = array(
            'start_time' => microtime(),
        );
    }

    /**
     * @param string $name
     * @return float
     */
    public static function stop ($name = 'timer') {
        list($finishUSec, $finishSec) = explode(" ", microtime());
        list($startUSec, $startSec) = explode(" ", self::$_timers[$name]['start_time']);
        $secondsPassed = $finishSec - $startSec;
        $uSecondsPassed = $finishUSec - $startUSec;
        $timePassed = (float)$uSecondsPassed + (float)$secondsPassed;
        unset(self::$_timers[$name]);
        return $timePassed;
    }

    /**
     * @return float
     */
    public static function getFloatTime () {
        list($uSec, $sec) = explode(" ", microtime());
        return ((float)$uSec + (float)$sec);
    }

}