<?php

/**
 * @author Evgeniy Udodov <flr.null@gmail.com>
 */

namespace FTrace\Utils\Code;

class Call {

    /**
     * @var Unit[]
     */
    private $_blocksList;

    /**
     * @var bool
     */
    private $_isOpen;

    /**
     * @var Unit
     */
    private $_previousUnit;

    public function __construct (Block $block) {
        $this->_blocksList = array($block);
        $this->_isOpen = true;
    }

    /**
     * @return array
     */
    public function getBlocks () {
        return $this->_blocksList;
    }

    /**
     * @return Block
     */
    public function getLastBlock () {
        $blockCount = count($this->_blocksList);
        return $this->_blocksList[--$blockCount];
    }

    /**
     * @return Block
     */
    public function getFirstBlock () {
        return $this->_blocksList[0];
    }

    /**
     * @param Block $block
     */
    public function addBlock (Block $block) {
        $this->_blocksList[] = $block;
    }

    /**
     * @param Unit $unit
     */
    public function addUnit (Unit $unit) {
        echo "\nCall: Start processing " . $unit->getLineView() . " with depth " . $unit->getDepth() . "\n";
        $lastBlock = $this->getLastBlock();
        echo "\nCall: Last block has " . count($lastBlock->getUnits()) . " units\n";
        if ($lastBlock->isOpen($unit)) {
            echo "\nCall: Last block is open ->addUnit() \n";
            $lastBlock->addUnit($unit);
        } else {
            echo "\nCall: Last block is closed new Block(unit); this->addBlock \n";
            $block = new Block($unit, $this->getBlocks());
            $this->addBlock($block);
            $this->_tryToCloseCurrentCall($unit);
        }
        $this->_previousUnit = $unit;
    }

    /**
     * @return bool
     */
    public function isOpen () {
        return $this->_isOpen;
    }

    public function close () {
        $this->_closeCurrentCall();
    }

    /**
     * @param Unit $unit
     * @param int $prevDepth
     * @return Call
     */
    public static function create (Unit $unit, $prevDepth = null) {
        echo "\nCall: create new call for " . $unit->getLineView() . ', prevDepth: ' . $prevDepth . "\n";
        $block = new Block($unit, array(), $prevDepth);
        return new Call($block);
    }

    private function _closeCurrentCall () {
        $this->_moveLastBlockToBegin();
        $this->_isOpen = false;
    }

    /**
     * @param Unit $lastAddedUnit
     */
    private function _tryToCloseCurrentCall (Unit $lastAddedUnit) {
        if (is_null($this->_previousUnit))
            return;

        $prevUnit = $this->_previousUnit;

        if ($prevUnit->getDepth() > $lastAddedUnit->getDepth()) {
            $this->_closeCurrentCall();
        }
    }

    private function _moveLastBlockToBegin () {
        $blocks = $this->getBlocks();
        $callBlock = array_pop($blocks);
        array_unshift($blocks, $callBlock);
        $this->_setBlocks($blocks);
    }

    /**
     * @param Block[] $blocks
     */
    private function _setBlocks (array $blocks) {
        $this->_blocksList = $blocks;
    }

}