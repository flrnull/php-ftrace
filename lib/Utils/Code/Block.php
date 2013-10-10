<?php

/**
 * @author Evgeniy Udodov <flr.null@gmail.com>
 */

namespace FTrace\Utils\Code;

class Block {

    /**
     * @var Unit[]
     */
    private $_units;

    /**
     * @var Unit
     */
    private $_lastUnit;

    public function __construct (Unit $unit) {
        $this->_units = array($unit);
        $this->_setLastUnit($unit);
    }

    /**
     * @param Unit $unit
     */
    public function addUnit (Unit $unit) {
        $prevUnit = $this->getLastUnit();
        if ($unit->getDepth() > $prevUnit->getDepth()) {
            $prevUnit->initCall($unit);
        } else {
            $this->_units[] = $unit;
        }
        $this->_setLastUnit($unit);
    }

    /**
     * @return Unit
     */
    public function getFirstUnit () {
        return $this->_units[0];
    }

    /**
     * @return Unit
     */
    public function getLastUnit () {
        $this->_lastUnit;
    }

    public function getUnits () {
        return $this->_units;
    }

    private function _setUnits (array $units) {
        $this->_units = $units;
    }

    public function blockIsOpen (Unit $newUnit) {
        if ($this->_currentBlockIsLoop()){
            if ($this->_unitIsLoopEntry($newUnit)) {
                return true;
            } else {
                $this->_moveLastUnitToStart();
                return false;
            }
        }

        if ($this->_blockIsBraced()) {
            return !$newUnit->isClosingBrace();
        }

        return false;
    }

    private function _currentBlockIsLoop () {
        throw new \Exception("Not imp");
    }

    private function _moveLastUnitToStart () {
        $units = $this->getUnits();
        $loopDeclareUnit = array_pop($units);
        array_unshift($units, $loopDeclareUnit);
        $this->_setUnits($units);
    }

    private function _blockIsBraced () {
        throw new \Exception("Not imp");
    }

    private function _unitIsLoopEntry (Unit $unit) {
        return $this->getLastUnit()->getLineNumber() >= $unit->getLineNumber();
    }

    private function _setLastUnit (Unit $unit) {
        $this->_lastUnit = $unit;
    }

}