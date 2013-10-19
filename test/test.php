<?php

/**
 * @author Evgeniy Udodov <flr.null@gmail.com>
 */

ini_set('display_errors', 1);

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
        if (true) {
            $this->_some1 = 'First line of two line if';
            $this->_some1 = 'Braced block body';
        }

        $this->_some1 = 4;

        $this->func2();
    }

    public function func2 () {
        for($i = 0; $i < 2; $i++)
            $this->_some2 = $i;

        $c = 1;
        $d = 2;
        $this->func3($c, $d);
    }

    public function func3 ($a, $b) {
        for($i = 0; $i < 2; $i++) {
            $this->some3 = 'Two lines braced loop';
            $this->_some3 = $i;
        }
    }
}

Profiler::start();

$test = new test();
$test->func1();

$result = Profiler::stop();

if (php_sapi_name() == 'cli') {
    $render = new \FTrace\Render\Cli();
} else {
    echo "<pre>";
    $render = new \FTrace\Render\Simple();
}
/* @var $unit FTrace\Code\Unit */
foreach($result['code']->getUnits() as $unit) {
    $render->render($unit);
}