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
        $block->setPreviousBlocks($this->_blocksList);
        $this->_blocksList[] = $block;
    }

    /**
     * @param Unit $unit
     */
    public function addUnit (Unit $unit) {
        $lastBlock = $this->getLastBlock();
        if ($lastBlock->blockIsOpen($unit)) {
            $lastBlock->addUnit($unit);
        } else {
            $block = new Block($unit);
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

    public static function create (Unit $unit) {
        $block = new Block($unit);
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