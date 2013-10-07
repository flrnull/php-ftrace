<?php

/**
 * @author Evgeniy Udodov <flr.null@gmail.com>
 */

declare(ticks = 1);
include_once __DIR__ . '/../lib/Profiler.php';
use FTrace\Profiler;

class test {

    private $_some1;
    private $_some2;
    private $_some3;


    public function __construct () {
        $x = 1;
    }

    public function func1 () {
        for($i = 0; $i < 2; $i++)
            $this->_some1 = $i;
        echo "Func1\n";
        $this->func2();
    }

    public function func2 () {
        for($i = 0; $i < 2; $i++) {
            $this->_some2 = $i;
        }
        echo "Func2\n";
        $c = 1;
        $d = 2;
        $this->func3($c, $d);
    }

    public function func3 ($a, $b) {
        for($i = 0; $i < 2; $i++) {
            $this->_some3 = $i;
        }
        echo "Func3\n";
    }
}

Profiler::start();

$test = new test();
$test->func1();

$result = Profiler::stop();

var_dump($result);

