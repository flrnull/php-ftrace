<?php

/**
 * @author Evgeniy Udodov <flr.null@gmail.com>
 */

declare(ticks = 1);

include_once __DIR__ . '/../lib/Profiler.php';
include_once __DIR__ . '/../lib/Utils/Time.php';
include_once __DIR__ . '/../lib/Utils/Trace.php';
include_once __DIR__ . '/../lib/Utils/TraceObject.php';
include_once __DIR__ . '/../lib/Utils/File.php';

use FTrace\Profiler;

class test {

    private $_some;

    public function __construct () {
        $x = 1;
    }

    public function func1 () {
        for($i = 0; $i < 2; $i++) {
            $this->_some = $i;
        }
        echo "Func1\n";
        $this->func2();
    }

    public function func2 () {
        for($i = 0; $i < 2; $i++) {
            $this->_some = $i;
        }
        echo "Func2\n";
        $c = 1;
        $d = 2;
        $this->func3($c, $d);
    }

    public function func3 ($a, $b) {
        for($i = 0; $i < 2; $i++) {
            $this->_some = $i;
        }
        echo "Func3\n";
    }
}

Profiler::start();

$test = new test();
$test->func1();

$result = Profiler::stop();

var_dump($result);

