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

    /**
     * @var int
     */
    private $_largestLineNumber;

    /**
     * @var bool|null
     */
    private $_isLoop;

    /**
     * @var bool|null
     */
    private $_isBraced;

    /**
     * @var Block[]
     */
    private $_previousBlocks;

    /**
     * @param Unit $unit
     */
    public function __construct (Unit $unit) {
        $this->_units = array($unit);
        $this->_setLastUnit($unit);
        $this->_processLinerNumber($unit);
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
            $this->_processLinerNumber($unit);
        }
        $this->_setLastUnit($unit);
        $this->_processIsBraced($unit);
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
        return $this->_lastUnit;
    }

    public function getUnits () {
        return $this->_units;
    }

    /**
     * @param Block[] $blocks
     */
    public function setPreviousBlocks (array $blocks) {
        $this->_previousBlocks = $blocks;
    }

    /**
     * @param Unit[] $units
     */
    private function _setUnits (array $units) {
        $this->_units = $units;
    }

    /**
     * @param Unit $newUnit
     * @return bool
     */
    public function blockIsOpen (Unit $newUnit) {
        if ($this->_currentBlockIsLoop($newUnit)){
            if ($this->_unitIsLoopEntry($newUnit)) {
                return true;
            } else {
                $this->_moveClosingUnitToStart();
                return false;
            }
        }

        if ($newUnit->isClosingBrace()) {
            if ($this->_blockIsBraced()) {
                $this->_moveClosingUnitToStart();
                return false;
            } else {
                $this->_aggregatePreviousBlocksBeforeOpenBrace();
                return true;
            }
        }

        return false;
    }

    /**
     * @param Unit $newUnit
     * @return bool
     */
    private function _currentBlockIsLoop (Unit $newUnit) {
        if (!is_null($this->_isLoop)) {
            return $this->_isLoop;
        }

        if ($this->_unitIsLoopEntry($newUnit)) {
            $this->_isLoop = true;
        }

        return $this->_isLoop;
    }

    private function _moveClosingUnitToStart () {
        $this->_updateClosingUnitBeforeMoveToStart();
        $units = $this->getUnits();
        $loopDeclareUnit = array_pop($units);
        array_unshift($units, $loopDeclareUnit);
        $this->_setUnits($units);
    }

    private function _updateClosingUnitBeforeMoveToStart () {
        $realLineForView = $this->getFirstUnit()->getLineNumber() - 1;
        $this->getLastUnit()->processNewLine($realLineForView);
    }

    /**
     * @return bool
     */
    private function _blockIsBraced () {
        if (is_null($this->_isBraced)) {
            return false;
        }

        return $this->_isBraced;
    }

    /**
     * @param Unit $unit
     * @return bool
     */
    private function _unitIsLoopEntry (Unit $unit) {
        return ($this->_largestLineNumber >= $unit->getLineNumber()
             && $this->getFirstUnit()->getDepth() == $unit->getDepth()
        );
    }

    /**
     * @param Unit $unit
     */
    private function _setLastUnit (Unit $unit) {
        $this->_lastUnit = $unit;
    }

    /**
     * @param Unit $unit
     */
    private function _processLinerNumber (Unit $unit) {
        if (is_null($this->_largestLineNumber) || $this->_largestLineNumber < $unit->getLineNumber()) {
            $this->_largestLineNumber = $unit->getLineNumber();
        }
    }

    /**
     * @param Unit $unit
     */
    private function _processIsBraced (Unit $unit) {
        if ($unit->isClosingBrace()) {
            $this->_isBraced = true;
        }
    }

    private function _aggregatePreviousBlocksBeforeOpenBrace () {
        $isOpenBrace = false;
        /* @var $prevBlock Block */
        $prevBlock = array_pop($this->_previousBlocks);
        while(!$isOpenBrace && !is_null($prevBlock)) {
            $units = $prevBlock->getUnits();
            if (count($units) === 1) {
                $isOpenBrace = $units[0]->isOpeningBrace();
            }
            $prevBlock = array_pop($this->_previousBlocks);
        }
    }

}