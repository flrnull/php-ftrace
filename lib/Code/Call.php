<?php

/**
 * @author Evgeniy Udodov <flr.null@gmail.com>
 */

namespace FTrace\Code;

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
            $this->_units[] = $unit;
            $this->_depth = $unit->getDepth();
            return;
        }

        $newUnitDepth = $unit->getDepth();
        $lastUnit = $this->getLastUnit();

        if ($newUnitDepth > $this->_depth) {
            if ($lastUnit->isMock()) {
                $lastUnit->getCall()->addUnit($unit);
            } else {
                $mockUnit = Unit::getMockForCall($this->_depth);
                $mockUnit->initCall($unit);
                $this->_units[] = $mockUnit;
            }

        } elseif ($newUnitDepth === $this->_depth) {
            if ($lastUnit->isMock()) {
                $lastUnit->mockReplaceWithUnit($unit);
            } else {
                $this->_units[] = $unit;
            }
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
            $this->_depth = ($this->_isRoot) ? $units[0]->getDepth()-1 : $units[0]->getDepth();
    }

}