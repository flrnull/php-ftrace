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
        echo "\nfunc1 starts";
        if (true)
            $this->_some1 = 'Braced block body';

        $this->_some1 = 4;

        $this->func2();
        echo "\n//func1";
    }

    public function func2 () {
        echo "\nfunc2 starts";
        for($i = 0; $i < 2; $i++) {
            $this->_some2 = $i;
        }
        $c = 1;
        $d = 2;
        $this->func3($c, $d);
        echo "\n//func2";
    }

    public function func3 ($a, $b) {
        echo "\nfunc3 starts";
        for($i = 0; $i < 2; $i++) {
            $this->_some3 = $i;
        }
        echo "\n//func3";
    }
}

Profiler::start();

$test = new test();
$test->func1();

$result = Profiler::stop();

var_dump($result);

