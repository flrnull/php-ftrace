<?php

/**
 * @author Evgeniy Udodov <flr.null@gmail.com>
 */

namespace FTrace\Utils\Code;

class Call {

    /**
     * @var bool
     */
    private $_isRoot;

    /**
     * @var int
     */
    private $_depth;

    /**
     * @var Unit[]
     */
    private $_units;

    /**
     * @param bool $isRoot
     * @param array $units
     */
    public function __construct ($isRoot = false, array $units = array()) {
        $this->_isRoot = $isRoot;
        $this->_units = $units;
        $this->_extractDepthFromUnits($units);
    }

    /**
     * @param Unit $unit
     */
    public function addUnit (Unit $unit) {
        if ($this->isEmpty()) {
            echo "\n\nCall: call is empty, add unit to units : " . $unit->getLineView() . "\n\n";
            $this->_units[] = $unit;
            $this->_depth = $unit->getDepth();
            return;
        }

        $newUnitDepth = $unit->getDepth();
        $lastUnit = $this->getLastUnit();

        if ($newUnitDepth > $this->_depth) {

            echo "\nCall: unit is deeper ({$newUnitDepth} > ".$this->_depth."), check last unit\n";
            if ($lastUnit->isMock()) {
                echo "\nCall: last unit is mock, add new unit to it's call\n";
                $lastUnit->getCall()->addUnit($unit);
            } else {
                echo "\nCall: last unit is not mock, create mock\n";
                $mockUnit = Unit::getMockForCall($this->_depth);

                echo "\nCall: create new call in mock\n";
                $mockUnit->initCall($unit);

                echo "\nCall: add mock to units\n";
                $this->_units[] = $mockUnit;
            }

        } elseif ($newUnitDepth === $this->_depth) {

            echo "\nCall: unit depth ({$newUnitDepth}) is the same depth as call (" . $this->_depth . ")\n";
            if ($lastUnit->isMock()) {
                echo "\nCall: last unit is mock, replace it\n";
                $lastUnit->mockReplaceWithUnit($unit);
            } else {
                echo "\nCall: last unit is not mock, add to units\n";
                $this->_units[] = $unit;
            }
        } else {
            echo "\nCall: unit is out of scope, skip\n";
            return;
        }
    }

    /**
     * @return bool
     */
    public function isRoot () {
        return $this->_isRoot;
    }

    /**
     * @return bool
     */
    public function isEmpty () {
        return (count($this->_units) === 0);
    }

    /**
     * @return Unit[]
     */
    public function getUnits () {
        return $this->_units;
    }

    /**
     * @return Unit
     */
    public function getLastUnit () {
        $unitCount = count($this->_units);
        return $this->_units[--$unitCount];
    }

    /**
     * @param Unit[] $units
     */
    private function _extractDepthFromUnits (array $units) {
        if (count($units) > 0)
            $this->_depth = $units[0]->getDepth();
    }

}