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
     * @var Unit
     */
    private $_lastCallMock;

    /**
     * @var int
     */
    private $_largestLineNumber;

    /**
     * @var int
     */
    private $_depth;

    /**
     * @var int
     */
    private $_prevCallDepth;

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
     * @param Block[] $previousBlocks
     * @param int $prevCallDepth
     */
    public function __construct (Unit $unit, array $previousBlocks = array(), $prevCallDepth = null) {
        $this->_prevCallDepth = $prevCallDepth;
        $this->setPreviousBlocks($previousBlocks);
        $this->addUnit($unit);
    }

    /**
     * @param Unit $unit
     */
    public function addUnit (Unit $unit) {
        $prevUnit = $this->getLastUnit();
        if (is_null($prevUnit)) {
            echo "\nBlock: It's first unit of block, simple create block with one unit: " . $unit->getLineView() . " depth: " . $unit->getDepth() . "\n";
            $this->_depth = $unit->getDepth();
        }

        $prevDepth = is_null($prevUnit) ? $this->_prevCallDepth : $prevUnit->getDepth();

        if ($unit->isCallFirstUnit($prevDepth)) {
            echo "\nBlock: Unit opens new call (prev [".$prevDepth."] new [".$unit->getLineView().":".$unit->getDepth()."]), create mock\n";
            $mockCallUnit = Unit::getMockForCall();
            $mockCallUnit->initCall($unit, $unit->getDepth());
            $this->_addUnitPostProcessing($mockCallUnit);
            $this->_lastCallMock = $mockCallUnit;
        } elseif ($unit->isCallClosingUnit($prevDepth)) {
            echo "\nBlock: Unit closes new call , replace mock for this unit\n";
            $this->_lastCallMock->mockReplaceWithUnit($unit);
            $this->_lastCallMock = null;
        } else {
            echo "\nBlock: Unit not opens or closes call (prev [".$prevDepth."] new [".$unit->getLineView().":".$unit->getDepth()."]), simple processing\n";
            $this->_addUnitPostProcessing($unit);
        }
    }

    /**
     * @return int
     */
    public function getDepth () {
       return $this->_depth;
    }

    /**
     * @param Unit $newUnit
     */
    private function _addUnitPostProcessing (Unit $newUnit) {
        $this->_units[] = $newUnit;
        $this->_processLinerNumber($newUnit);
        $this->_setLastUnit($newUnit);
        $this->_processIsBraced($newUnit);
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
     * @return Block
     */
    private function _getPreviousBlock () {
        $count = count($this->_previousBlocks);
        if ($count === 0) {
            return null;
        }
        return $this->_previousBlocks[--$count];
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
    public function isOpen (Unit $newUnit) {
        if ($this->_lastUnitIsCallMock()) {
            return true;
        }

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
     * @return bool
     */
    private function _lastUnitIsCallMock () {
        return $this->getLastUnit()->isMock();
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