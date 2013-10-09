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
        if ($unit->getDeep() > $prevUnit->getDeep()) {
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

    /**
     * @return Unit
     */
    public function getPreviousUnit () {
        throw new \Exception("Not imp");
    }

    public function getUnits () {
        return $this->_units;
    }

    private function _setLastUnit (Unit $unit) {
        $this->_lastUnit = $unit;
    }

}