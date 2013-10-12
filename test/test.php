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
        echo "\nfunc1 starts";
        if (true) {
            $this->_some1 = 'First line of two line if';
            $this->_some1 = 'Braced block body';
        }

        $this->_some1 = 4;

        $this->func2();
        echo "\n//func1";
    }

    public function func2 () {
        echo "\nfunc2 starts";
        for($i = 0; $i < 2; $i++)
            $this->_some2 = $i;

        $c = 1;
        $d = 2;
        $this->func3($c, $d);
        echo "\n//func2";
    }

    public function func3 ($a, $b) {
        echo "\nfunc3 starts";
        for($i = 0; $i < 2; $i++) {
            $this->some3 = 'Two lines braced loop';
            $this->_some3 = $i;
        }
        echo "\n//func3";
    }
}

echo "<pre>";

Profiler::start();

$test = new test();
$test->func1();

$result = Profiler::stop();


function render($string, $tabCount) {
    echo str_repeat('— — ', $tabCount) . '> ' . $string . "\n";
}
/* @var $call FTrace\Utils\Code\Call */
foreach($result['calls'] as $call) {
    echo "- call start -\n";
    $blocks = $call->getBlocks();
    $openCallBlock = array_shift($blocks); /* @var $openCallBlock FTrace\Utils\Code\Block */
    render($openCallBlock->getFirstUnit()->getLineView(), 1);
    foreach($blocks as $block) { /* @var $block FTrace\Utils\Code\Block */
        $units = $block->getUnits();
        foreach($units as $unit) {
            render($unit->getLineView(), 2);
        }
    }
}

include_once '/home/quiver/sources/kint/Kint.class.php';
Kint::dump($result['debug']);