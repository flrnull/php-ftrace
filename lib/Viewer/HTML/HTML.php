<?php

/**
 * @author Evgeniy Udodov <flr.null@gmail.com>
 */

namespace FTrace\Viewer;

use FTrace\Code\Unit;

class HTML {

    /**
     * @var array
     */
    private $_data;

    /**
     * @param array $traceData
     */
    public function __construct (array $traceData) {
        $this->_data = $traceData;
    }

    public function view () {
        $codeTime = $this->_data['code_time'];
        $codeCount = $this->_data['counter'];
        $profileTime = $this->_data['profiler_time'];
        $rootCall = $this->_data['code'];
        include_once __DIR__ . '/trace.html';
    }

    /**
     * @param float $time
     */
    private function _timeFormat ($time) {
        echo number_format($time, 6, ".", " ");
    }

    /**
     * @param Unit $unit
     */
    private function _renderUnit (Unit $unit) {
        if (trim($unit->getLineView()) == '}') {
            return;
        }
        include __DIR__ . '/unit.html';
        if ($unit->isCall()) {
            foreach($unit->getCall()->getUnits() as $childUnit) {
                $this->_renderUnit($childUnit);
            }
        }
    }

}